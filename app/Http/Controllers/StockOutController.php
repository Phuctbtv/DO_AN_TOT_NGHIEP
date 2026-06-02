<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Trip;
use App\Models\Warehouse;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockOutController extends Controller
{
    public function __construct(private TelegramService $telegram) {}

    // ============================================================
    //  INDEX – Danh sách chuyến chờ xuất kho
    // ============================================================

    public function index()
    {
        $user = auth()->user();

        // Lấy danh sách kho mà thủ kho này quản lý
        $warehouseIds = Warehouse::where('manager_id', $user->id)->pluck('id');

        $trips = Trip::with(['driver', 'warehouse', 'tripDetails.supply'])
            ->where('status', 'preparing')
            ->whereIn('warehouse_id', $warehouseIds)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('stock_outs.index', compact('trips'));
    }

    // ============================================================
    //  SHOW – Chi tiết chuyến + kiểm tra tồn kho
    // ============================================================

    public function show(Trip $trip)
    {
        $user = auth()->user();

        // Kiểm tra quyền: thủ kho phải quản lý kho của chuyến này
        abort_unless(
            Warehouse::where('id', $trip->warehouse_id)
                ->where('manager_id', $user->id)
                ->exists(),
            403,
            'Bạn không có quyền xem chuyến xe này.'
        );

        // Chỉ cho phép xem chuyến đang ở trạng thái 'preparing'
        abort_unless(
            $trip->status === 'preparing',
            403,
            'Chuyến xe này không còn ở trạng thái chờ xuất.'
        );

        $trip->load(['driver', 'warehouse', 'tripDetails.supply.category', 'creator']);

        $warehouseId = $trip->warehouse_id;

        // Tính tồn kho hiện tại theo từng supply trong kho này
        $stockIns = StockIn::where('warehouse_id', $warehouseId)
            ->selectRaw('supply_id, SUM(quantity) as total_in')
            ->groupBy('supply_id')
            ->pluck('total_in', 'supply_id');

        $stockOuts = StockOut::where('warehouse_id', $warehouseId)
            ->selectRaw('supply_id, SUM(quantity) as total_out')
            ->groupBy('supply_id')
            ->pluck('total_out', 'supply_id');

        // Gắn tồn kho + trạng thái vào từng TripDetail
        $allSufficient = true;
        $details = $trip->tripDetails->map(function ($detail) use ($stockIns, $stockOuts, &$allSufficient) {
            $supplyId  = $detail->supply_id;
            $totalIn   = $stockIns[$supplyId]  ?? 0;
            $totalOut  = $stockOuts[$supplyId] ?? 0;
            $available = max(0, $totalIn - $totalOut);
            $sufficient = $available >= $detail->quantity_loaded;

            if (!$sufficient) {
                $allSufficient = false;
            }

            $detail->available_stock = $available;
            $detail->is_sufficient   = $sufficient;

            return $detail;
        });

        return view('stock_outs.show', compact('trip', 'details', 'allSufficient'));
    }

    // ============================================================
    //  CONFIRM – Xác nhận xuất kho
    // ============================================================

    public function confirm(Request $request, Trip $trip)
    {
        $user = auth()->user();

        // Kiểm tra quyền
        abort_unless(
            Warehouse::where('id', $trip->warehouse_id)
                ->where('manager_id', $user->id)
                ->exists(),
            403
        );

        // Chỉ cho phép xuất khi trạng thái là 'preparing'
        if ($trip->status !== 'preparing') {
            return back()->with('error', '⚠️ Chuyến xe này không còn ở trạng thái chờ xuất.');
        }

        $trip->load(['tripDetails.supply', 'driver', 'warehouse']);
        $warehouseId = $trip->warehouse_id;

        // ---- Kiểm tra tồn kho lần cuối trước khi xuất ----
        $stockIns = StockIn::where('warehouse_id', $warehouseId)
            ->selectRaw('supply_id, SUM(quantity) as total_in')
            ->groupBy('supply_id')
            ->pluck('total_in', 'supply_id');

        $stockOuts = StockOut::where('warehouse_id', $warehouseId)
            ->selectRaw('supply_id, SUM(quantity) as total_out')
            ->groupBy('supply_id')
            ->pluck('total_out', 'supply_id');

        $shortages = [];
        foreach ($trip->tripDetails as $detail) {
            $supplyId  = $detail->supply_id;
            $available = max(0, ($stockIns[$supplyId] ?? 0) - ($stockOuts[$supplyId] ?? 0));
            if ($available < $detail->quantity_loaded) {
                $shortages[] = sprintf(
                    '<strong>%s</strong>: cần %d, tồn kho %d',
                    $detail->supply->name ?? "ID#{$supplyId}",
                    $detail->quantity_loaded,
                    $available
                );
            }
        }

        if (!empty($shortages)) {
            return back()->with('error',
                '❌ Không đủ tồn kho để xuất! Các mặt hàng thiếu:<br>' . implode('<br>', $shortages)
            );
        }

        // ---- Transaction: tạo stock_outs + cập nhật trip ----
        DB::beginTransaction();
        try {
            $now = now();

            foreach ($trip->tripDetails as $detail) {
                StockOut::create([
                    'warehouse_id'  => $warehouseId,
                    'supply_id'     => $detail->supply_id,
                    'quantity'      => $detail->quantity_loaded,
                    'trip_id'       => $trip->id,
                    'exported_date' => $now,
                    'created_by'    => $user->id,
                ]);
            }

            $trip->update([
                'status'      => 'shipping',
                'exported_at' => $now,
            ]);

            DB::commit();

            // Gửi Telegram thông báo đến ADMIN (TELEGRAM_ADMIN_CHAT_ID)
            try {
                $this->telegram->notifyStockOutConfirmed(
                    $trip->trip_code,
                    $trip->driver->name      ?? 'N/A',
                    $trip->warehouse->name   ?? 'N/A',
                    $user->name,
                    $trip->tripDetails->count(),
                    $now->format('H:i d/m/Y')
                );
            } catch (\Throwable $e) {
                Log::warning('[StockOut] Telegram notify admin failed: ' . $e->getMessage());
            }

            return redirect()
                ->route('warehouse.stock_outs.index')
                ->with('success', "✅ Xuất kho thành công! Chuyến <strong>{$trip->trip_code}</strong> đã chuyển sang trạng thái <strong>Đang giao</strong>.");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[StockOutController@confirm] ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xuất kho: ' . $e->getMessage());
        }
    }
}

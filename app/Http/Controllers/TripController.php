<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Household;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Trip;
use App\Models\TripDetail;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TripController extends Controller
{
    public function __construct(private TelegramService $telegram) {}

    // ============================================================
    //  INDEX – Danh sách chuyến xe
    // ============================================================

    public function index(Request $request)
    {
        $query = Trip::with(['driver', 'warehouse', 'tripDetails.supply', 'creator'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn($q) =>
                $q->where('trip_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('driver', fn($q2) => $q2->where('name', 'like', '%' . $request->search . '%'))
            )
            ->latest();

        $trips = $query->paginate(15)->withQueryString();

        $statusCounts = [
            'all'       => Trip::count(),
            'preparing' => Trip::where('status', 'preparing')->count(),
            'exporting' => Trip::where('status', 'exporting')->count(),
            'shipping'  => Trip::where('status', 'shipping')->count(),
            'completed' => Trip::where('status', 'completed')->count(),
            'cancelled' => Trip::where('status', 'cancelled')->count(),
        ];

        return view('trips.index', compact('trips', 'statusCounts'));
    }

    // ============================================================
    //  CREATE – Form tạo chuyến xe
    // ============================================================

    public function create()
    {
        $drivers    = User::where('role', 'driver')->orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        // Supplies dưới dạng array đơn giản — tránh lỗi Blade ParseError
        $suppliesJson = \App\Models\Supply::with('category')
            ->orderBy('name')->get()
            ->map(function ($s) {
                return [
                    'id'       => $s->id,
                    'name'     => $s->name,
                    'unit'     => $s->unit,
                    'category' => $s->category ? $s->category->name : '--',
                ];
            })
            ->values()->toArray();

        // Hộ dân đã duyệt (active) — một hộ có thể được giao nhiều chuyến
        $households = Household::active()
            ->with('resident')
            ->orderByRaw("CASE priority_level WHEN 1 THEN 1 WHEN 2 THEN 2 WHEN 3 THEN 3 ELSE 4 END")
            ->orderBy('household_name')
            ->get();

        return view('trips.create', compact('drivers', 'warehouses', 'suppliesJson', 'households'));
    }

    // ============================================================
    //  AJAX – Lấy danh sách tồn kho theo kho
    // ============================================================

    public function stockByWarehouse(int $warehouseId)
    {
        // Kiểm tra kho tồn tại
        $warehouse = Warehouse::findOrFail($warehouseId);

        // Tính tồn kho = tổng nhập - tổng xuất
        $stockIns = StockIn::where('warehouse_id', $warehouseId)
            ->selectRaw('supply_id, SUM(quantity) as total_in')
            ->groupBy('supply_id')
            ->pluck('total_in', 'supply_id');

        $stockOuts = StockOut::where('warehouse_id', $warehouseId)
            ->selectRaw('supply_id, SUM(quantity) as total_out')
            ->groupBy('supply_id')
            ->pluck('total_out', 'supply_id');

        $stock = $stockIns->map(function ($in, $supplyId) use ($stockOuts) {
            $out = $stockOuts[$supplyId] ?? 0;
            return max(0, $in - $out);
        })->filter(fn($qty) => $qty > 0);

        // Kèm thông tin supply
        $supplies = \App\Models\Supply::whereIn('id', $stock->keys())->with('category')->get();

        $result = $supplies->map(fn($s) => [
            'id'       => $s->id,
            'name'     => $s->name,
            'unit'     => $s->unit,
            'category' => $s->category?->name ?? '—',
            'stock'    => $stock[$s->id] ?? 0,
        ]);

        return response()->json($result->values());
    }

    // ============================================================
    //  STORE – Lưu chuyến xe mới
    // ============================================================

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id'                    => ['required', 'exists:warehouses,id'],
            'driver_id'                       => ['required', 'exists:users,id'],
            'vehicle_info'                    => ['required', 'string', 'max:100'],
            'notes'                           => ['nullable', 'string', 'max:500'],
            'items'                           => ['required', 'array', 'min:1'],
            'items.*.supply_id'               => ['required', 'exists:supplies,id'],
            'items.*.quantity_loaded'         => ['required', 'integer', 'min:1'],
            'household_ids'                   => ['required', 'array', 'min:1'],
            'household_ids.*'                 => ['required', 'exists:households,id'],
        ], [
            'warehouse_id.required'           => 'Vui lòng chọn kho xuất.',
            'driver_id.required'              => 'Vui lòng chọn tài xế.',
            'vehicle_info.required'           => 'Vui lòng nhập thông tin phương tiện.',
            'items.required'                  => 'Vui lòng thêm ít nhất 1 mặt hàng.',
            'items.min'                       => 'Vui lòng thêm ít nhất 1 mặt hàng.',
            'items.*.supply_id.required'      => 'Vui lòng chọn mặt hàng.',
            'items.*.quantity_loaded.required' => 'Vui lòng nhập số lượng.',
            'items.*.quantity_loaded.min'     => 'Số lượng phải ít nhất là 1.',
            'household_ids.required'          => 'Vui lòng chọn ít nhất 1 hộ dân nhận hàng.',
            'household_ids.min'              => 'Vui lòng chọn ít nhất 1 hộ dân nhận hàng.',
        ]);

        DB::beginTransaction();
        try {
            // Sinh mã chuyến: TP-{year}{month}-{ID 4 chữ số}
            $now   = now();
            $tmpId = Trip::max('id') + 1;
            $code  = 'TP-' . $now->format('Ym') . '-' . str_pad($tmpId, 4, '0', STR_PAD_LEFT);

            $trip = Trip::create([
                'trip_code'    => $code,
                'driver_id'    => $request->driver_id,
                'warehouse_id' => $request->warehouse_id,
                'vehicle_info' => $request->vehicle_info,
                'notes'        => $request->notes,
                'status'       => 'preparing',
                'created_by'   => auth()->id(),
            ]);

            // Cập nhật code thực tế với id thật
            $trip->update([
                'trip_code' => 'TP-' . $now->format('Ym') . '-' . str_pad($trip->id, 4, '0', STR_PAD_LEFT),
            ]);

            // Lưu trip_details (hàng hoá)
            foreach ($request->items as $item) {
                TripDetail::create([
                    'trip_id'            => $trip->id,
                    'supply_id'          => $item['supply_id'],
                    'quantity_loaded'    => $item['quantity_loaded'],
                    'quantity_delivered' => 0,
                ]);
            }

            // Lưu deliveries (hộ dân nhận hàng)
            $households = Household::whereIn('id', $request->household_ids)->get()->keyBy('id');
            foreach ($request->household_ids as $householdId) {
                $hh = $households[$householdId] ?? null;
                if (!$hh) continue;

                // Sinh delivery_code sau khi biết delivery ID
                $delivery = Delivery::create([
                    'delivery_code'  => 'TEMP', // sẽ cập nhật sau
                    'trip_id'        => $trip->id,
                    'household_id'   => $householdId,
                    'recipient_name' => $hh->household_name,
                    'recipient_cccd' => $hh->resident?->identity_card ?? '',
                    'status'         => 'pending',
                    'sync_status'    => 'pending',
                ]);

                // GIAO-{YYYYMMDD}-{id}
                $delivery->update([
                    'delivery_code' => 'GIAO-' . $now->format('Ymd') . '-' . str_pad($delivery->id, 4, '0', STR_PAD_LEFT),
                ]);
            }

            DB::commit();

            // Tải lại để lấy quan hệ
            $trip->load(['driver', 'warehouse', 'tripDetails', 'deliveries']);

            // Gửi Telegram vào NHÓM
            $this->telegram->notifyTripAssigned(
                $trip->trip_code,
                $trip->driver->name,
                $trip->warehouse->name,
                $trip->vehicle_info,
                $trip->tripDetails->count()
            );

            return redirect()->route('admin.trips.show', $trip)
                ->with('success', "Dã tạo chuyến xe <strong>{$trip->trip_code}</strong> và phân công {$trip->deliveries->count()} hộ dân!");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[TripController@store] ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo chuyến xe: ' . $e->getMessage());
        }
    }

    // ============================================================
    //  SHOW – Chi tiết chuyến xe
    // ============================================================

    public function show(Trip $trip)
    {
        $trip->load([
            'driver', 'warehouse', 'tripDetails.supply.category',
            'creator', 'deliveries.household.resident'
        ]);
        return view('trips.show', compact('trip'));
    }

    // ============================================================
    //  UPDATE STATUS – Cập nhật trạng thái
    // ============================================================

    public function updateStatus(Request $request, Trip $trip)
    {
        $allowed = ['preparing', 'exporting', 'shipping', 'completed', 'cancelled'];
        $request->validate([
            'status' => ['required', 'in:' . implode(',', $allowed)],
        ]);

        $oldStatus = $trip->status;
        $extra = [];

        if ($request->status === 'exporting')  $extra['exported_at']  = now();
        if ($request->status === 'shipping')   $extra['started_at']   = now();
        if ($request->status === 'completed')  $extra['completed_at'] = now();

        $trip->update(array_merge(['status' => $request->status], $extra));
        $trip->load('driver');

        // Gửi Telegram vào NHÓM
        $this->telegram->notifyTripStatusChanged(
            $trip->trip_code,
            $trip->driver->name,
            $request->status
        );

        return back()->with('success', 'Đã cập nhật trạng thái chuyến xe!');
    }

    // ============================================================
    //  DESTROY – Huỷ/Xoá chuyến xe
    // ============================================================

    public function destroy(Trip $trip)
    {
        if (!in_array($trip->status, ['preparing', 'cancelled'])) {
            return back()->with('error', 'Chỉ có thể xoá chuyến xe đang ở trạng thái Chuẩn bị hoặc Đã huỷ.');
        }
        $code = $trip->trip_code;
        $trip->delete();
        return redirect()->route('admin.trips.index')
            ->with('success', "Đã xoá chuyến xe <strong>{$code}</strong>.");
    }
}

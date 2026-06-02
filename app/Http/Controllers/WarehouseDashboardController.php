<?php

namespace App\Http\Controllers;

use App\Exports\StockReportExport;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supply;
use App\Models\Warehouse;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;


class WarehouseDashboardController extends Controller
{
    // ============================================================
    //  Helper: Lấy danh sách kho + kho được chọn
    // ============================================================

    private function resolveWarehouse(Request $request): array
    {
        $user       = auth()->user();
        $warehouses = Warehouse::where('manager_id', $user->id)->orderBy('name')->get();

        $selectedId = $request->filled('warehouse_id')
            ? (int) $request->warehouse_id
            : ($warehouses->first()?->id ?? null);

        $warehouse = $warehouses->firstWhere('id', $selectedId) ?? $warehouses->first();

        return [$warehouses, $warehouse];
    }

    // ============================================================
    //  Helper: Tính tồn kho toàn bộ supplies theo warehouse
    // ============================================================

    private function computeInventory(int $warehouseId): \Illuminate\Support\Collection
    {
        $stockIns = StockIn::where('warehouse_id', $warehouseId)
            ->selectRaw('supply_id, SUM(quantity) as total_in')
            ->groupBy('supply_id')
            ->pluck('total_in', 'supply_id');

        $stockOuts = StockOut::where('warehouse_id', $warehouseId)
            ->selectRaw('supply_id, SUM(quantity) as total_out')
            ->groupBy('supply_id')
            ->pluck('total_out', 'supply_id');

        $supplyIds = $stockIns->keys()->merge($stockOuts->keys())->unique();
        $supplies  = Supply::with('category')->whereIn('id', $supplyIds)->orderBy('name')->get();

        return $supplies->map(function ($supply) use ($stockIns, $stockOuts) {
            $totalIn  = $stockIns[$supply->id]  ?? 0;
            $totalOut = $stockOuts[$supply->id] ?? 0;
            $stock    = max(0, $totalIn - $totalOut);
            $minAlert = $supply->min_stock_alert ?? 0;

            $status = 'ok';
            if ($stock === 0) {
                $status = 'empty';
            } elseif ($minAlert > 0 && $stock < $minAlert) {
                $status = 'low';
            }

            return (object) [
                'supply'    => $supply,
                'total_in'  => $totalIn,
                'total_out' => $totalOut,
                'stock'     => $stock,
                'min_alert' => $minAlert,
                'status'    => $status,
            ];
        });
    }

    // ============================================================
    //  1. TỔNG QUAN
    // ============================================================

    public function overview(Request $request)
    {
        [$warehouses, $warehouse] = $this->resolveWarehouse($request);

        if (!$warehouse) {
            return view('warehouse.overview', [
                'warehouses' => $warehouses,
                'warehouse'  => null,
                'stats'      => [],
                'recentIns'  => collect(),
                'recentOuts' => collect(),
            ]);
        }

        $warehouseId = $warehouse->id;
        $inventory   = $this->computeInventory($warehouseId);

        // Tháng hiện tại
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        $totalInMonth = StockIn::where('warehouse_id', $warehouseId)
            ->whereBetween('received_date', [$monthStart, $monthEnd])
            ->sum('quantity');

        $totalOutMonth = StockOut::where('warehouse_id', $warehouseId)
            ->whereBetween('exported_date', [$monthStart, $monthEnd])
            ->sum('quantity');

        $stats = [
            'total_types'     => $inventory->count(),
            'total_stock'     => $inventory->sum('stock'),
            'total_in_month'  => $totalInMonth,
            'total_out_month' => $totalOutMonth,
            'low_count'       => $inventory->where('status', 'low')->count(),
            'empty_count'     => $inventory->where('status', 'empty')->count(),
        ];

        // Nhập/xuất gần đây (5 bản ghi)
        $recentIns = StockIn::with(['supply', 'creator'])
            ->where('warehouse_id', $warehouseId)
            ->latest('received_date')
            ->limit(5)
            ->get();

        $recentOuts = StockOut::with(['supply', 'trip'])
            ->where('warehouse_id', $warehouseId)
            ->latest('exported_date')
            ->limit(5)
            ->get();

        return view('warehouse.overview', compact(
            'warehouses', 'warehouse', 'stats', 'recentIns', 'recentOuts', 'inventory'
        ));
    }

    // ============================================================
    //  2. THỐNG KÊ (biểu đồ Chart.js)
    // ============================================================

    public function statistics(Request $request)
    {
        [$warehouses, $warehouse] = $this->resolveWarehouse($request);

        $period = $request->get('period', 'week'); // 'week' | 'month'

        if (!$warehouse) {
            return view('warehouse.statistics', [
                'warehouses'    => $warehouses,
                'warehouse'     => null,
                'period'        => $period,
                'chartLabels'   => [],
                'chartDataIn'   => [],
                'chartDataOut'  => [],
                'totalIn'       => 0,
                'totalOut'      => 0,
            ]);
        }

        $warehouseId = $warehouse->id;

        if ($period === 'month') {
            // 30 ngày gần nhất
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $groupFormat = '%Y-%m-%d';
            $labelFormat = 'd/m';
            $days = 30;
        } else {
            // 7 ngày gần nhất
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $groupFormat = '%Y-%m-%d';
            $labelFormat = 'd/m';
            $days = 7;
        }

        $endDate = Carbon::now()->endOfDay();

        // Tạo mảng ngày
        $dateRange = [];
        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($days - 1 - $i)->format('Y-m-d');
            $dateRange[$date] = 0;
        }

        // Nhập theo ngày
        $insRaw = StockIn::where('warehouse_id', $warehouseId)
            ->whereBetween('received_date', [$startDate, $endDate])
            ->selectRaw("DATE(received_date) as day, SUM(quantity) as total")
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        // Xuất theo ngày
        $outsRaw = StockOut::where('warehouse_id', $warehouseId)
            ->whereBetween('exported_date', [$startDate, $endDate])
            ->selectRaw("DATE(exported_date) as day, SUM(quantity) as total")
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $chartLabels  = [];
        $chartDataIn  = [];
        $chartDataOut = [];

        foreach ($dateRange as $date => $zero) {
            $label = Carbon::parse($date)->format($labelFormat);
            $chartLabels[]  = $label;
            $chartDataIn[]  = (int) ($insRaw[$date]  ?? 0);
            $chartDataOut[] = (int) ($outsRaw[$date] ?? 0);
        }

        $totalIn  = array_sum($chartDataIn);
        $totalOut = array_sum($chartDataOut);

        return view('warehouse.statistics', compact(
            'warehouses', 'warehouse', 'period',
            'chartLabels', 'chartDataIn', 'chartDataOut',
            'totalIn', 'totalOut'
        ));
    }

    // ============================================================
    //  3. CẢNH BÁO TỒN KHO
    // ============================================================

    public function alerts(Request $request)
    {
        [$warehouses, $warehouse] = $this->resolveWarehouse($request);

        if (!$warehouse) {
            return view('warehouse.alerts', [
                'warehouses' => $warehouses,
                'warehouse'  => null,
                'alertRows'  => collect(),
            ]);
        }

        $warehouseId = $warehouse->id;
        $inventory   = $this->computeInventory($warehouseId);

        // Chỉ lấy những mặt hàng cần cảnh báo (low hoặc empty)
        $alertRows = $inventory
            ->filter(fn($row) => $row->status === 'low' || $row->status === 'empty')
            ->sortBy(fn($row) => $row->status === 'empty' ? 0 : 1) // Hết hàng lên trên
            ->values();

        return view('warehouse.alerts', compact('warehouses', 'warehouse', 'alertRows'));
    }

    // ============================================================
    //  4. GỬI YÊU CẦU BỔ SUNG → Telegram Admin
    // ============================================================

    public function sendStockRequest(Request $request)
    {
        $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
        ]);

        $user = auth()->user();
        $warehouse = Warehouse::where('id', $request->warehouse_id)
            ->where('manager_id', $user->id)
            ->firstOrFail();

        $inventory = $this->computeInventory($warehouse->id);
        $alertRows = $inventory->filter(fn($row) => $row->status === 'low' || $row->status === 'empty');

        if ($alertRows->isEmpty()) {
            return back()->with('info', 'ℹ️ Không có mặt hàng nào cần bổ sung hiện tại.');
        }

        $emptyCount = $alertRows->where('status', 'empty')->count();
        $lowCount   = $alertRows->where('status', 'low')->count();

        // Tạo danh sách tóm tắt
        $itemLines = $alertRows->map(function ($row) {
            $icon   = $row->status === 'empty' ? '🔴' : '🟠';
            $label  = $row->status === 'empty' ? 'Hết hàng' : 'Sắp hết';
            return "{$icon} {$row->supply->name} ({$row->stock} {$row->supply->unit}) – {$label}";
        })->join("\n");

        try {
            $telegram = app(TelegramService::class);
            $telegram->notifyStockAlert(
                $warehouse->name,
                $user->name,
                $emptyCount,
                $lowCount,
                $itemLines
            );

            return back()->with('success', "✅ Đã gửi yêu cầu bổ sung tới Admin qua Telegram! ({$alertRows->count()} mặt hàng)");
        } catch (\Throwable $e) {
            Log::error('[WarehouseDashboard@sendStockRequest] ' . $e->getMessage());
            return back()->with('error', 'Không thể gửi thông báo. Vui lòng thử lại sau.');
        }
    }

    // ============================================================
    //  5. XUẤT EXCEL – Báo cáo nhập xuất kho
    // ============================================================

    public function exportStockReport(Request $request)
    {
        $request->validate([
            'month'        => ['required', 'integer', 'between:1,12'],
            'year'         => ['required', 'integer', 'min:2020', 'max:2099'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
        ]);

        $user       = auth()->user();
        $warehouses = Warehouse::where('manager_id', $user->id)->get();

        // Xác định kho cần báo cáo
        if ($request->filled('warehouse_id')) {
            $selectedWarehouse = $warehouses->firstWhere('id', (int) $request->warehouse_id);
            abort_unless($selectedWarehouse, 403, 'Bạn không có quyền xem kho này.');
            $warehouseIds  = [$selectedWarehouse->id];
            $warehouseName = $selectedWarehouse->name;
        } else {
            $warehouseIds  = $warehouses->pluck('id')->toArray();
            $warehouseName = $warehouses->count() === 1
                ? $warehouses->first()->name
                : 'Tất cả kho (' . $warehouses->pluck('name')->join(', ') . ')';
        }

        $month = (int) $request->month;
        $year  = (int) $request->year;

        $fileName = 'BaoCao_NhapXuat_T' . str_pad($month, 2, '0', STR_PAD_LEFT) . $year
            . '_' . now()->format('His') . '.xlsx';

        return Excel::download(
            new StockReportExport($month, $year, $warehouseIds, $warehouseName),
            $fileName
        );
    }
}


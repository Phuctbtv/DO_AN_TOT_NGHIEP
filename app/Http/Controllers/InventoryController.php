<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supply;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Lấy danh sách kho của thủ kho này
        $warehouses = Warehouse::where('manager_id', $user->id)->orderBy('name')->get();

        if ($warehouses->isEmpty()) {
            return view('inventory.index', [
                'warehouses'    => $warehouses,
                'warehouse'     => null,
                'inventoryRows' => collect(),
                'categories'    => collect(),
                'summary'       => ['total' => 0, 'ok' => 0, 'low' => 0, 'empty' => 0],
            ]);
        }

        // Xác định kho được chọn (mặc định kho đầu tiên)
        $selectedWarehouseId = $request->filled('warehouse_id')
            ? (int) $request->warehouse_id
            : $warehouses->first()->id;

        // Đảm bảo kho đó thuộc quyền quản lý
        $warehouse = $warehouses->firstWhere('id', $selectedWarehouseId)
                  ?? $warehouses->first();

        $warehouseId = $warehouse->id;

        // ── Tính tồn kho theo từng supply ──────────────────────────
        $stockIns = StockIn::where('warehouse_id', $warehouseId)
            ->selectRaw('supply_id, SUM(quantity) as total_in')
            ->groupBy('supply_id')
            ->pluck('total_in', 'supply_id');

        $stockOuts = StockOut::where('warehouse_id', $warehouseId)
            ->selectRaw('supply_id, SUM(quantity) as total_out')
            ->groupBy('supply_id')
            ->pluck('total_out', 'supply_id');

        // Lấy tất cả supplies có tồn kho > 0 hoặc đã từng nhập vào kho này
        $supplyIds = $stockIns->keys()->merge($stockOuts->keys())->unique();

        $supplies = Supply::with('category')
            ->whereIn('id', $supplyIds)
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->orderBy('name')
            ->get();

        // ── Tính tồn và xác định trạng thái ───────────────────────
        $inventoryRows = $supplies->map(function ($supply) use ($stockIns, $stockOuts) {
            $totalIn  = $stockIns[$supply->id]  ?? 0;
            $totalOut = $stockOuts[$supply->id] ?? 0;
            $stock    = max(0, $totalIn - $totalOut);
            $minAlert = $supply->min_stock_alert ?? 0;

            if ($stock === 0) {
                $status = 'empty';
            } elseif ($minAlert > 0 && $stock < $minAlert) {
                $status = 'low';
            } else {
                $status = 'ok';
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

        // ── Lọc theo trạng thái ────────────────────────────────────
        if ($request->filled('status')) {
            $inventoryRows = $inventoryRows->where('status', $request->status)->values();
        }

        // ── Summary ─────────────────────────────────────────────────
        $summary = [
            'total' => $inventoryRows->count(),
            'ok'    => $inventoryRows->where('status', 'ok')->count(),
            'low'   => $inventoryRows->where('status', 'low')->count(),
            'empty' => $inventoryRows->where('status', 'empty')->count(),
        ];

        $categories = Category::orderBy('name')->get();

        return view('inventory.index', compact(
            'warehouses', 'warehouse', 'inventoryRows', 'categories', 'summary'
        ));
    }
}

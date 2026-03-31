<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseRequest;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Danh sách kho (có tìm kiếm & phân trang).
     */
    public function index(Request $request)
    {
        $query = Warehouse::with('manager')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name');

        $warehouses = $query->paginate(15)->withQueryString();

        return view('warehouses.index', compact('warehouses'));
    }

    /**
     * Form tạo kho mới.
     */
    public function create()
    {
        $managers = User::where('role', 'warehouse_manager')->orderBy('name')->get();
        return view('warehouses.create', compact('managers'));
    }

    /**
     * Lưu kho mới.
     */
    public function store(WarehouseRequest $request)
    {
        Warehouse::create($request->validated());

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Đã thêm kho thành công!');
    }

    /**
     * Form chỉnh sửa kho.
     */
    public function edit(Warehouse $warehouse)
    {
        $managers = User::where('role', 'warehouse_manager')->orderBy('name')->get();
        return view('warehouses.edit', compact('warehouse', 'managers'));
    }

    /**
     * Cập nhật kho.
     */
    public function update(WarehouseRequest $request, Warehouse $warehouse)
    {
        $warehouse->update($request->validated());

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Đã cập nhật kho thành công!');
    }

    /**
     * Xoá kho.
     */
    public function destroy(Warehouse $warehouse)
    {
        try {
            $warehouse->delete();
            return redirect()->route('admin.warehouses.index')
                ->with('success', 'Đã xoá kho thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.warehouses.index')
                ->with('error', 'Không thể xoá: kho đang được sử dụng trong các bản ghi khác.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplyRequest;
use App\Models\Category;
use App\Models\Supply;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    /**
     * Danh sách nhu yếu phẩm (có tìm kiếm & phân trang).
     */
    public function index(Request $request)
    {
        $query = Supply::with('category')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name');

        $supplies = $query->paginate(15)->withQueryString();

        return view('supplies.index', compact('supplies'));
    }

    /**
     * Form tạo mới.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('supplies.create', compact('categories'));
    }

    /**
     * Lưu nhu yếu phẩm mới.
     */
    public function store(SupplyRequest $request)
    {
        Supply::create($request->validated());

        return redirect()->route('admin.supplies.index')
            ->with('success', 'Đã thêm nhu yếu phẩm thành công!');
    }

    /**
     * Form chỉnh sửa.
     */
    public function edit(Supply $supply)
    {
        $categories = Category::orderBy('name')->get();
        return view('supplies.edit', compact('supply', 'categories'));
    }

    /**
     * Cập nhật nhu yếu phẩm.
     */
    public function update(SupplyRequest $request, Supply $supply)
    {
        $supply->update($request->validated());

        return redirect()->route('admin.supplies.index')
            ->with('success', 'Đã cập nhật nhu yếu phẩm thành công!');
    }

    /**
     * Xoá nhu yếu phẩm.
     */
    public function destroy(Supply $supply)
    {
        try {
            $supply->delete();
            return redirect()->route('admin.supplies.index')
                ->with('success', 'Đã xoá nhu yếu phẩm thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.supplies.index')
                ->with('error', 'Không thể xoá: nhu yếu phẩm đang được sử dụng trong các bản ghi khác.');
        }
    }
}

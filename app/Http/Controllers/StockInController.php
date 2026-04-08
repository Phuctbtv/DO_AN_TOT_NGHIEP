<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\Supply;
use App\Models\Warehouse;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockInController extends Controller
{
    public function __construct(private CloudinaryService $cloudinary) {}

    // ============================================================
    //  INDEX – Danh sách lịch sử nhập kho
    // ============================================================

    public function index(Request $request)
    {
        $user = auth()->user();

        // Thủ kho chỉ thấy kho của mình
        $warehouseIds = Warehouse::where('manager_id', $user->id)->pluck('id');

        $query = StockIn::with(['warehouse', 'supply', 'creator'])
            ->whereIn('warehouse_id', $warehouseIds)
            ->when($request->filled('warehouse_id'), fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->filled('search'), fn($q) =>
                $q->whereHas('supply', fn($q2) =>
                    $q2->where('name', 'like', '%' . $request->search . '%')
                )
            )
            ->latest('received_date');

        $stockIns   = $query->paginate(15)->withQueryString();
        $warehouses = Warehouse::where('manager_id', $user->id)->orderBy('name')->get();

        return view('stock_ins.index', compact('stockIns', 'warehouses'));
    }

    // ============================================================
    //  CREATE – Form nhập kho
    // ============================================================

    public function create()
    {
        $user       = auth()->user();
        $warehouses = Warehouse::where('manager_id', $user->id)->orderBy('name')->get();
        $supplies   = Supply::orderBy('name')->get();

        // Nếu chỉ quản lý 1 kho → auto-fill, không cần dropdown
        $myWarehouse = $warehouses->count() === 1 ? $warehouses->first() : null;

        return view('stock_ins.create', compact('warehouses', 'supplies', 'myWarehouse'));
    }

    // ============================================================
    //  STORE – Lưu phiếu nhập kho
    // ============================================================

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id'  => ['required', 'exists:warehouses,id'],
            'supply_id'     => ['required', 'exists:supplies,id'],
            'quantity'      => ['required', 'integer', 'min:1'],
            'donor_info'    => ['nullable', 'string', 'max:255'],
            'received_date' => ['required', 'date'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'warehouse_id.required'  => 'Vui lòng chọn kho.',
            'supply_id.required'     => 'Vui lòng chọn nhu yếu phẩm.',
            'quantity.required'      => 'Vui lòng nhập số lượng.',
            'quantity.min'           => 'Số lượng phải lớn hơn 0.',
            'received_date.required' => 'Vui lòng chọn ngày nhập.',
            'image.image'            => 'File phải là ảnh (jpg, jpeg, png, webp).',
            'image.max'              => 'Ảnh không được vượt quá 5MB.',
        ]);

        // Kiểm tra thủ kho có quản lý kho này không
        $warehouse = Warehouse::where('id', $request->warehouse_id)
            ->where('manager_id', auth()->id())
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Upload ảnh lên Cloudinary nếu có
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $imageUrl = $this->cloudinary->upload($request->file('image'), 'daiphuc/stock_ins');
                if (!$imageUrl) {
                    // Nếu upload thất bại, vẫn tiếp tục nhưng không có ảnh
                    Log::warning('[StockIn] Upload ảnh thất bại, tiếp tục lưu không có ảnh.');
                }
            }

            // Tạo phiếu nhập kho
            $stockIn = StockIn::create([
                'warehouse_id'  => $request->warehouse_id,
                'supply_id'     => $request->supply_id,
                'quantity'      => $request->quantity,
                'donor_info'    => $request->donor_info,
                'image_url'     => $imageUrl,
                'received_date' => $request->received_date,
                'created_by'    => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('warehouse.stock_ins.index')
                ->with('success', "✅ Đã lưu phiếu nhập kho thành công! Mặt hàng: <strong>{$stockIn->supply->name}</strong>, Số lượng: <strong>{$stockIn->quantity}</strong>.");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[StockInController@store] ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi lưu phiếu nhập: ' . $e->getMessage());
        }
    }

    // ============================================================
    //  SHOW – Chi tiết phiếu nhập kho
    // ============================================================

    public function show(StockIn $stockIn)
    {
        // Kiểm tra thủ kho có quyền xem không
        abort_unless(
            Warehouse::where('id', $stockIn->warehouse_id)
                ->where('manager_id', auth()->id())
                ->exists(),
            403
        );

        $stockIn->load(['warehouse', 'supply.category', 'creator']);
        return view('stock_ins.show', compact('stockIn'));
    }

    // ============================================================
    //  DESTROY – Xóa phiếu nhập kho
    // ============================================================

    public function destroy(StockIn $stockIn)
    {
        // Kiểm tra quyền
        abort_unless(
            Warehouse::where('id', $stockIn->warehouse_id)
                ->where('manager_id', auth()->id())
                ->exists(),
            403
        );

        try {
            $supply   = $stockIn->supply->name ?? 'N/A';
            $quantity = $stockIn->quantity;
            $stockIn->delete();

            return redirect()
                ->route('warehouse.stock_ins.index')
                ->with('success', "🗑️ Đã xóa phiếu nhập: <strong>{$supply}</strong> ({$quantity}).");
        } catch (\Throwable $e) {
            Log::error('[StockInController@destroy] ' . $e->getMessage());
            return back()->with('error', 'Không thể xóa phiếu nhập: ' . $e->getMessage());
        }
    }
}

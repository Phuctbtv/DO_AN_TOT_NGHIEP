<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Danh sách user (có tìm kiếm & lọc theo role, phân trang 15).
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->when($request->filled('search'), fn ($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
            )
            ->when($request->filled('role'), fn ($q) =>
                $q->where('role', $request->role)
            )
            ->whereIn('role', ['warehouse_manager', 'driver', 'resident'])
            ->orderBy('role')
            ->orderBy('name');

        $users = $query->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Form tạo user mới.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Lưu user mới.
     */
    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Đã thêm tài khoản thành công!');
    }

    /**
     * Form chỉnh sửa user.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Cập nhật user.
     */
    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        // Chỉ cập nhật password nếu có điền
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Đã cập nhật tài khoản thành công!');
    }

    /**
     * Xoá user.
     */
    public function destroy(User $user)
    {
        // Không cho xoá chính mình
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể xoá tài khoản của chính bạn!');
        }

        // Không cho xoá admin
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể xoá tài khoản Admin!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Đã xoá tài khoản thành công!');
    }
}

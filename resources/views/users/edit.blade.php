@extends('layouts.app')
@section('title', 'Chỉnh sửa tài khoản - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  @include('partials.admin-sidebar', ['activeMenu' => request('from') === 'driver' ? 'drivers' : 'users'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => request('from') === 'driver' ? '✏️ Chỉnh sửa Tài xế' : '✏️ Chỉnh sửa tài khoản'])
    <div style="padding:1.5rem">

      <div style="max-width:640px">

        {{-- VALIDATION ERRORS --}}
        @if($errors->any())
          <div style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;border-radius:8px;padding:.85rem 1.1rem;margin-bottom:1.25rem;font-size:.88rem">
            <strong>⚠️ Vui lòng kiểm tra lại:</strong>
            <ul style="margin:.4rem 0 0 1.2rem;line-height:1.7">
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="table-wrap" style="padding:1.75rem">
          <form action="{{ route('admin.users.update', $user) }}?from={{ request('from') }}" method="POST" autocomplete="off">
            @csrf
            @method('PATCH')

            {{-- Tên --}}
            <div style="margin-bottom:1.1rem">
              <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.35rem">
                Họ và tên <span style="color:#dc2626">*</span>
              </label>
              <input type="text" name="name" value="{{ old('name', $user->name) }}"
                style="width:100%;padding:.6rem .9rem;border:1px solid {{ $errors->has('name') ? '#fca5a5' : '#d1d5db' }};border-radius:8px;font-size:.9rem;box-sizing:border-box">
            </div>

            {{-- Email --}}
            <div style="margin-bottom:1.1rem">
              <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.35rem">
                Email <span style="color:#dc2626">*</span>
              </label>
              <input type="email" name="email" value="{{ old('email', $user->email) }}"
                style="width:100%;padding:.6rem .9rem;border:1px solid {{ $errors->has('email') ? '#fca5a5' : '#d1d5db' }};border-radius:8px;font-size:.9rem;box-sizing:border-box">
            </div>

            {{-- Mật khẩu (tuỳ chọn) --}}
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:1rem;margin-bottom:1.1rem">
              <p style="font-size:.82rem;color:#6b7280;margin:0 0 .75rem 0">
                🔒 Để trống nếu không muốn thay đổi mật khẩu.
              </p>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div>
                  <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.35rem">
                    Mật khẩu mới
                  </label>
                  <input type="password" name="password"
                    placeholder="Tối thiểu 8 ký tự"
                    style="width:100%;padding:.6rem .9rem;border:1px solid {{ $errors->has('password') ? '#fca5a5' : '#d1d5db' }};border-radius:8px;font-size:.9rem;box-sizing:border-box">
                </div>
                <div>
                  <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.35rem">
                    Xác nhận mật khẩu
                  </label>
                  <input type="password" name="password_confirmation"
                    placeholder="Nhập lại mật khẩu"
                    style="width:100%;padding:.6rem .9rem;border:1px solid #d1d5db;border-radius:8px;font-size:.9rem;box-sizing:border-box">
                </div>
              </div>
            </div>

            {{-- Số điện thoại --}}
            <div style="margin-bottom:1.1rem">
              <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.35rem">
                Số điện thoại
              </label>
              <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                style="width:100%;padding:.6rem .9rem;border:1px solid #d1d5db;border-radius:8px;font-size:.9rem;box-sizing:border-box">
            </div>

            {{-- Vai trò --}}
            <div style="margin-bottom:1.75rem">
              <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.35rem">
                Vai trò <span style="color:#dc2626">*</span>
              </label>
              <select name="role"
                style="width:100%;padding:.6rem .9rem;border:1px solid {{ $errors->has('role') ? '#fca5a5' : '#d1d5db' }};border-radius:8px;font-size:.9rem;background:#fff;color:#374151;box-sizing:border-box">
                <option value="warehouse_manager" @selected(old('role', $user->role) === 'warehouse_manager')>🏭 Thủ kho</option>
                <option value="driver"            @selected(old('role', $user->role) === 'driver')>🚛 Tài xế</option>
                <option value="resident"          @selected(old('role', $user->role) === 'resident')>🏠 Người dân</option>
              </select>
            </div>

            {{-- ACTIONS --}}
            <div style="display:flex;gap:.75rem">
              <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
              @if(request('from') === 'driver')
                <a href="{{ route('admin.users.index', ['role' => 'driver']) }}" class="btn btn-outline">← Quay lại Danh sách Tài xế</a>
              @else
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">← Quay lại</a>
              @endif
            </div>

          </form>
        </div>
      </div>

    </div>
  </main>
</div>
@endsection

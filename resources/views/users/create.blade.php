@extends('layouts.app')
@section('title', 'Thêm tài khoản - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  @include('partials.admin-sidebar', ['activeMenu' => request('from') === 'driver' ? 'drivers' : 'users'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => request('from') === 'driver' ? '➕ Thêm Tài xế mới' : '➕ Thêm tài khoản mới'])
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
          <form action="{{ route('admin.users.store') }}?from={{ request('from') }}" method="POST" autocomplete="off">
            @csrf

            {{-- Tên --}}
            <div style="margin-bottom:1.1rem">
              <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.35rem">
                Họ và tên <span style="color:#dc2626">*</span>
              </label>
              <input type="text" name="name" value="{{ old('name') }}"
                placeholder="Nguyễn Văn A"
                style="width:100%;padding:.6rem .9rem;border:1px solid {{ $errors->has('name') ? '#fca5a5' : '#d1d5db' }};border-radius:8px;font-size:.9rem;box-sizing:border-box">
            </div>

            {{-- Email --}}
            <div style="margin-bottom:1.1rem">
              <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.35rem">
                Email <span style="color:#dc2626">*</span>
              </label>
              <input type="email" name="email" value="{{ old('email') }}"
                placeholder="email@example.com"
                style="width:100%;padding:.6rem .9rem;border:1px solid {{ $errors->has('email') ? '#fca5a5' : '#d1d5db' }};border-radius:8px;font-size:.9rem;box-sizing:border-box">
            </div>

            {{-- Mật khẩu --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.1rem">
              <div>
                <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.35rem">
                  Mật khẩu <span style="color:#dc2626">*</span>
                </label>
                <input type="password" name="password"
                  placeholder="Tối thiểu 8 ký tự"
                  style="width:100%;padding:.6rem .9rem;border:1px solid {{ $errors->has('password') ? '#fca5a5' : '#d1d5db' }};border-radius:8px;font-size:.9rem;box-sizing:border-box">
              </div>
              <div>
                <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.35rem">
                  Xác nhận mật khẩu <span style="color:#dc2626">*</span>
                </label>
                <input type="password" name="password_confirmation"
                  placeholder="Nhập lại mật khẩu"
                  style="width:100%;padding:.6rem .9rem;border:1px solid #d1d5db;border-radius:8px;font-size:.9rem;box-sizing:border-box">
              </div>
            </div>

            {{-- Số điện thoại --}}
            <div style="margin-bottom:1.1rem">
              <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.35rem">
                Số điện thoại
              </label>
              <input type="tel" name="phone" value="{{ old('phone') }}"
                placeholder="0901234567"
                style="width:100%;padding:.6rem .9rem;border:1px solid #d1d5db;border-radius:8px;font-size:.9rem;box-sizing:border-box">
            </div>

            {{-- Vai trò --}}
            <div style="margin-bottom:1.75rem">
              <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.35rem">
                Vai trò <span style="color:#dc2626">*</span>
              </label>
              <select name="role"
                style="width:100%;padding:.6rem .9rem;border:1px solid {{ $errors->has('role') ? '#fca5a5' : '#d1d5db' }};border-radius:8px;font-size:.9rem;background:#fff;color:#374151;box-sizing:border-box">
                <option value="">-- Chọn vai trò --</option>
                <option value="warehouse_manager" @selected(old('role', request('from') === 'driver' ? '' : '') === 'warehouse_manager')>🏭 Thủ kho</option>
                <option value="driver"            @selected(old('role', request('from') === 'driver' ? 'driver' : '') === 'driver')>🚛 Tài xế</option>
                <option value="resident"          @selected(old('role') === 'resident')>🏠 Người dân</option>
              </select>
            </div>

            {{-- ACTIONS --}}
            <div style="display:flex;gap:.75rem">
              <button type="submit" class="btn btn-primary">💾 Tạo tài khoản</button>
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

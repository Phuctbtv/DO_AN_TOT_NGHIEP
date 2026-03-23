@extends('layouts.app')

@section('title', 'ĐẠI PHÚC - Hồ sơ cá nhân')

@section('content')

{{-- ==================== NAVBAR ==================== --}}
<nav class="navbar">
  <div class="container navbar-inner">
    <a href="/" class="navbar-logo">🌊 ĐẠI <span>PHÚC</span></a>
    <div class="navbar-menu">
      <a href="/">Trang chủ</a>
      <a href="{{ route('dashboard') }}">Dashboard</a>
    </div>
    <div class="navbar-actions">
      <span style="font-size:.85rem;color:#64748b;font-weight:500">
        👤 {{ Auth::user()->name }}
      </span>
      <form method="POST" action="{{ route('logout') }}" style="display:inline">
        @csrf
        <button type="submit" class="btn btn-outline btn-sm">🚪 Đăng xuất</button>
      </form>
    </div>
  </div>
</nav>

{{-- ==================== PROFILE CONTENT ==================== --}}
<div style="max-width:800px;margin:2.5rem auto;padding:0 1rem">

  {{-- Alert thành công --}}
  @if (session('status') === 'profile-updated')
    <div class="alert alert-success" style="margin-bottom:1.5rem">
      ✅ Thông tin hồ sơ đã được cập nhật thành công!
    </div>
  @endif

  {{-- Tiêu đề --}}
  <div style="margin-bottom:2rem">
    <span class="section-badge">👤 Tài khoản</span>
    <h1 class="section-title" style="font-size:1.75rem;margin-top:.5rem">Hồ sơ cá nhân</h1>
    <p class="section-subtitle">Cập nhật thông tin tài khoản và bảo mật của bạn</p>
  </div>

  {{-- ===== FORM CẬP NHẬT THÔNG TIN ===== --}}
  <div style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);padding:2rem;margin-bottom:1.5rem">
    <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1.5rem;color:#1e293b;display:flex;align-items:center;gap:.5rem">
      ✏️ Cập nhật thông tin
    </h2>

    <form method="POST" action="{{ route('profile.update') }}">
      @csrf
      @method('PATCH')

      <div class="form-group">
        <label class="form-label">Họ và tên <span class="required">*</span></label>
        <input type="text" name="name" class="form-control @error('name') border-red-500 @enderror"
               value="{{ old('name', $user->name) }}" required autofocus>
        @error('name')
          <p style="color:#ef4444;font-size:.8rem;margin-top:.35rem">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label">Email <span class="required">*</span></label>
        <input type="email" name="email" class="form-control @error('email') border-red-500 @enderror"
               value="{{ old('email', $user->email) }}" required>
        @error('email')
          <p style="color:#ef4444;font-size:.8rem;margin-top:.35rem">{{ $message }}</p>
        @enderror
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
          <p style="font-size:.8rem;color:#f97316;margin-top:.35rem">
            ⚠️ Email chưa được xác minh.
          </p>
        @endif
      </div>

      <button type="submit" class="btn btn-teal">💾 Lưu thay đổi</button>
    </form>
  </div>

  {{-- ===== FORM ĐỔI MẬT KHẨU ===== --}}
  <div style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);padding:2rem;margin-bottom:1.5rem">
    <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1.5rem;color:#1e293b;display:flex;align-items:center;gap:.5rem">
      🔒 Đổi mật khẩu
    </h2>

    @if (session('status') === 'password-updated')
      <div class="alert alert-success" style="margin-bottom:1rem">✅ Mật khẩu đã được cập nhật!</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label class="form-label">Mật khẩu hiện tại</label>
        <input type="password" name="current_password" class="form-control @error('current_password', 'updatePassword') border-red-500 @enderror">
        @error('current_password', 'updatePassword')
          <p style="color:#ef4444;font-size:.8rem;margin-top:.35rem">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label">Mật khẩu mới</label>
        <input type="password" name="password" class="form-control @error('password', 'updatePassword') border-red-500 @enderror">
        @error('password', 'updatePassword')
          <p style="color:#ef4444;font-size:.8rem;margin-top:.35rem">{{ $message }}</p>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label">Xác nhận mật khẩu mới</label>
        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation', 'updatePassword') border-red-500 @enderror">
        @error('password_confirmation', 'updatePassword')
          <p style="color:#ef4444;font-size:.8rem;margin-top:.35rem">{{ $message }}</p>
        @enderror
      </div>

      <button type="submit" class="btn btn-teal">🔐 Cập nhật mật khẩu</button>
    </form>
  </div>

  {{-- ===== XÓA TÀI KHOẢN ===== --}}
  <div style="background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);padding:2rem;border-top:3px solid #ef4444">
    <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:.75rem;color:#dc2626;display:flex;align-items:center;gap:.5rem">
      ⚠️ Vùng nguy hiểm
    </h2>
    <p style="font-size:.875rem;color:#64748b;margin-bottom:1.25rem">
      Xóa tài khoản sẽ xóa vĩnh viễn tất cả dữ liệu. Hành động này không thể hoàn tác.
    </p>

    <button class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border-color:#fca5a5"
            onclick="document.getElementById('deletAccountBox').style.display='block';this.style.display='none'">
      🗑️ Xóa tài khoản
    </button>

    <div id="deletAccountBox" style="display:none;margin-top:1rem">
      <form method="POST" action="{{ route('profile.destroy') }}">
        @csrf
        @method('DELETE')
        <div class="form-group">
          <label class="form-label">Nhập mật khẩu để xác nhận</label>
          <input type="password" name="password" class="form-control" placeholder="Mật khẩu của bạn" required>
          @error('password', 'userDeletion')
            <p style="color:#ef4444;font-size:.8rem;margin-top:.35rem">{{ $message }}</p>
          @enderror
        </div>
        <div style="display:flex;gap:.75rem">
          <button type="submit" class="btn btn-sm" style="background:#dc2626;color:#fff;border-color:#dc2626">
            ✓ Xác nhận xóa
          </button>
          <button type="button" class="btn btn-outline btn-sm"
                  onclick="document.getElementById('deletAccountBox').style.display='none';this.closest('div').previousElementSibling.style.display='inline-flex'">
            Hủy
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection

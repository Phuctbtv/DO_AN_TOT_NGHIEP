{{-- ======================================================
     Admin Sidebar – dùng chung cho tất cả trang admin
     Truyền biến $activeMenu để highlight menu đang active
     Ví dụ: @include('partials.admin-sidebar', ['activeMenu' => 'supplies'])
     ====================================================== --}}

<aside class="sidebar" :class="{ 'open': sidebarOpen }">
  <div class="sidebar-logo">🌊 ĐẠI <span>PHÚC</span></div>

  {{-- TỔNG QUAN --}}
  <div class="sidebar-section">TỔNG QUAN</div>
  <nav class="sidebar-nav">
    <a href="{{ route('admin.dashboard') }}" @class(['active' => ($activeMenu ?? '') === 'dashboard'])>
      <span class="nav-icon">📊</span> Dashboard
    </a>
    <a href="#" @class(['active' => ($activeMenu ?? '') === 'households'])>
      <span class="nav-icon">🏠</span> Hộ dân
    </a>
    <a href="{{ route('admin.warehouses.index') }}" @class(['active' => ($activeMenu ?? '') === 'warehouses'])>
      <span class="nav-icon">📦</span> Kho hàng
    </a>
  </nav>

  {{-- NHU YẾU PHẨM --}}
  <div class="sidebar-section">NHU YẾU PHẨM</div>
  <nav class="sidebar-nav">
    <a href="{{ route('admin.supplies.index') }}" @class(['active' => ($activeMenu ?? '') === 'supplies'])>
      <span class="nav-icon">🛒</span> Danh sách
    </a>
    <a href="{{ route('admin.supplies.create') }}" @class(['active' => ($activeMenu ?? '') === 'supplies-create'])>
      <span class="nav-icon">➕</span> Thêm mới
    </a>
  </nav>

  {{-- VẬN CHUYỂN --}}
  <div class="sidebar-section">VẬN CHUYỂN</div>
  <nav class="sidebar-nav">
    <a href="#" @class(['active' => ($activeMenu ?? '') === 'trips'])>
      <span class="nav-icon">🚛</span> Chuyến xe
    </a>
    <a href="#" @class(['active' => ($activeMenu ?? '') === 'gps'])>
      <span class="nav-icon">🗺️</span> Giám sát GPS
    </a>
    <a href="#" @class(['active' => ($activeMenu ?? '') === 'drivers'])>
      <span class="nav-icon">👤</span> Tài xế
    </a>
  </nav>

  {{-- HỆ THỐNG --}}
  <div class="sidebar-section">HỆ THỐNG</div>
  <nav class="sidebar-nav">
    <a href="{{ route('admin.users.index') }}" @class(['active' => ($activeMenu ?? '') === 'users'])>
      <span class="nav-icon">👥</span> Người dùng
    </a>
    <a href="#" @class(['active' => ($activeMenu ?? '') === 'approvals'])>
      <span class="nav-icon">📋</span> Phê duyệt
    </a>
    <a href="#" @class(['active' => ($activeMenu ?? '') === 'feedbacks'])>
      <span class="nav-icon">💬</span> Phản hồi
    </a>
    <a href="#" @class(['active' => ($activeMenu ?? '') === 'settings'])>
      <span class="nav-icon">⚙️</span> Cài đặt
    </a>
  </nav>

  <div style="margin-top:auto;padding:1rem 1.25rem;border-top:1px solid rgba(255,255,255,.1)">
    <a href="/" style="display:flex;align-items:center;gap:.5rem;font-size:.82rem;color:rgba(255,255,255,.5);padding:.35rem 0">
      ← Về trang chủ
    </a>
  </div>
</aside>

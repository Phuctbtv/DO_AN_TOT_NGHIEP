{{-- ======================================================
     Warehouse Sidebar – dùng chung cho tất cả trang thủ kho
     Truyền biến $activeMenu để highlight menu đang active
     Ví dụ: @include('partials.warehouse-sidebar', ['activeMenu' => 'stock_ins'])
     ====================================================== --}}

<aside class="sidebar" :class="{ 'open': sidebarOpen }">
  <div class="sidebar-logo">🌊 ĐẠI <span>PHÚC</span></div>

  {{-- TỔNG QUAN --}}
  <div class="sidebar-section">KHO HÀNG</div>
  <nav class="sidebar-nav">
    <a href="{{ route('warehouse.dashboard') }}" @class(['active' => ($activeMenu ?? '') === 'dashboard'])>
      <span class="nav-icon">📊</span> Tổng quan
    </a>
  </nav>

  {{-- NHẬP XUẤT KHO --}}
  <div class="sidebar-section">QUẢN LÝ HÀNG HÓA</div>
  <nav class="sidebar-nav">
    <a href="{{ route('warehouse.stock_ins.create') }}" @class(['active' => ($activeMenu ?? '') === 'stock_ins-create'])>
      <span class="nav-icon">📥</span> Nhập kho
    </a>
    <a href="{{ route('warehouse.stock_ins.index') }}" @class(['active' => ($activeMenu ?? '') === 'stock_ins'])>
      <span class="nav-icon">📋</span> Lịch sử nhập kho
    </a>
    <a href="#" @class(['active' => ($activeMenu ?? '') === 'stock_outs'])>
      <span class="nav-icon">📤</span> Xuất kho
    </a>
    <a href="#" @class(['active' => ($activeMenu ?? '') === 'inventory'])>
      <span class="nav-icon">🗄️</span> Tồn kho hiện tại
    </a>
  </nav>

  {{-- BÁO CÁO --}}
  <div class="sidebar-section">BÁO CÁO</div>
  <nav class="sidebar-nav">
    <a href="#" @class(['active' => ($activeMenu ?? '') === 'statistics'])>
      <span class="nav-icon">📈</span> Thống kê
    </a>
    <a href="#" @class(['active' => ($activeMenu ?? '') === 'alerts'])>
      <span class="nav-icon">🔔</span> Cảnh báo tồn kho
    </a>
  </nav>

  <div style="margin-top:auto;padding:1rem 1.25rem;border-top:1px solid rgba(255,255,255,.1)">
    <a href="/" style="display:flex;align-items:center;gap:.5rem;font-size:.82rem;color:rgba(255,255,255,.5);padding:.35rem 0">
      ← Về trang chủ
    </a>
  </div>
</aside>

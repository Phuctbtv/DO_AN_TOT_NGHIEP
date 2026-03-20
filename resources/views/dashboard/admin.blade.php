@extends('layouts.app')
@section('title', 'Admin Dashboard - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  {{-- ==================== SIDEBAR ==================== --}}
  <aside class="sidebar" :class="{ 'open': sidebarOpen }">
    <div class="sidebar-logo">🌊 ĐẠI <span>PHÚC</span></div>

    <div class="sidebar-section">TỔNG QUAN</div>
    <nav class="sidebar-nav">
      <a href="#" class="active"><span class="nav-icon">📊</span> Dashboard</a>
      <a href="#"><span class="nav-icon">🏠</span> Hộ dân</a>
      <a href="#"><span class="nav-icon">📦</span> Kho hàng</a>
    </nav>

    <div class="sidebar-section">VẬN CHUYỂN</div>
    <nav class="sidebar-nav">
      <a href="#"><span class="nav-icon">🚛</span> Chuyến xe</a>
      <a href="#"><span class="nav-icon">🗺️</span> Giám sát GPS</a>
      <a href="#"><span class="nav-icon">👤</span> Tài xế</a>
    </nav>

    <div class="sidebar-section">HỆ THỐNG</div>
    <nav class="sidebar-nav">
      <a href="#"><span class="nav-icon">👥</span> Người dùng</a>
      <a href="#"><span class="nav-icon">📋</span> Phê duyệt</a>
      <a href="#"><span class="nav-icon">💬</span> Phản hồi</a>
      <a href="#"><span class="nav-icon">⚙️</span> Cài đặt</a>
    </nav>

    <div style="margin-top:auto;padding:1rem 1.25rem;border-top:1px solid rgba(255,255,255,.1)">
      <a href="/" style="display:flex;align-items:center;gap:.5rem;font-size:.82rem;color:rgba(255,255,255,.5);padding:.35rem 0">
        ← Về trang chủ
      </a>
    </div>
  </aside>

  {{-- ==================== MAIN ==================== --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '📊 Tổng quan hệ thống'])
    <div style="padding:1.5rem">

    {{-- STAT CARDS --}}
    <div class="dash-stats">
      <div class="dash-card">
        <div class="card-icon" style="background:#dcfce7;color:#16a34a">🏠</div>
        <div class="card-value">12.567</div>
        <div class="card-label">Hộ dân đã hỗ trợ</div>
        <div class="card-change up">↑ 12% so với tuần trước</div>
      </div>
      <div class="dash-card">
        <div class="card-icon" style="background:#dbeafe;color:#2563eb">🚛</div>
        <div class="card-value">1.234</div>
        <div class="card-label">Tổng chuyến xe</div>
        <div class="card-change up">↑ 8% so với tuần trước</div>
      </div>
      <div class="dash-card">
        <div class="card-icon" style="background:#fef3c7;color:#d97706">📦</div>
        <div class="card-value">487</div>
        <div class="card-label">Tấn hàng phân phối</div>
        <div class="card-change up">↑ 15% so với tuần trước</div>
      </div>
      <div class="dash-card">
        <div class="card-icon" style="background:#fee2e2;color:#dc2626">⏳</div>
        <div class="card-value">23</div>
        <div class="card-label">Chờ phê duyệt</div>
        <div class="card-change down">Cần xử lý gấp</div>
      </div>
    </div>

    {{-- CHARTS ROW --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.25rem;margin-bottom:1.5rem">
      <div class="chart-container">
        <h3>📈 Số chuyến xe theo ngày</h3>
        <canvas id="tripsChart" height="200"></canvas>
      </div>
      <div class="chart-container">
        <h3>📊 Trạng thái hộ dân</h3>
        <canvas id="statusChart" height="200"></canvas>
      </div>
    </div>

    {{-- MAP --}}
    <div class="chart-container" style="margin-bottom:1.5rem">
      <h3>🗺️ Giám sát xe trực tiếp</h3>
      <div id="adminMap" style="height:350px;border-radius:8px;margin-top:.75rem"></div>
    </div>

    {{-- TABLE: CHUYẾN XE --}}
    <div class="table-wrap">
      <div class="table-header">
        <h3>🚛 Danh sách chuyến xe gần đây</h3>
        <button class="btn btn-outline btn-sm">Xem tất cả →</button>
      </div>
      <table>
        <thead>
          <tr>
            <th>Mã xe</th>
            <th>Tài xế</th>
            <th>Từ</th>
            <th>Đến</th>
            <th>Số lượng</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody id="adminTripsTable"></tbody>
      </table>
    </div>
    </div>{{-- end padding wrapper --}}
  </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Init charts
  initAdminCharts();
  // Init map
  initAdminMap('adminMap');

  // Fill table
  const tbody = document.getElementById('adminTripsTable');
  if (tbody) {
    tbody.innerHTML = MOCK.adminTrips.map(t => `
      <tr>
        <td><strong>${t.id}</strong></td>
        <td>${t.driver}</td>
        <td>${t.from}</td>
        <td>${t.to}</td>
        <td>${t.items} gói</td>
        <td><span class="status-pill ${t.statusType}">${t.status}</span></td>
      </tr>
    `).join('');
  }
});
</script>
@endpush

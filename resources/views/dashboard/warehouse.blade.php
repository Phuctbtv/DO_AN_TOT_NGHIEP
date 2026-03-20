@extends('layouts.app')
@section('title', 'Quản lý Kho - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ showImport: false, showExport: false }">

  {{-- ==================== SIDEBAR ==================== --}}
  <aside class="sidebar">
    <div class="sidebar-logo">🌊 ĐẠI <span>PHÚC</span></div>

    <div class="sidebar-section">KHO HÀNG</div>
    <nav class="sidebar-nav">
      <a href="#" class="active"><span class="nav-icon">📊</span> Tổng quan</a>
      <a href="#"><span class="nav-icon">📥</span> Nhập kho</a>
      <a href="#"><span class="nav-icon">📤</span> Xuất kho</a>
      <a href="#"><span class="nav-icon">📋</span> Lịch sử</a>
    </nav>

    <div class="sidebar-section">BÁO CÁO</div>
    <nav class="sidebar-nav">
      <a href="#"><span class="nav-icon">📈</span> Thống kê</a>
      <a href="#"><span class="nav-icon">🔔</span> Cảnh báo tồn kho</a>
    </nav>

    <div style="margin-top:auto;padding:1rem 1.25rem;border-top:1px solid rgba(255,255,255,.1)">
      <a href="/" style="color:rgba(255,255,255,.5);font-size:.8rem">← Về trang chủ</a>
    </div>
  </aside>

  {{-- ==================== MAIN ==================== --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '📦 Quản lý kho hàng'])
    <div style="padding:1.25rem 1.5rem;display:flex;justify-content:flex-end;gap:.5rem;border-bottom:1px solid #f1f5f9">
      <button class="btn btn-teal btn-sm" @click="showImport = true">📥 Nhập kho</button>
      <button class="btn btn-orange btn-sm" @click="showExport = true">📤 Xuất kho</button>
    </div>
    <div style="padding:1.5rem">

    {{-- STAT CARDS --}}
    <div class="dash-stats">
      <div class="dash-card">
        <div class="card-icon" style="background:#dcfce7;color:#16a34a">📦</div>
        <div class="card-value">6</div>
        <div class="card-label">Loại hàng hóa</div>
      </div>
      <div class="dash-card">
        <div class="card-icon" style="background:#dbeafe;color:#2563eb">📥</div>
        <div class="card-value">7.950</div>
        <div class="card-label">Tổng nhập (đơn vị)</div>
      </div>
      <div class="dash-card">
        <div class="card-icon" style="background:#fef3c7;color:#d97706">📤</div>
        <div class="card-value">11.870</div>
        <div class="card-label">Tổng xuất (đơn vị)</div>
      </div>
      <div class="dash-card">
        <div class="card-icon" style="background:#fee2e2;color:#dc2626">⚠️</div>
        <div class="card-value">2</div>
        <div class="card-label">Sắp hết hàng</div>
      </div>
    </div>

    {{-- INVENTORY TABLE --}}
    <div class="table-wrap" style="margin-bottom:1.5rem">
      <div class="table-header">
        <h3>📋 Tồn kho hiện tại</h3>
        <button class="btn btn-outline btn-sm">🔄 Làm mới</button>
      </div>
      <table>
        <thead>
          <tr>
            <th>Mặt hàng</th>
            <th>Đơn vị</th>
            <th>Tồn kho</th>
            <th>Đã nhập</th>
            <th>Đã xuất</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody id="warehouseTable"></tbody>
      </table>
    </div>

    {{-- QUICK FORMS --}}
    <div class="warehouse-grid">
      {{-- RECENT IMPORTS --}}
      <div class="wh-card">
        <div class="wh-card-header">📥 Nhập kho gần đây</div>
        <div class="wh-card-body">
          <div class="ticker-item" style="padding:.75rem 0;border-color:#f1f5f9">
            <span class="ticker-time">10:30</span>
            <span class="ticker-text">2.000 kg Gạo từ nhà tài trợ ABC</span>
          </div>
          <div class="ticker-item" style="padding:.75rem 0;border-color:#f1f5f9">
            <span class="ticker-time">09:15</span>
            <span class="ticker-text">300 thùng Mỳ tôm từ siêu thị XYZ</span>
          </div>
          <div class="ticker-item" style="padding:.75rem 0;border-color:#f1f5f9">
            <span class="ticker-time">08:00</span>
            <span class="ticker-text">500 bộ quần áo từ chiến dịch quyên góp</span>
          </div>
        </div>
      </div>

      {{-- RECENT EXPORTS --}}
      <div class="wh-card">
        <div class="wh-card-header">📤 Xuất kho gần đây</div>
        <div class="wh-card-body">
          <div class="ticker-item" style="padding:.75rem 0;border-color:#f1f5f9">
            <span class="ticker-time">11:00</span>
            <span class="ticker-text">Xe TX-089: 120 gói quà → Hòa Bình</span>
          </div>
          <div class="ticker-item" style="padding:.75rem 0;border-color:#f1f5f9">
            <span class="ticker-time">10:00</span>
            <span class="ticker-text">Xe TX-045: 80 gói quà → Sơn Tịnh</span>
          </div>
          <div class="ticker-item" style="padding:.75rem 0;border-color:#f1f5f9">
            <span class="ticker-time">08:30</span>
            <span class="ticker-text">200L nước sạch → Quảng Trị (cấp tốc)</span>
          </div>
        </div>
      </div>
    </div>

    {{-- MODAL: NHẬP KHO --}}
    <template x-if="showImport">
      <div class="modal-overlay" @click.self="showImport = false">
        <div class="modal-box" @click.stop>
          <div class="modal-header">
            <h3>📥 Phiếu nhập kho</h3>
            <button class="modal-close" @click="showImport = false">✕</button>
          </div>
          <div class="modal-body">
            <form onsubmit="handleWarehouseForm(event, 'in')">
              <div class="form-group">
                <label class="form-label">Mặt hàng <span class="required">*</span></label>
                <select class="form-control" required>
                  <option value="">-- Chọn mặt hàng --</option>
                  <option>Gạo (kg)</option>
                  <option>Mỳ tôm (thùng)</option>
                  <option>Nước suối (lít)</option>
                  <option>Quần áo (bộ)</option>
                  <option>Chăn màn (cái)</option>
                  <option>Thuốc y tế (hộp)</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Số lượng <span class="required">*</span></label>
                <input type="number" class="form-control" placeholder="VD: 500" required>
              </div>
              <div class="form-group">
                <label class="form-label">Nguồn / Nhà tài trợ</label>
                <input type="text" class="form-control" placeholder="VD: Công ty ABC">
              </div>
              <div class="form-group">
                <label class="form-label">Ghi chú</label>
                <textarea class="form-control" rows="2" placeholder="Ghi chú thêm..."></textarea>
              </div>
              <button type="submit" class="btn btn-teal btn-lg" style="width:100%">✅ Xác nhận nhập kho</button>
            </form>
          </div>
        </div>
      </div>
    </template>

    {{-- MODAL: XUẤT KHO --}}
    <template x-if="showExport">
      <div class="modal-overlay" @click.self="showExport = false">
        <div class="modal-box" @click.stop>
          <div class="modal-header">
            <h3>📤 Phiếu xuất kho</h3>
            <button class="modal-close" @click="showExport = false">✕</button>
          </div>
          <div class="modal-body">
            <form onsubmit="handleWarehouseForm(event, 'out')">
              <div class="form-group">
                <label class="form-label">Mặt hàng <span class="required">*</span></label>
                <select class="form-control" required>
                  <option value="">-- Chọn mặt hàng --</option>
                  <option>Gạo (kg)</option>
                  <option>Mỳ tôm (thùng)</option>
                  <option>Nước suối (lít)</option>
                  <option>Quần áo (bộ)</option>
                  <option>Chăn màn (cái)</option>
                  <option>Thuốc y tế (hộp)</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Số lượng <span class="required">*</span></label>
                <input type="number" class="form-control" placeholder="VD: 200" required>
              </div>
              <div class="form-group">
                <label class="form-label">Mã chuyến xe</label>
                <input type="text" class="form-control" placeholder="VD: TX-089">
              </div>
              <div class="form-group">
                <label class="form-label">Điểm đến</label>
                <input type="text" class="form-control" placeholder="VD: Xã Hòa Bình, Đà Nẵng">
              </div>
              <button type="submit" class="btn btn-orange btn-lg" style="width:100%">📤 Xác nhận xuất kho</button>
            </form>
          </div>
        </div>
      </div>
    </template>

    </div>{{-- end padding wrapper --}}
  </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.getElementById('warehouseTable');
  if (tbody) {
    tbody.innerHTML = MOCK.warehouseItems.map(item => {
      let status = 'success', statusText = 'Đủ hàng';
      if (item.inStock < 200) { status = 'danger'; statusText = 'Sắp hết'; }
      else if (item.inStock < 1000) { status = 'warning'; statusText = 'Trung bình'; }
      return `
        <tr>
          <td><strong>${item.name}</strong></td>
          <td>${item.unit}</td>
          <td style="font-weight:700">${item.inStock.toLocaleString('vi-VN')}</td>
          <td style="color:#16a34a">+${item.incoming.toLocaleString('vi-VN')}</td>
          <td style="color:#dc2626">-${item.outgoing.toLocaleString('vi-VN')}</td>
          <td><span class="status-pill ${status}">${statusText}</span></td>
        </tr>
      `;
    }).join('');
  }
});
</script>
@endpush

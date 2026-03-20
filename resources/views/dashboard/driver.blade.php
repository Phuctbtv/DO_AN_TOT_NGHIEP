@extends('layouts.app')
@section('title', 'Tài xế - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ tab: 'deliveries', showScanner: false, showUpload: false }">

  {{-- ==================== SIDEBAR ==================== --}}
  <aside class="sidebar">
    <div class="sidebar-logo">🌊 ĐẠI <span>PHÚC</span></div>

    <div class="sidebar-section">NHIỆM VỤ</div>
    <nav class="sidebar-nav">
      <a href="#" :class="tab === 'deliveries' ? 'active' : ''" @click.prevent="tab = 'deliveries'">
        <span class="nav-icon">📋</span> Danh sách giao
      </a>
      <a href="#" :class="tab === 'scanner' ? 'active' : ''" @click.prevent="tab = 'scanner'">
        <span class="nav-icon">📷</span> Quét QR
      </a>
      <a href="#" :class="tab === 'map' ? 'active' : ''" @click.prevent="tab = 'map'">
        <span class="nav-icon">🗺️</span> Bản đồ GPS
      </a>
    </nav>

    <div class="sidebar-section">TIẾN TRÌNH HÔM NAY</div>
    <div style="padding:.5rem 1.25rem;">
      <div style="display:flex;justify-content:space-between;font-size:.82rem;margin-bottom:.5rem">
        <span style="color:rgba(255,255,255,.6)">Hoàn thành</span>
        <span style="color:#22c55e;font-weight:700">1 / 3</span>
      </div>
      <div style="background:rgba(255,255,255,.1);border-radius:999px;height:6px;overflow:hidden">
        <div style="width:33%;height:100%;background:#22c55e;border-radius:999px"></div>
      </div>
    </div>

    <div style="margin-top:auto;padding:1rem 1.25rem;border-top:1px solid rgba(255,255,255,.1)">
      <a href="/" style="display:flex;align-items:center;gap:.5rem;font-size:.82rem;color:rgba(255,255,255,.5);padding:.35rem 0">
        ← Về trang chủ
      </a>
    </div>
  </aside>

  {{-- ==================== MAIN ==================== --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '🚛 Dashboard Tài xế'])
    <div style="padding:1.5rem">

      {{-- STAT CARDS ROW --}}
      <div class="dash-stats" style="margin-bottom:1.5rem">
        <div class="dash-card">
          <div class="card-icon" style="background:#fef3c7;color:#d97706">⏳</div>
          <div class="card-value">2</div>
          <div class="card-label">Chờ giao</div>
        </div>
        <div class="dash-card">
          <div class="card-icon" style="background:#dbeafe;color:#2563eb">🚛</div>
          <div class="card-value">1</div>
          <div class="card-label">Đang giao</div>
        </div>
        <div class="dash-card">
          <div class="card-icon" style="background:#dcfce7;color:#16a34a">✅</div>
          <div class="card-value">1</div>
          <div class="card-label">Hoàn thành hôm nay</div>
        </div>
        <div class="dash-card">
          <div class="card-icon" style="background:#f0fdfa;color:#0d9488">📦</div>
          <div class="card-value">238</div>
          <div class="card-label">Gói hàng đã giao</div>
        </div>
      </div>

      {{-- ==================== TAB: DANH SÁCH GIAO ==================== --}}
      <div x-show="tab === 'deliveries'">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
          <h3 style="font-size:1.1rem;font-weight:700">📋 Danh sách điểm giao hôm nay</h3>
          <span style="font-size:.8rem;color:#64748b">{{ now()->format('d/m/Y') }}</span>
        </div>

        {{-- Delivery list as table --}}
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Hộ dân</th>
                <th>Địa chỉ</th>
                <th>Hàng hóa</th>
                <th>Khoảng cách</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>1</strong></td>
                <td>
                  <div style="font-weight:600">Nguyễn Văn A</div>
                  <div style="font-size:.75rem;color:#64748b">📞 0901.234.567</div>
                </td>
                <td style="font-size:.85rem">Thôn 3, Xã Hòa Bình, Đà Nẵng</td>
                <td style="font-size:.85rem">10 gói quà</td>
                <td style="font-size:.85rem">2.3 km</td>
                <td><span class="status-pill warning">Chờ giao</span></td>
                <td>
                  <div style="display:flex;gap:.4rem">
                    <a href="tel:0901234567" class="btn btn-outline-teal btn-sm">📞</a>
                    <button class="btn btn-teal btn-sm" onclick="getLocation('lat1','lng1',this)">📍 GPS</button>
                    <button class="btn btn-orange btn-sm" @click="showUpload = true">📷</button>
                  </div>
                  <input type="hidden" id="lat1"><input type="hidden" id="lng1">
                </td>
              </tr>
              <tr style="background:#fff8f0">
                <td><strong>2</strong></td>
                <td>
                  <div style="font-weight:600">Trần Thị B</div>
                  <div style="font-size:.75rem;color:#64748b">📞 0912.345.678</div>
                </td>
                <td style="font-size:.85rem">Tổ 5, Cẩm Nam, Hội An</td>
                <td style="font-size:.85rem">8 gói quà</td>
                <td style="font-size:.85rem">5.1 km</td>
                <td><span class="status-pill info">Đang giao</span></td>
                <td>
                  <div style="display:flex;gap:.4rem">
                    <a href="tel:0912345678" class="btn btn-outline-teal btn-sm">📞</a>
                    <button class="btn btn-teal btn-sm" onclick="showToast('✅ Đã xác nhận giao thành công!')">✅ Xác nhận</button>
                  </div>
                </td>
              </tr>
              <tr style="opacity:.65">
                <td><strong>3</strong></td>
                <td>
                  <div style="font-weight:600">Lê Văn C</div>
                  <div style="font-size:.75rem;color:#64748b">📞 0923.456.789</div>
                </td>
                <td style="font-size:.85rem">Khối 4, TT Hải Lăng, Quảng Trị</td>
                <td style="font-size:.85rem">15 gói quà</td>
                <td style="font-size:.85rem">12.7 km</td>
                <td><span class="status-pill success">Hoàn thành</span></td>
                <td>
                  <span style="font-size:.78rem;color:#64748b">Giao lúc 09:30</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      {{-- ==================== TAB: QR SCANNER ==================== --}}
      <div x-show="tab === 'scanner'" style="max-width:600px;margin:0 auto">
        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem">📷 Quét mã QR hộ dân</h3>
        <div class="chart-container">
          <div id="qr-reader"></div>
          <div style="margin-top:1rem;background:#f8fafc;border-radius:8px;padding:1rem">
            <h4 style="font-size:.9rem;margin-bottom:.5rem">📋 Hướng dẫn</h4>
            <ol style="font-size:.85rem;color:#64748b;padding-left:1.2rem;line-height:2">
              <li>Hướng camera vào mã QR của hộ dân</li>
              <li>Hệ thống tự nhận diện và xác nhận giao hàng</li>
              <li>Chụp ảnh minh chứng (bắt buộc)</li>
              <li>Nhấn "Hoàn tất" để kết thúc</li>
            </ol>
          </div>
        </div>
      </div>

      {{-- ==================== TAB: MAP ==================== --}}
      <div x-show="tab === 'map'">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
          <h3 style="font-size:1.1rem;font-weight:700">🗺️ Bản đồ tuyến đường</h3>
          <button class="btn btn-teal btn-sm" onclick="getLocation('drvLat','drvLng',this)">
            📍 Cập nhật vị trí hiện tại
          </button>
        </div>
        <div class="chart-container">
          <div id="driverMap" style="height:450px;border-radius:8px"></div>
        </div>
        <input type="hidden" id="drvLat"><input type="hidden" id="drvLng">
      </div>

    </div>{{-- end padding wrapper --}}

    {{-- MODAL: UPLOAD ẢNH --}}
    <template x-if="showUpload">
      <div class="modal-overlay" @click.self="showUpload = false">
        <div class="modal-box" style="max-width:480px" @click.stop>
          <div class="modal-header">
            <h3>📷 Chụp ảnh minh chứng</h3>
            <button class="modal-close" @click="showUpload = false">✕</button>
          </div>
          <div class="modal-body">
            <p style="font-size:.85rem;color:#64748b;margin-bottom:1rem">Chụp ảnh tại điểm giao hàng làm bằng chứng xác nhận</p>
            <label class="file-upload" style="padding:2rem">
              <input type="file" accept="image/*" capture="environment" style="display:none" onchange="handleFileUpload(this,'driverPhotoPreview')">
              📸 Nhấn để chụp ảnh hoặc chọn từ thư viện
            </label>
            <div id="driverPhotoPreview" style="display:flex;flex-wrap:wrap;gap:.25rem;margin-top:.75rem"></div>
            <button class="btn btn-teal btn-lg" style="width:100%;margin-top:1rem" @click="showUpload = false; showToast('✅ Đã lưu ảnh minh chứng thành công')">
              💾 Lưu ảnh minh chứng
            </button>
          </div>
        </div>
      </div>
    </template>

  </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Init driver map
  const mapEl = document.getElementById('driverMap');
  if (mapEl) {
    const map = L.map('driverMap').setView([16.0544, 108.2022], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap', maxZoom: 18
    }).addTo(map);

    const deliveries = [
      { lat: 16.0544, lng: 108.2022, name: 'Nguyễn Văn A', status: 'Chờ giao', color: '#eab308' },
      { lat: 16.0200, lng: 108.2500, name: 'Trần Thị B', status: 'Đang giao', color: '#f97316' },
    ];
    deliveries.forEach(d => {
      L.marker([d.lat, d.lng], {
        icon: L.divIcon({
          className: 'delivery-marker',
          html: `<div style="width:14px;height:14px;background:${d.color};border-radius:50%;border:3px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,.3)"></div>`,
          iconSize: [14, 14], iconAnchor: [7, 7],
        })
      }).addTo(map).bindPopup(`<strong>${d.name}</strong><br>${d.status}`);
    });

    // Observe tab change to fix map render
    document.querySelectorAll('[\\@click\\.prevent]').forEach(btn => {
      btn.addEventListener('click', () => setTimeout(() => map.invalidateSize(), 300));
    });
  }

  initQRScanner();
});
</script>
@endpush

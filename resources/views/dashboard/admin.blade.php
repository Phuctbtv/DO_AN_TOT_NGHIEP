@extends('layouts.app')
@section('title', 'Admin Dashboard - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  @include('partials.admin-sidebar', ['activeMenu' => 'dashboard'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '📊 Tổng quan hệ thống'])
    <div style="padding:1.5rem">

    {{-- ==================== STAT CARDS ==================== --}}
    <div class="dash-stats">

      {{-- Card 1: Hộ dân --}}
      <div class="dash-card">
        <div class="card-icon" style="background:#dcfce7;color:#16a34a">🏠</div>
        <div class="card-value">{{ number_format($stats['households']) }}</div>
        <div class="card-label">Hộ dân đã hỗ trợ</div>
        @if($stats['households_change']['value'] !== null)
          <div class="card-change {{ $stats['households_change']['up'] ? 'up' : 'down' }}">
            {{ $stats['households_change']['up'] ? '↑' : '↓' }} {{ $stats['households_change']['value'] }}% so với tuần trước
          </div>
        @else
          <div class="card-change up">— Chưa có dữ liệu tuần trước</div>
        @endif
      </div>

      {{-- Card 2: Tổng chuyến xe --}}
      <div class="dash-card">
        <div class="card-icon" style="background:#dbeafe;color:#2563eb">🚛</div>
        <div class="card-value">{{ number_format($stats['total_trips']) }}</div>
        <div class="card-label">Tổng chuyến xe</div>
        @if($stats['trips_change']['value'] !== null)
          <div class="card-change {{ $stats['trips_change']['up'] ? 'up' : 'down' }}">
            {{ $stats['trips_change']['up'] ? '↑' : '↓' }} {{ $stats['trips_change']['value'] }}% so với tuần trước
          </div>
        @else
          <div class="card-change up">— Chưa có dữ liệu tuần trước</div>
        @endif
      </div>

      {{-- Card 3: Tấn hàng --}}
      <div class="dash-card">
        <div class="card-icon" style="background:#fef3c7;color:#d97706">📦</div>
        <div class="card-value">{{ $stats['total_ton'] }}T</div>
        <div class="card-label">Tấn hàng phân phối</div>
        @if($stats['ton_change']['value'] !== null)
          <div class="card-change {{ $stats['ton_change']['up'] ? 'up' : 'down' }}">
            {{ $stats['ton_change']['up'] ? '↑' : '↓' }} {{ $stats['ton_change']['value'] }}% so với tuần trước
          </div>
        @else
          <div class="card-change up">— Chưa có dữ liệu tuần trước</div>
        @endif
      </div>

      {{-- Card 4: Chờ duyệt --}}
      <div class="dash-card">
        <div class="card-icon" style="background:#fee2e2;color:#dc2626">⏳</div>
        <div class="card-value">{{ $stats['pending'] }}</div>
        <div class="card-label">Chờ phê duyệt</div>
        @if($stats['pending'] > 0)
          <a href="{{ route('admin.households.pending') }}"
             style="display:inline-block;margin-top:.5rem;background:#f59e0b;color:#fff;padding:.25rem .75rem;border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none">
            Xem &amp; duyệt →
          </a>
        @else
          <div class="card-change up">Không có đơn chờ</div>
        @endif
      </div>
    </div>

    {{-- ==================== CHARTS ==================== --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.25rem;margin-bottom:1.5rem">

      {{-- Biểu đồ 1: Chuyến xe 7 ngày --}}
      <div class="chart-container">
        <h3>📈 Số chuyến xe theo ngày (7 ngày gần nhất)</h3>
        <canvas id="tripsChart" height="200"></canvas>
      </div>

      {{-- Biểu đồ 2: Trạng thái hộ dân --}}
      <div class="chart-container">
        <h3>📊 Trạng thái hộ dân</h3>
        <canvas id="statusChart" height="200"></canvas>
        <div style="display:flex;gap:.75rem;justify-content:center;margin-top:.75rem;font-size:.78rem;flex-wrap:wrap">
          <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#10b981;margin-right:4px"></span>Đã duyệt ({{ $statusChart['active'] }})</span>
          <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#f59e0b;margin-right:4px"></span>Chờ duyệt ({{ $statusChart['pending'] }})</span>
          <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#ef4444;margin-right:4px"></span>Từ chối ({{ $statusChart['rejected'] }})</span>
        </div>
      </div>
    </div>

    {{-- ==================== MAP ==================== --}}
    <div class="chart-container" style="margin-bottom:1.5rem">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem">
        <h3 style="margin:0">🗺️ Giám sát xe đang giao hàng</h3>
        <span style="font-size:.8rem;color:#64748b">
          {{ count($shippingTrips) }} xe đang hoạt động
          @if(count($shippingTrips) > 0)
            <span style="display:inline-block;width:8px;height:8px;background:#10b981;border-radius:50%;margin-left:4px;animation:pulse 1.5s infinite"></span>
          @endif
        </span>
      </div>
      <div id="adminMap" style="height:350px;border-radius:8px"></div>
      @if(count($shippingTrips) === 0)
        <div style="margin-top:.5rem;font-size:.8rem;color:#94a3b8;text-align:center">Hiện không có xe nào đang giao hàng</div>
      @endif
    </div>

    {{-- ==================== TABLE: CHUYẾN XE GẦN ĐÂY ==================== --}}
    <div class="table-wrap">
      <div class="table-header">
        <h3>🚛 Danh sách chuyến xe gần đây</h3>
        <a href="{{ route('admin.trips.index') }}" class="btn btn-outline btn-sm">Xem tất cả →</a>
      </div>
      <div style="overflow-x:auto">
        <table>
          <thead>
            <tr>
              <th>Mã chuyến</th>
              <th>Tài xế</th>
              <th>Kho xuất</th>
              <th>Điểm giao đầu</th>
              <th>Tổng hàng (kg)</th>
              <th>Trạng thái</th>
              <th>Tạo lúc</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentTrips as $t)
            <tr data-trip-id="{{ $t['id'] }}">
              <td><strong style="color:#0d9488">{{ $t['trip_code'] }}</strong></td>
              <td>{{ $t['driver'] }}</td>
              <td style="font-size:.82rem;color:#64748b">{{ $t['warehouse'] }}</td>
              <td style="font-size:.82rem;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $t['first_addr'] }}</td>
              <td class="col-qty">{{ number_format($t['total_qty']) }}</td>
              <td>
                <span style="background:{{ $t['status_bg'] }};color:{{ $t['status_color'] }};padding:.2rem .55rem;border-radius:6px;font-size:.75rem;font-weight:600">
                  {{ $t['status_label'] }}
                </span>
              </td>
              <td style="font-size:.78rem;color:#94a3b8">{{ $t['created_at'] }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="7" style="text-align:center;padding:2rem;color:#94a3b8">Chưa có chuyến xe nào</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    </div>{{-- end padding wrapper --}}
  </main>
</div>

{{-- Inject dữ liệu PHP → JS --}}
<script>
  window.ADMIN_DATA = {
    chartLabels:   @json($chartLabels),
    chartData:     @json($chartData),
    statusChart:   @json($statusChart),
    shippingTrips: @json($shippingTrips),
  };
</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const D = window.ADMIN_DATA;

  // ── BIỂU ĐỒ 1: Chuyến xe theo ngày ──────────────────────────
  const ctx1 = document.getElementById('tripsChart');
  if (ctx1) {
    new Chart(ctx1, {
      type: 'bar',
      data: {
        labels: D.chartLabels,
        datasets: [{
          label: 'Số chuyến xe',
          data: D.chartData,
          backgroundColor: 'rgba(13,148,136,.7)',
          borderColor: '#0d9488',
          borderWidth: 1,
          borderRadius: 6,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
      }
    });
  }

  // ── BIỂU ĐỒ 2: Trạng thái hộ dân (Doughnut) ─────────────────
  const ctx2 = document.getElementById('statusChart');
  if (ctx2) {
    const s = D.statusChart;
    new Chart(ctx2, {
      type: 'doughnut',
      data: {
        labels: ['Đã duyệt', 'Chờ duyệt', 'Từ chối'],
        datasets: [{
          data: [s.active, s.pending, s.rejected],
          backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
          borderWidth: 2,
          borderColor: '#fff',
        }]
      },
      options: {
        responsive: true,
        cutout: '65%',
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: ctx => ` ${ctx.label}: ${ctx.parsed} hộ`
            }
          }
        }
      }
    });
  }

  // ── BẢN ĐỒ GIÁM SÁT XE ──────────────────────────────────────
  const mapEl = document.getElementById('adminMap');
  if (mapEl) {
    const map = L.map('adminMap').setView([16.47, 107.59], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap'
    }).addTo(map);

    const tripMarkers = {};

    D.shippingTrips.forEach(t => {
      const icon = L.divIcon({
        className: '',
        html: `<div style="background:#3b82f6;color:#fff;border-radius:8px;padding:4px 8px;font-size:11px;font-weight:700;white-space:nowrap;box-shadow:0 2px 8px rgba(0,0,0,.25)">🚛 ${t.trip_code}</div>`,
        iconAnchor: [30, 20]
      });
      const marker = L.marker([t.lat, t.lng], { icon })
        .bindPopup(`<b>${t.trip_code}</b><br>Tài xế: ${t.driver}<br>Tiến độ: ${t.done}/${t.total} điểm`)
        .addTo(map);
      tripMarkers[t.trip_id] = marker;
    });

    // WebSocket: cập nhật vị trí marker realtime
    if (window.Echo) {
      setupAdminWebSocket(map, tripMarkers);
    }
  }

  // ── WEBSOCKET ────────────────────────────────────────────────
  function setupAdminWebSocket(map, tripMarkers) {
    fetch('/admin/trips/active-ids')
      .then(r => r.ok ? r.json() : [])
      .then(tripIds => {
        if (!tripIds.length) return;
        tripIds.forEach(tripId => {
          window.Echo.private(`deliveries.${tripId}`)
            .listen('.DeliveryUpdated', (e) => {
              showAdminToast(`🚛 Chuyến ${tripId}: ${e.success_count}/${e.total_count} điểm`, 'info');
              // Cập nhật hàng bảng
              const row = document.querySelector(`tr[data-trip-id="${tripId}"]`);
              if (row) {
                const qtyCell = row.querySelector('.col-qty');
                if (qtyCell && e.done_count !== undefined) qtyCell.textContent = e.done_count;
              }
            });
          window.Echo.private(`trips.${tripId}`)
            .listen('.TripStatusUpdated', (e) => {
              if (e.status === 'completed') {
                showAdminToast(`🎉 Chuyến ${e.trip_code} đã HOÀN THÀNH!`, 'success');
              }
            });
        });
        showAdminToast(`📡 Đang giám sát ${tripIds.length} chuyến xe realtime`, 'info');
      })
      .catch(() => {});
  }
});

// Toast notification
const _adminToasts = [];
function showAdminToast(msg, type = 'info') {
  const colors = { info: '#0f172a', success: '#059669', warning: '#d97706', error: '#dc2626' };
  const toast = document.createElement('div');
  toast.style.cssText = `
    position:fixed;bottom:${2 + _adminToasts.length * 3.5}rem;right:1.5rem;
    background:${colors[type]||colors.info};color:#fff;
    padding:.6rem 1.2rem;border-radius:9px;font-size:.82rem;font-weight:600;
    z-index:99999;opacity:0;transition:opacity .3s;white-space:nowrap;
    box-shadow:0 4px 20px rgba(0,0,0,.3)
  `;
  toast.textContent = msg;
  document.body.appendChild(toast);
  _adminToasts.push(toast);
  requestAnimationFrame(() => toast.style.opacity = 1);
  setTimeout(() => {
    toast.style.opacity = 0;
    setTimeout(() => { toast.remove(); _adminToasts.splice(_adminToasts.indexOf(toast), 1); }, 300);
  }, 4000);
}
</script>
@endpush

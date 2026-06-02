@extends('layouts.app')
@section('title', 'Giám sát GPS – ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  @include('partials.admin-sidebar', ['activeMenu' => 'gps'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '🗺️ Giám sát GPS'])

    <div style="padding:1.5rem">

      {{-- ══════════ STAT STRIP ══════════ --}}
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem">

        <div class="dash-card" style="text-align:center">
          <div class="card-icon" style="background:#dbeafe;color:#2563eb;margin:0 auto .5rem">🚛</div>
          <div class="card-value" id="stat-active">{{ count($shippingTrips) }}</div>
          <div class="card-label">Xe đang giao</div>
        </div>

        <div class="dash-card" style="text-align:center">
          <div class="card-icon" style="background:#d1fae5;color:#059669;margin:0 auto .5rem">📍</div>
          <div class="card-value" id="stat-hh">{{ $householdsWithCoords->count() }}</div>
          <div class="card-label">Hộ dân có GPS</div>
        </div>

        <div class="dash-card" style="text-align:center">
          <div class="card-icon" style="background:#fef3c7;color:#d97706;margin:0 auto .5rem">⚠️</div>
          <div class="card-value">{{ count($warningDeliveries) }}</div>
          <div class="card-label">Giao hàng GPS lệch</div>
        </div>

        <div class="dash-card" style="text-align:center">
          <div class="card-icon" style="background:#ede9fe;color:#7c3aed;margin:0 auto .5rem">📡</div>
          <div class="card-value">{{ $gpsTolerance }}m</div>
          <div class="card-label">Ngưỡng GPS cho phép</div>
        </div>

      </div>

      {{-- ══════════ LAYOUT: BẢN ĐỒ + PANEL ══════════ --}}
      <div style="display:grid;grid-template-columns:1fr 360px;gap:1.25rem;margin-bottom:1.5rem">

        {{-- BẢN ĐỒ --}}
        <div class="chart-container" style="padding:0;overflow:hidden;position:relative">
          <div style="padding:.9rem 1.1rem;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e2e8f0">
            <div style="font-weight:700;font-size:.95rem">🗺️ Bản đồ theo dõi realtime</div>
            <div style="display:flex;align-items:center;gap:.5rem;font-size:.78rem;color:#64748b">
              <span id="live-badge" style="display:inline-flex;align-items:center;gap:4px;background:#d1fae5;color:#059669;padding:.2rem .6rem;border-radius:999px;font-weight:600">
                <span style="width:7px;height:7px;background:#10b981;border-radius:50%;animation:pulse 1.5s infinite;display:inline-block"></span>
                LIVE
              </span>
              <span id="last-refresh">—</span>
            </div>
          </div>
          <div id="gpsMap" style="height:480px"></div>

          {{-- Legend --}}
          <div style="position:absolute;bottom:12px;left:12px;background:rgba(255,255,255,.93);border-radius:8px;padding:.5rem .75rem;font-size:.73rem;box-shadow:0 2px 10px rgba(0,0,0,.12);z-index:999;line-height:1.7">
            <div><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#3b82f6;margin-right:5px;vertical-align:middle"></span>Xe đang giao</div>
            <div><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#10b981;margin-right:5px;vertical-align:middle"></span>Hộ dân (đã duyệt)</div>
            <div><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#f59e0b;margin-right:5px;vertical-align:middle"></span>GPS lệch vượt ngưỡng</div>
          </div>
        </div>

        {{-- PANEL CHUYẾN XE --}}
        <div style="display:flex;flex-direction:column;gap:.75rem;max-height:558px;overflow-y:auto">
          @if(count($shippingTrips) === 0)
            <div style="background:#f8fafc;border:2px dashed #e2e8f0;border-radius:12px;padding:2.5rem;text-align:center;color:#94a3b8">
              <div style="font-size:2.5rem;margin-bottom:.5rem">🚛</div>
              <div style="font-weight:600">Không có xe nào đang giao hàng</div>
              <div style="font-size:.8rem;margin-top:.25rem">Sẽ hiển thị khi chuyến xe chuyển sang trạng thái "Đang giao"</div>
            </div>
          @else
            @foreach($shippingTrips as $trip)
            <div class="chart-container" style="padding:.9rem 1rem;cursor:pointer;transition:box-shadow .2s"
                 onclick="focusTrip({{ $trip['trip_id'] }})"
                 id="trip-card-{{ $trip['trip_id'] }}"
                 onmouseenter="this.style.boxShadow='0 4px 20px rgba(59,130,246,.2)'"
                 onmouseleave="this.style.boxShadow=''">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem">
                <div style="font-weight:700;color:#0d9488">{{ $trip['trip_code'] }}</div>
                <span style="background:#dbeafe;color:#2563eb;border-radius:999px;padding:.1rem .5rem;font-size:.72rem;font-weight:600">Đang giao</span>
              </div>
              <div style="font-size:.8rem;color:#64748b;margin-bottom:.6rem">
                👤 {{ $trip['driver'] }} &nbsp;|&nbsp; 📦 {{ $trip['warehouse'] }}
              </div>
              {{-- Progress bar --}}
              <div style="background:#e2e8f0;border-radius:999px;height:6px;margin-bottom:.35rem;overflow:hidden">
                <div style="background:#10b981;height:100%;width:{{ $trip['progress'] }}%;border-radius:999px;transition:width .5s"></div>
              </div>
              <div style="font-size:.75rem;color:#64748b;display:flex;justify-content:space-between">
                <span>{{ $trip['done'] }}/{{ $trip['total'] }} điểm giao</span>
                <span style="color:#10b981;font-weight:600">{{ $trip['progress'] }}%</span>
              </div>
              @if($trip['started_at'])
              <div style="font-size:.72rem;color:#94a3b8;margin-top:.3rem">🕐 Bắt đầu: {{ $trip['started_at'] }}</div>
              @endif
            </div>
            @endforeach
          @endif
        </div>
      </div>

      {{-- ══════════ BẢNG CẢNH BÁO GPS LỆCH ══════════ --}}
      <div class="table-wrap">
        <div class="table-header">
          <h3>⚠️ Danh sách giao hàng GPS lệch vượt ngưỡng ({{ $gpsTolerance }}m)</h3>
          @if(count($warningDeliveries) === 0)
            <span style="font-size:.8rem;color:#10b981;font-weight:600">✅ Không có cảnh báo</span>
          @else
            <span style="font-size:.8rem;color:#d97706;font-weight:600">{{ count($warningDeliveries) }} cảnh báo</span>
          @endif
        </div>
        @if(count($warningDeliveries) === 0)
          <div style="text-align:center;padding:3rem;color:#94a3b8">
            <div style="font-size:2rem;margin-bottom:.5rem">✅</div>
            <div>Tất cả giao hàng đều trong ngưỡng GPS cho phép</div>
          </div>
        @else
        <div style="overflow-x:auto">
          <table>
            <thead>
              <tr>
                <th>Mã giao hàng</th>
                <th>Chuyến xe</th>
                <th>Địa chỉ hộ dân</th>
                <th>Khoảng lệch</th>
                <th>Thời gian giao</th>
                <th>Ghi chú lý do</th>
                <th>Bản đồ</th>
              </tr>
            </thead>
            <tbody>
              @foreach($warningDeliveries as $w)
              <tr>
                <td><strong style="color:#0d9488">{{ $w['delivery_code'] }}</strong></td>
                <td>{{ $w['trip_code'] }}</td>
                <td style="font-size:.8rem;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $w['address'] }}">{{ $w['address'] }}</td>
                <td>
                  <span style="background:#fee2e2;color:#dc2626;padding:.2rem .55rem;border-radius:6px;font-size:.78rem;font-weight:700">
                    {{ number_format($w['deviation']) }}m
                  </span>
                </td>
                <td style="font-size:.78rem;color:#64748b">{{ $w['delivered_at'] ?? '—' }}</td>
                <td style="font-size:.78rem;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $w['notes'] }}">
                  {{ $w['notes'] ?: '—' }}
                </td>
                <td>
                  @if($w['hh_lat'] && $w['actual_lat'])
                    <button onclick="showDeviationOnMap({{ $w['hh_lat'] }},{{ $w['hh_lng'] }},{{ $w['actual_lat'] }},{{ $w['actual_lng'] }},'{{ $w['delivery_code'] }}')"
                            style="background:#ede9fe;color:#7c3aed;border:none;border-radius:6px;padding:.25rem .6rem;font-size:.75rem;cursor:pointer;font-weight:600">
                      📍 Xem
                    </button>
                  @else
                    <span style="font-size:.75rem;color:#94a3b8">—</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @endif
      </div>

    </div>{{-- end padding --}}
  </main>
</div>

{{-- Inject dữ liệu --}}
<script>
window.GPS_DATA = {
  shippingTrips:      @json($shippingTrips),
  households:         @json($householdsWithCoords),
  warningDeliveries:  @json($warningDeliveries),
  gpsTolerance:       {{ $gpsTolerance }},
};
</script>
@endsection

@push('scripts')
<style>
  .leaflet-popup-content-wrapper { border-radius: 10px !important; }
  .trip-popup { font-size:.83rem; line-height:1.7; }
  .trip-popup b { color:#0d9488; }
</style>
<script>
// ─── Khởi tạo bản đồ ────────────────────────────────────────────────────────
let gpsMap, tripMarkersMap = {}, devMarkers = [];

document.addEventListener('DOMContentLoaded', () => {
  const D = window.GPS_DATA;

  // 1. Khởi tạo map
  gpsMap = L.map('gpsMap').setView([16.47, 107.59], 7);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(gpsMap);

  // 2. Marker icon factory
  const carIcon = (label) => L.divIcon({
    className: '',
    html: `<div style="background:#3b82f6;color:#fff;border-radius:8px;padding:4px 10px;font-size:11px;font-weight:700;white-space:nowrap;box-shadow:0 2px 10px rgba(0,0,0,.3);border:2px solid #fff">🚛 ${label}</div>`,
    iconAnchor: [35, 20]
  });

  const hhIcon = L.divIcon({
    className: '',
    html: `<div style="width:12px;height:12px;background:#10b981;border-radius:50%;border:2px solid #fff;box-shadow:0 1px 5px rgba(0,0,0,.3)"></div>`,
    iconAnchor: [6, 6]
  });

  const warnIcon = L.divIcon({
    className: '',
    html: `<div style="width:13px;height:13px;background:#f59e0b;border-radius:50%;border:2px solid #fff;box-shadow:0 1px 5px rgba(0,0,0,.3)"></div>`,
    iconAnchor: [6, 6]
  });

  // 3. Vẽ hộ dân
  D.households.forEach(hh => {
    L.marker([hh.lat, hh.lng], { icon: hhIcon })
     .bindPopup(`<div class="trip-popup"><b>${hh.household_name}</b><br>${hh.address}</div>`)
     .addTo(gpsMap);
  });

  // 4. Vẽ xe đang giao
  D.shippingTrips.forEach(t => {
    if (!t.lat || !t.lng) return;
    const marker = L.marker([t.lat, t.lng], { icon: carIcon(t.trip_code) })
      .bindPopup(`
        <div class="trip-popup">
          <b>${t.trip_code}</b><br>
          👤 ${t.driver}<br>
          📦 ${t.warehouse}<br>
          ✅ ${t.done}/${t.total} điểm giao (${t.progress}%)
        </div>
      `)
      .addTo(gpsMap);
    tripMarkersMap[t.trip_id] = marker;
  });

  // 5. Vẽ cảnh báo GPS lệch (vị trí thực tế)
  D.warningDeliveries.forEach(w => {
    if (!w.actual_lat || !w.actual_lng) return;
    L.marker([w.actual_lat, w.actual_lng], { icon: warnIcon })
     .bindPopup(`<div class="trip-popup">⚠️ <b>${w.delivery_code}</b><br>Lệch ${w.deviation}m<br>${w.address}</div>`)
     .addTo(gpsMap);
  });

  // 6. Auto-fit bounds nếu có xe
  if (D.shippingTrips.some(t => t.lat)) {
    const pts = D.shippingTrips.filter(t => t.lat).map(t => [t.lat, t.lng]);
    if (pts.length) gpsMap.fitBounds(L.latLngBounds(pts).pad(0.3));
  }

  // 7. Polling cập nhật vị trí mỗi 15 giây
  setInterval(refreshLivePositions, 15000);
  updateLastRefreshBadge();

  // 8. WebSocket
  if (window.Echo) {
    setupAdminGpsSocket();
  }
});

// ─── Polling live positions ───────────────────────────────────────────────────
function refreshLivePositions() {
  fetch('/admin/gps/live-positions')
    .then(r => r.ok ? r.json() : [])
    .then(trips => {
      trips.forEach(t => {
        if (!t.lat || !t.lng) return;
        const marker = tripMarkersMap[t.trip_id];
        if (marker) {
          marker.setLatLng([t.lat, t.lng]);
          marker.setPopupContent(`
            <div class="trip-popup">
              <b>${t.trip_code}</b><br>
              👤 ${t.driver}<br>
              ✅ ${t.done}/${t.total} điểm giao
            </div>
          `);
        }
      });
      document.getElementById('stat-active').textContent = trips.length;
      updateLastRefreshBadge();
    })
    .catch(() => {});
}

function updateLastRefreshBadge() {
  const el = document.getElementById('last-refresh');
  if (el) el.textContent = 'Cập nhật lúc ' + new Date().toLocaleTimeString('vi-VN');
}

// ─── Tập trung vào chuyến xe ─────────────────────────────────────────────────
function focusTrip(tripId) {
  const marker = tripMarkersMap[tripId];
  if (marker) {
    gpsMap.setView(marker.getLatLng(), 14, { animate: true });
    marker.openPopup();
  } else {
    showGpsToast('⚠️ Chuyến xe chưa có tọa độ GPS', 'warning');
  }

  // Highlight card
  document.querySelectorAll('[id^="trip-card-"]').forEach(el => el.style.borderColor = '');
  const card = document.getElementById(`trip-card-${tripId}`);
  if (card) card.style.borderColor = '#3b82f6';
}

// ─── Hiện sai lệch trên bản đồ ───────────────────────────────────────────────
function showDeviationOnMap(hhLat, hhLng, actLat, actLng, code) {
  // Xoá markers cũ
  devMarkers.forEach(m => gpsMap.removeLayer(m));
  devMarkers = [];

  const hhM = L.circle([hhLat, hhLng], { radius: window.GPS_DATA.gpsTolerance, color: '#10b981', fillOpacity: .12 })
    .bindPopup(`<b>📍 Vị trí hộ dân</b>`).addTo(gpsMap);
  const actM = L.marker([actLat, actLng], {
    icon: L.divIcon({
      className: '',
      html: `<div style="background:#dc2626;color:#fff;border-radius:8px;padding:4px 8px;font-size:11px;font-weight:700;box-shadow:0 2px 8px rgba(0,0,0,.3)">⚠️ ${code}</div>`,
      iconAnchor: [30, 20]
    })
  }).bindPopup(`<b>🚛 Vị trí thực tế: ${code}</b>`).addTo(gpsMap);

  const lineM = L.polyline([[hhLat, hhLng], [actLat, actLng]], { color: '#dc2626', dashArray: '6,4', weight: 2 }).addTo(gpsMap);

  devMarkers = [hhM, actM, lineM];

  gpsMap.fitBounds(L.latLngBounds([[hhLat, hhLng], [actLat, actLng]]).pad(0.5), { animate: true });
  actM.openPopup();
  showGpsToast(`📍 Đang hiển thị sai lệch GPS của ${code}`, 'info');
}

// ─── WebSocket realtime ───────────────────────────────────────────────────────
function setupAdminGpsSocket() {
  fetch('/admin/trips/active-ids')
    .then(r => r.ok ? r.json() : [])
    .then(ids => {
      ids.forEach(tripId => {
        window.Echo.private(`deliveries.${tripId}`)
          .listen('.DeliveryUpdated', (e) => {
            showGpsToast(`🚛 Chuyến ${tripId}: ${e.success_count}/${e.total_count} điểm giao`, 'info');
            // Cập nhật stat
            setTimeout(refreshLivePositions, 1000);
          });
        window.Echo.private(`trips.${tripId}`)
          .listen('.TripStatusUpdated', (e) => {
            if (e.status === 'completed') {
              showGpsToast(`🎉 Chuyến ${e.trip_code} hoàn thành!`, 'success');
              const marker = tripMarkersMap[tripId];
              if (marker) gpsMap.removeLayer(marker);
            }
          });
      });
    })
    .catch(() => {});
}

// ─── Toast ────────────────────────────────────────────────────────────────────
const _gpsToasts = [];
function showGpsToast(msg, type = 'info') {
  const colors = { info:'#0f172a', success:'#059669', warning:'#d97706', error:'#dc2626' };
  const toast = document.createElement('div');
  toast.style.cssText = `
    position:fixed;bottom:${1.5 + _gpsToasts.length * 3.5}rem;right:1.5rem;
    background:${colors[type]||colors.info};color:#fff;
    padding:.6rem 1.2rem;border-radius:9px;font-size:.82rem;font-weight:600;
    z-index:99999;opacity:0;transition:opacity .3s;white-space:nowrap;
    box-shadow:0 4px 20px rgba(0,0,0,.3)
  `;
  toast.textContent = msg;
  document.body.appendChild(toast);
  _gpsToasts.push(toast);
  requestAnimationFrame(() => toast.style.opacity = 1);
  setTimeout(() => {
    toast.style.opacity = 0;
    setTimeout(() => { toast.remove(); _gpsToasts.splice(_gpsToasts.indexOf(toast), 1); }, 300);
  }, 4000);
}
</script>
@endpush

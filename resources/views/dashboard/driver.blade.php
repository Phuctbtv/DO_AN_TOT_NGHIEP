@extends('layouts.app')
@section('title', 'Tài xế - ĐẠI PHÚC')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css"/>
<style>
/* ─── Modal overlay ─────────────────────── */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9000;display:flex;align-items:center;justify-content:center;padding:1rem}
.modal-box{background:#fff;border-radius:20px;width:100%;max-width:600px;max-height:92vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.3);animation:slideUp .25s ease}
@keyframes slideUp{from{transform:translateY(24px);opacity:0}to{transform:translateY(0);opacity:1}}
@keyframes slideDown{from{transform:translateY(-16px);opacity:0}to{transform:translateY(0);opacity:1}}
.modal-hd{display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.4rem;border-bottom:1px solid #e2e8f0;position:sticky;top:0;background:#fff;z-index:1;border-radius:20px 20px 0 0}
.modal-hd h2{font-size:1rem;font-weight:800;color:#0f172a;margin:0}
.modal-bd{padding:1.25rem 1.4rem}
/* ─── Tabs ──────────────────────────────── */
.tab-bar{display:flex;gap:.35rem;background:#f1f5f9;border-radius:10px;padding:.3rem;margin-bottom:1.25rem}
.tab-btn{flex:1;padding:.55rem;border:none;background:transparent;border-radius:8px;font-size:.82rem;font-weight:700;cursor:pointer;color:#64748b;transition:all .2s}
.tab-btn.active{background:#fff;color:#0d9488;box-shadow:0 1px 4px rgba(0,0,0,.1)}
/* ─── QR reader ─────────────────────────── */
#qr-reader{border-radius:10px;overflow:hidden}
#qr-reader video{border-radius:10px}
/* ─── Upload zone ───────────────────────── */
.upload-zone{border:2px dashed #cbd5e1;border-radius:10px;padding:1.5rem;text-align:center;cursor:pointer;transition:all .2s}
.upload-zone:hover,.upload-zone.active{border-color:#0d9488;background:#f0fdfa}
/* ─── Info grid ─────────────────────────── */
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:.6rem;margin-bottom:1rem}
.info-item{background:#f8fafc;border:1px solid #e2e8f0;border-radius:9px;padding:.65rem .85rem}
.info-item .lbl{font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.15rem}
.info-item .val{font-size:.88rem;font-weight:700;color:#0f172a}
/* ─── Distance badge ────────────────────── */
.dist-badge{display:flex;align-items:center;gap:.75rem;padding:.8rem 1rem;border-radius:10px;margin-bottom:.9rem;border:1.5px solid;font-weight:700;font-size:.9rem}
.dist-ok{background:#d1fae5;color:#065f46;border-color:#6ee7b7}
.dist-warn{background:#fef3c7;color:#92400e;border-color:#fcd34d}
.dist-err{background:#fee2e2;color:#991b1b;border-color:#fca5a5}
/* ─── Buttons ───────────────────────────── */
.btn-full{width:100%;padding:.82rem;border:none;border-radius:11px;font-size:.95rem;font-weight:800;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:.5rem}
.btn-teal-solid{background:linear-gradient(135deg,#0d9488,#10b981);color:#fff}
.btn-teal-solid:hover{box-shadow:0 4px 14px rgba(13,148,136,.4);transform:translateY(-1px)}
.btn-teal-solid:disabled{background:#d1fae5;color:#6ee7b7;cursor:not-allowed;transform:none;box-shadow:none}
.btn-orange-solid{background:linear-gradient(135deg,#d97706,#f59e0b);color:#fff}
.btn-orange-solid:hover{box-shadow:0 4px 14px rgba(217,119,6,.4);transform:translateY(-1px)}
.btn-ghost{background:transparent;border:1.5px solid #e2e8f0;color:#64748b;padding:.72rem;border-radius:10px;font-weight:600;cursor:pointer;width:100%;font-size:.88rem;transition:all .2s}
.btn-ghost:hover{background:#f8fafc;border-color:#cbd5e1}
.btn-gps{background:transparent;border:1.5px solid #0d9488;color:#0d9488;padding:.65rem;border-radius:10px;font-weight:700;cursor:pointer;width:100%;font-size:.88rem;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:.5rem;margin-bottom:.9rem}
.btn-gps:hover{background:#0d9488;color:#fff}
/* ─── Delivery row ──────────────────────── */
.drow{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:1rem 1.25rem;margin-bottom:.6rem;transition:box-shadow .2s}
.drow:hover{box-shadow:0 3px 12px rgba(0,0,0,.07)}
.drow.done{opacity:.6;background:#f8fafc}
/* ─── Status pill ───────────────────────── */
.spill{display:inline-block;padding:.2rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;white-space:nowrap}
.s-pending{background:#fef3c7;color:#b45309}
.s-success{background:#d1fae5;color:#065f46}
.s-warning{background:#fff7ed;color:#c2410c}
/* ─── Spinner ───────────────────────────── */
.spin{width:18px;height:18px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spinning .7s linear infinite;display:inline-block}
@keyframes spinning{to{transform:rotate(360deg)}}
/* ─── Toast ─────────────────────────────── */
#toast{position:fixed;bottom:2rem;left:50%;transform:translateX(-50%);background:#0f172a;color:#fff;padding:.65rem 1.4rem;border-radius:999px;font-size:.88rem;font-weight:600;z-index:99999;opacity:0;transition:opacity .3s;pointer-events:none;white-space:nowrap}
#toast.show{opacity:1}
/* ─── Map ───────────────────────────────── */
#gps-mini-map{height:200px;border-radius:9px;border:1px solid #e2e8f0;margin-bottom:.75rem}
#route-map{height:460px;border-radius:0 0 14px 14px}
/* ─── Force reason ──────────────────────── */
.warn-box{background:#fff7ed;border:1.5px solid #fed7aa;border-radius:10px;padding:.9rem;margin-bottom:.9rem}
.warn-box textarea{width:100%;border:1px solid #fed7aa;border-radius:7px;padding:.55rem;font-size:.85rem;resize:vertical;min-height:65px;box-sizing:border-box;font-family:inherit}
/* ─── Route panel ───────────────────────── */
.route-panel{background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;margin-bottom:1rem}
.route-panel-hd{background:linear-gradient(135deg,#0f172a,#1e3a5f);padding:.9rem 1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem}
.route-info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0;border-bottom:1px solid #e2e8f0}
.route-stat{padding:.85rem 1rem;text-align:center;border-right:1px solid #f1f5f9}
.route-stat:last-child{border-right:none}
.route-stat .rs-val{font-size:1.2rem;font-weight:800;color:#0f172a}
.route-stat .rs-lbl{font-size:.7rem;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-top:.1rem}
.next-point-card{padding:1rem 1.25rem;display:flex;align-items:flex-start;gap:.85rem}
.np-avatar{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0}
/* Hide LRM routing control default panel */
.leaflet-routing-container{display:none!important}
.leaflet-routing-alt{display:none!important}
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- ALPINE ROOT — dữ liệu khởi tạo qua window.__DRIVER_INIT__ --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@php
  $tripId = $activeTrip?->id;

  // Chuẩn bị dữ liệu deliveries cho Alpine (server-side render để tránh flash)
  $initialDeliveries = [];
  if ($activeTrip) {
      $initialDeliveries = $activeTrip->deliveries->map(fn($d) => [
          'id'                 => $d->id,
          'delivery_code'      => $d->delivery_code,
          'recipient_name'     => $d->recipient_name,
          'recipient_cccd'     => $d->recipient_cccd,
          'status'             => $d->status,
          'delivered_at'       => $d->delivered_at?->format('H:i d/m/Y'),
          'address'            => $d->household?->address ?? '—',
          'phone'              => $d->household?->phone ?? '',
          'priority_level'     => $d->household?->priority_level ?? 3,
          'proof_image_url'    => $d->proof_image_url,
          'distance_deviation' => $d->distance_deviation,
      ])->values()->toArray();
  }
@endphp

{{-- ⚠️ QUAN TRỌNG: Đặt data trong <script> tránh lỗi " trong HTML attribute --}}
<script>
window.__DRIVER_INIT__ = {
  tripId:     {{ $tripId ?? 'null' }},
  pending:    {{ $stats['pending'] }},
  done:       {{ $stats['done'] }},
  total:      {{ $stats['total'] }},
  deliveries: {!! json_encode($initialDeliveries, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
};
</script>

<div class="dash-layout"
     x-data="driverApp()"
     x-init="init()"
     @keydown.escape.window="closeAll()">

  {{-- SIDEBAR --}}
  <aside class="sidebar">
    <div class="sidebar-logo">🌊 ĐẠI <span>PHÚC</span></div>
    <div class="sidebar-section">NHIỆM VỤ</div>
    <nav class="sidebar-nav">
      <a href="#" :class="tab==='list'?'active':''" @click.prevent="tab='list'; stopQr()">
        <span class="nav-icon">📋</span> Danh sách giao
      </a>
      <a href="#" :class="tab==='map'?'active':''" @click.prevent="tab='map'; initRouteMap()">
        <span class="nav-icon">🗺️</span> Bản đồ tuyến
      </a>
    </nav>

    @if($activeTrip)
    <div class="sidebar-section">TIẾN TRÌNH</div>
    <div style="padding:.5rem 1.25rem">
      <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:.4rem">
        <span style="color:rgba(255,255,255,.55)">Hoàn thành</span>
        <span style="color:#22c55e;font-weight:700">
          <span x-text="success">{{ $stats['done'] }}</span>/<span x-text="total">{{ $stats['total'] }}</span>
        </span>
      </div>
      <div style="background:rgba(255,255,255,.1);border-radius:999px;height:5px;overflow:hidden">
        <div :style="'width:' + (total > 0 ? Math.round(success/total*100) : 0) + '%;height:100%;background:#22c55e;border-radius:999px;transition:width .5s ease'"
             style="height:100%;background:#22c55e;border-radius:999px;transition:width .5s ease"></div>
      </div>
      <div style="font-size:.7rem;color:rgba(255,255,255,.35);margin-top:.3rem;text-align:center">{{ $activeTrip->trip_code }}</div>
    </div>
    @endif

    <div style="margin-top:auto;padding:1rem 1.25rem;border-top:1px solid rgba(255,255,255,.1)">
      <a href="{{ route('home') }}" style="font-size:.8rem;color:rgba(255,255,255,.4);display:block;margin-bottom:.3rem">← Trang chủ</a>
      <form method="POST" action="{{ route('logout') }}">@csrf
        <button style="background:none;border:none;color:rgba(255,255,255,.35);font-size:.8rem;cursor:pointer;padding:0">🚪 Đăng xuất</button>
      </form>
    </div>
  </aside>

  {{-- MAIN --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle'=>'🚛 Dashboard Tài xế'])
    <div style="padding:1.25rem">

      {{-- Stat cards (reactive qua Alpine, cập nhật realtime) --}}
      <div class="dash-stats" style="margin-bottom:1.25rem">
        <div class="dash-card">
          <div class="card-icon" style="background:#fef3c7;color:#d97706">⏳</div>
          <div class="card-value" x-text="pending"></div>
          <div class="card-label">Chờ giao</div>
        </div>
        <div class="dash-card">
          <div class="card-icon" style="background:#dcfce7;color:#16a34a">✅</div>
          <div class="card-value" x-text="success"></div>
          <div class="card-label">Đã giao</div>
        </div>
        <div class="dash-card">
          <div class="card-icon" style="background:#dbeafe;color:#2563eb">🚛</div>
          <div class="card-value" x-text="total"></div>
          <div class="card-label">Tổng điểm</div>
        </div>
        <div class="dash-card">
          <div class="card-icon" style="background:#f0fdfa;color:#0d9488">📍</div>
          <div class="card-value">{{ $gpsTolerance }}m</div>
          <div class="card-label">Ngưỡng GPS</div>
        </div>
      </div>

      {{-- NÚT HOÀN THÀNH CHUYẾN (hiển thị khi tất cả đã giao) --}}
      @if($activeTrip)
      <div x-show="total > 0 && success >= total" x-cloak
           style="background:linear-gradient(135deg,#059669,#10b981);border-radius:14px;padding:1.1rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;color:#fff;animation:slideDown .4s ease">
        <div>
          <div style="font-weight:800;font-size:1rem">🎉 Tất cả điểm đã được giao!</div>
          <div style="font-size:.82rem;opacity:.85;margin-top:.2rem">Chuyến xe đã hoàn thành xuất sắc.</div>
        </div>
        <button onclick="location.reload()" style="background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.4);color:#fff;padding:.55rem 1.1rem;border-radius:9px;font-weight:700;cursor:pointer;font-size:.88rem">
          🔄 Tải lại trang
        </button>
      </div>
      @endif

      @if(!$activeTrip)
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:3rem;text-align:center;color:#94a3b8">
          <div style="font-size:3rem;margin-bottom:.75rem">🚛</div>
          <div style="font-size:1rem;font-weight:700;color:#64748b">Chưa có chuyến xe được phân công</div>
          <div style="font-size:.85rem;margin-top:.3rem">Vui lòng chờ Admin phân công chuyến xe mới.</div>
        </div>
      @else

      {{-- ═══ BANNER TRẠNG THÁI CHUYẾN XE ═══ --}}
      @if($activeTrip->status === 'preparing')
        {{-- Chờ kho xuất hàng --}}
        <div style="background:linear-gradient(135deg,#78350f,#f59e0b);border-radius:14px;padding:1.1rem 1.4rem;margin-bottom:1.25rem;color:#fff;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
          <div>
            <div style="font-size:.7rem;color:rgba(255,255,255,.65);text-transform:uppercase;letter-spacing:.5px">🕐 Đang chờ xuất kho</div>
            <div style="font-size:1.25rem;font-weight:900;font-family:monospace;color:#fef3c7">{{ $activeTrip->trip_code }}</div>
            <div style="font-size:.8rem;margin-top:.2rem;color:rgba(255,255,255,.7)">Chờ thủ kho xác nhận xuất hàng trước khi bắt đầu giao</div>
          </div>
          <div style="text-align:right;font-size:.82rem">
            <div>🏭 {{ $activeTrip->warehouse?->name }}</div>
            <div style="color:rgba(255,255,255,.6)">🚗 {{ $activeTrip->vehicle_info }}</div>
            <div style="margin-top:.4rem">
              <span style="background:rgba(255,255,255,.2);padding:.2rem .7rem;border-radius:999px;font-size:.72rem;font-weight:700">⏳ Chuẩn bị</span>
            </div>
          </div>
        </div>

      @elseif($activeTrip->status === 'exporting')
        {{-- Kho đang xuất — driver nhận hàng rồi bắt đầu --}}
        <div style="background:linear-gradient(135deg,#5b21b6,#7c3aed);border-radius:14px;padding:1.1rem 1.4rem;margin-bottom:1.25rem;color:#fff;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
          <div>
            <div style="font-size:.7rem;color:rgba(255,255,255,.65);text-transform:uppercase;letter-spacing:.5px">📤 Kho đang xuất hàng</div>
            <div style="font-size:1.25rem;font-weight:900;font-family:monospace;color:#e9d5ff">{{ $activeTrip->trip_code }}</div>
            <div style="font-size:.8rem;margin-top:.2rem;color:rgba(255,255,255,.7)">Nhận hàng xong → ấn nút bắt đầu để giao</div>
          </div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.5rem">
            <div style="font-size:.82rem;text-align:right">
              <div>🏭 {{ $activeTrip->warehouse?->name }}</div>
              <div style="color:rgba(255,255,255,.6)">🚗 {{ $activeTrip->vehicle_info }}</div>
            </div>
            {{-- Nút bắt đầu giao hàng --}}
            <form method="POST" action="{{ route('driver.trips.start', $activeTrip) }}"
                  onsubmit="return confirm('Xác nhận đã nhận hàng và bắt đầu giao?')">
              @csrf
              <button type="submit"
                      style="background:#fff;color:#7c3aed;border:none;padding:.55rem 1.2rem;border-radius:9px;font-weight:800;font-size:.88rem;cursor:pointer;display:flex;align-items:center;gap:.35rem">
                🚀 Bắt đầu giao hàng
              </button>
            </form>
          </div>
        </div>

      @else
        {{-- Đang giao (shipping) — giao diện chính --}}
        <div style="background:linear-gradient(135deg,#0f172a,#1e3a5f);border-radius:14px;padding:1.1rem 1.4rem;margin-bottom:1.25rem;color:#fff;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem">
          <div>
            <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px">🚛 Đang giao hàng</div>
            <div style="font-size:1.35rem;font-weight:900;font-family:monospace;color:#5eead4">{{ $activeTrip->trip_code }}</div>
            <div style="font-size:.72rem;color:#64748b;margin-top:.15rem">
              Phân công: {{ $activeTrip->created_at->format('d/m/Y H:i') }}
            </div>
          </div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.5rem">
            <div style="text-align:right;font-size:.82rem">
              <div>🏭 {{ $activeTrip->warehouse?->name }}</div>
              <div style="color:#64748b">🚗 {{ $activeTrip->vehicle_info }}</div>
            </div>
            {{-- Nút kết thúc thủ công nếu chuyến bị stuck --}}
            @php $pendingCount = $activeTrip->deliveries->where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
            <div x-data="{showEnd: false}" style="text-align:right">
              <button @click="showEnd=!showEnd"
                      style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.6);padding:.3rem .7rem;border-radius:7px;font-size:.72rem;cursor:pointer">
                ⋯ Tùy chọn
              </button>
              <div x-show="showEnd" x-cloak style="margin-top:.4rem;background:rgba(0,0,0,.4);border-radius:9px;padding:.5rem;text-align:right">
                <div style="font-size:.72rem;color:#fca5a5;margin-bottom:.4rem">Còn {{ $pendingCount }} điểm chưa giao</div>
                <form method="POST" action="{{ route('driver.trips.start', $activeTrip) }}"
                      onsubmit="return confirm('Xác nhận hoàn thành chuyến xe {{ $activeTrip->trip_code }}?\n({{ $pendingCount }} điểm chưa giao sẽ không được tính)')">
                  @csrf
                  <input type="hidden" name="_force_complete" value="1">
                  <button type="submit"
                          style="background:#ef4444;color:#fff;border:none;padding:.35rem .8rem;border-radius:7px;font-size:.75rem;font-weight:700;cursor:pointer">
                    ⚠️ Kết thúc chuyến
                  </button>
                </form>
              </div>
            </div>
            @endif
          </div>
        </div>
      @endif

      {{-- ── TAB: DANH SÁCH (Realtime qua Alpine + WebSocket) ── --}}
      <div x-show="tab==='list'">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.85rem">
          <div style="font-size:1rem;font-weight:700">📋 Danh sách điểm giao hàng</div>
          <div style="display:flex;align-items:center;gap:.5rem">
            @if($activeTrip->status === 'shipping')
            <div id="ws-status" style="display:flex;align-items:center;gap:.35rem;font-size:.73rem;color:#94a3b8">
              <span id="ws-dot" style="width:7px;height:7px;border-radius:50%;background:#cbd5e1;display:inline-block"></span>
              <span id="ws-label">Đang kết nối...</span>
            </div>
            @endif
            <button @click="loadDeliveries()" style="background:#f1f5f9;border:1px solid #e2e8f0;border-radius:7px;padding:.3rem .7rem;font-size:.75rem;cursor:pointer;color:#475569">🔄 Làm mới</button>
          </div>
        </div>

        @if(in_array($activeTrip->status, ['preparing', 'exporting']))
          {{-- Chưa bắt đầu giao — hiển thị danh sách xem trước nhưng không cho QR --}}
          <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1rem;font-size:.85rem;color:#92400e;display:flex;align-items:center;gap:.5rem">
            <span style="font-size:1.2rem">
              @if($activeTrip->status === 'preparing') ⏳ @else 📤 @endif
            </span>
            <span>
              @if($activeTrip->status === 'preparing')
                Chuyến xe đang <strong>chờ xuất kho</strong>. Chức năng giao hàng sẽ mở sau khi bắt đầu giao.
              @else
                Kho đang <strong>xuất hàng</strong>. Nhận đủ hàng rồi ấn <strong>"Bắt đầu giao hàng"</strong> ở trên.
              @endif
            </span>
          </div>
        @endif

        {{-- Danh sách render bằng Alpine x-for --}}
        <div x-show="deliveries.length === 0 && !wsLoading" style="text-align:center;padding:3rem;color:#94a3b8">
          <div style="font-size:2.5rem">📭</div>
          <div style="margin-top:.5rem">Chưa có điểm giao nào.</div>
        </div>

        <div x-show="wsLoading" style="text-align:center;padding:2rem;color:#94a3b8">
          <div class="spin" style="border-top-color:#0d9488;border-color:rgba(13,148,136,.2);width:24px;height:24px;margin:0 auto .5rem"></div>
          Đang tải danh sách...
        </div>

        <template x-for="delivery in deliveries" :key="delivery.id">
          <div class="drow" :class="{ done: delivery.status==='success' || delivery.status==='warning' }" :id="'row-'+delivery.id">
            <div style="display:flex;gap:.75rem;align-items:flex-start;flex-wrap:wrap">
              <div style="flex:1;min-width:180px">
                <div style="display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;margin-bottom:.3rem">
                  <span style="font-family:monospace;font-size:.72rem;color:#7c3aed;background:#ede9fe;padding:.1rem .4rem;border-radius:4px" x-text="delivery.delivery_code"></span>
                  <span class="spill"
                    :class="delivery.status==='success'?'s-success':(delivery.status==='warning'?'s-warning':'s-pending')"
                    x-text="delivery.status==='success'?'✅ Đã giao':(delivery.status==='warning'?'⚠️ Cảnh báo':'⏳ Chờ giao')">
                  </span>
                  <span style="padding:.1rem .45rem;border-radius:999px;font-size:.68rem;font-weight:700"
                    :style="delivery.priority_level==1?'background:#fee2e2;color:#dc2626':(delivery.priority_level==2?'background:#fef3c7;color:#d97706':'background:#d1fae5;color:#059669')"
                    x-text="delivery.priority_level==1?'🔴 Cấp 1':(delivery.priority_level==2?'🟡 Cấp 2':'🟢 Thường')">
                  </span>
                </div>
                <div style="font-weight:700;color:#0f172a" x-text="delivery.recipient_name"></div>
                <div style="font-size:.78rem;color:#64748b;margin-top:.15rem" x-text="'CCCD: ' + delivery.recipient_cccd"></div>
                <div style="font-size:.8rem;color:#475569;margin-top:.15rem" x-text="'📍 ' + (delivery.address || '—')"></div>
                <div x-show="delivery.phone" style="font-size:.78rem;color:#64748b;margin-top:.1rem" x-text="'📞 ' + delivery.phone"></div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.4rem;min-width:110px">
                <template x-if="delivery.status==='success' || delivery.status==='warning'">
                  <div style="text-align:right;font-size:.78rem">
                    <div style="color:#059669;font-weight:700" x-text="'Giao ' + (delivery.delivered_at || '')"></div>
                    <div x-show="delivery.distance_deviation !== null"
                         :style="delivery.distance_deviation <= {{ $gpsTolerance }} ? 'color:#059669;font-size:.72rem' : 'color:#d97706;font-size:.72rem'"
                         x-text="'📏 ' + Math.round(delivery.distance_deviation || 0) + 'm lệch'">
                    </div>
                    <a x-show="delivery.proof_image_url" :href="delivery.proof_image_url" target="_blank"
                       style="font-size:.75rem;color:#3b82f6;text-decoration:none">📸 Ảnh bằng chứng</a>
                  </div>
                </template>
                <template x-if="delivery.status==='pending'">
                  @if($activeTrip->status === 'shipping')
                  <button class="btn btn-teal btn-sm" @click="openQrModal(delivery.id)">
                    📷 Quét QR
                  </button>
                  @else
                  <span style="font-size:.72rem;color:#f59e0b;font-weight:600">🔒 Chờ bắt đầu</span>
                  @endif
                </template>
              </div>
            </div>
          </div>
        </template>
      </div>

      {{-- ── TAB: BẢN ĐỒ ── --}}
      <div x-show="tab==='map'">
        <div style="font-size:1rem;font-weight:700;margin-bottom:.85rem">🗺️ Bản đồ tuyến đường</div>

        {{-- Next destination panel --}}
        <div class="route-panel">
          <div class="route-panel-hd">
            <div>
              <div style="font-size:.68rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px">Điểm giao tiếp theo</div>
              <div id="rp-name" style="font-weight:700;color:#fff;font-size:.95rem;margin-top:.2rem">—</div>
            </div>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap">
              <button id="btn-nav-maps" onclick="openGoogleMaps()" style="display:none;background:#4285f4;color:#fff;border:none;border-radius:8px;padding:.45rem .9rem;font-size:.8rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:.35rem">
                🚗 Chỉ đường Google Maps
              </button>
              <button onclick="refreshRoute()" style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:8px;padding:.45rem .9rem;font-size:.8rem;font-weight:700;cursor:pointer">
                🔄 Làm mới
              </button>
            </div>
          </div>

          {{-- Stats row --}}
          <div class="route-info-grid">
            <div class="route-stat">
              <div class="rs-val" id="rp-dist-air">—</div>
              <div class="rs-lbl">Đường chim bay</div>
            </div>
            <div class="route-stat">
              <div class="rs-val" id="rp-dist-road">—</div>
              <div class="rs-lbl">Đường đi thực</div>
            </div>
            <div class="route-stat">
              <div class="rs-val" id="rp-eta">—</div>
              <div class="rs-lbl">Thời gian ước tính</div>
            </div>
          </div>

          {{-- Destination detail --}}
          <div class="next-point-card">
            <div class="np-avatar">📍</div>
            <div style="flex:1">
              <div style="font-weight:700;color:#0f172a" id="rp-fullname">Đang tìm điểm giao tiếp theo...</div>
              <div style="font-size:.82rem;color:#64748b;margin-top:.2rem" id="rp-addr"></div>
              <div style="display:flex;gap:.85rem;margin-top:.35rem;flex-wrap:wrap">
                <span style="font-size:.78rem;color:#94a3b8" id="rp-phone"></span>
                <span style="font-size:.78rem" id="rp-status-pill"></span>
              </div>
            </div>
          </div>

          {{-- Loading state --}}
          <div id="rp-loading" style="padding:1rem 1.25rem;font-size:.85rem;color:#94a3b8;display:flex;align-items:center;gap:.5rem">
            <div class="spin" style="border-top-color:#94a3b8;border-color:rgba(148,163,184,.25);width:14px;height:14px"></div>
            Đang lấy vị trí và vẽ tuyến đường...
          </div>
        </div>

        {{-- Map --}}
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden">
          <div style="background:#f8fafc;padding:.55rem 1rem;display:flex;align-items:center;gap:1.25rem;font-size:.75rem;color:#64748b;border-bottom:1px solid #f1f5f9">
            <span><span style="display:inline-block;width:11px;height:11px;background:#3b82f6;border-radius:50%;margin-right:.3rem;vertical-align:middle"></span>Vị trí của bạn</span>
            <span><span style="display:inline-block;width:11px;height:11px;background:#ef4444;border-radius:50%;margin-right:.3rem;vertical-align:middle"></span>Điểm tiếp theo</span>
            <span><span style="display:inline-block;width:11px;height:11px;background:#f59e0b;border-radius:50%;margin-right:.3rem;vertical-align:middle"></span>Điểm còn lại</span>
            <span><span style="display:inline-block;width:11px;height:11px;background:#10b981;border-radius:50%;margin-right:.3rem;vertical-align:middle"></span>Đã giao</span>
          </div>
          <div id="route-map"></div>
        </div>
      </div>

      @endif {{-- activeTrip --}}
    </div>
  </main>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- MODAL 1: QUÉT QR                                          --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div id="qr-modal" class="modal-overlay" style="display:none" onclick="if(event.target===this)window.driverInstance.closeAll()">
  <div class="modal-box">
    <div class="modal-hd">
      <h2>📷 Quét mã QR</h2>
      <button onclick="window.driverInstance.closeAll()" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:#64748b;line-height:1">✕</button>
    </div>
    <div class="modal-bd">

      {{-- 2 TAB BUTTONS --}}
      <div class="tab-bar">
        <button class="tab-btn" id="qtab-cam"   onclick="switchQrTab('cam')"   >📷 Quét QR</button>
        <button class="tab-btn" id="qtab-img"   onclick="switchQrTab('img')"   >🖼️ Upload ảnh</button>
      </div>

      {{-- Tab 1: Camera --}}
      <div id="qtab-cam-pane">
        <div id="qr-reader" style="width:100%"></div>
        <div style="font-size:.8rem;color:#94a3b8;text-align:center;margin-top:.65rem">
          📱 Hướng camera vào mã QR trên thẻ hộ dân
        </div>
      </div>

      {{-- Tab 2: Upload ảnh QR --}}
      <div id="qtab-img-pane" style="display:none">
        <label class="upload-zone" id="qr-img-drop" for="qr-img-input">
          <div id="qr-img-placeholder">
            <div style="font-size:2.5rem;margin-bottom:.4rem">🖼️</div>
            <div style="font-weight:600;color:#475569">Nhấn để chọn ảnh chứa mã QR</div>
            <div style="font-size:.78rem;color:#94a3b8;margin-top:.2rem">PNG, JPG — Tối đa 8MB</div>
          </div>
          <div id="qr-img-preview" style="display:none">
            <img id="qr-img-thumb" style="max-height:200px;max-width:100%;border-radius:8px;object-fit:contain" src="" alt="">
          </div>
        </label>
        <input type="file" id="qr-img-input" accept="image/*" style="display:none" onchange="handleQrImageUpload(this)">
        <button class="btn-full btn-teal-solid" style="margin-top:.75rem" onclick="readQrFromImage()" id="btn-read-qr">
          🔍 Đọc QR từ ảnh
        </button>
      </div>



      {{-- Kết quả lỗi --}}
      <div id="qr-error" style="display:none;margin-top:.75rem;background:#fee2e2;border:1px solid #fca5a5;border-radius:9px;padding:.75rem 1rem;font-size:.85rem;color:#991b1b;font-weight:600"></div>

    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- MODAL 2: XÁC NHẬN GIAO HÀNG                               --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div id="confirm-modal" class="modal-overlay" style="display:none" onclick="if(event.target===this)closeConfirmModal()">
  <div class="modal-box">
    <div class="modal-hd">
      <h2>✅ Xác nhận giao hàng</h2>
      <button onclick="closeConfirmModal()" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:#64748b;line-height:1">✕</button>
    </div>
    <div class="modal-bd">

      {{-- Thông tin hộ dân --}}
      <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.5rem">👤 Thông tin hộ dân</div>
      <div class="info-grid">
        <div class="info-item"><div class="lbl">Họ tên</div><div class="val" id="cf-name">—</div></div>
        <div class="info-item"><div class="lbl">CCCD</div><div class="val" id="cf-cccd" style="font-family:monospace">—</div></div>
        <div class="info-item" style="grid-column:1/-1"><div class="lbl">Địa chỉ</div><div class="val" id="cf-addr" style="font-weight:400;font-size:.83rem;color:#475569">—</div></div>
        <div class="info-item"><div class="lbl">SĐT</div><div class="val" id="cf-phone">—</div></div>
        <div class="info-item"><div class="lbl">Mã giao</div><div class="val" id="cf-code" style="font-family:monospace;color:#7c3aed;font-size:.82rem">—</div></div>
      </div>

      {{-- Bản đồ GPS --}}
      <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.5rem">🗺️ Xác thực vị trí</div>
      <div id="gps-mini-map"></div>
      <div style="display:flex;gap:1rem;font-size:.73rem;color:#64748b;margin-bottom:.65rem">
        <span>🟢 Vị trí đăng ký</span><span>🔴 Vị trí thực tế của bạn</span>
      </div>

      {{-- Khoảng cách --}}
      <div id="dist-badge" class="dist-badge dist-warn" style="font-size:.85rem">
        <span style="font-size:1.35rem" id="dist-icon">⏳</span>
        <div><div id="dist-text">Chưa lấy vị trí GPS</div><div style="font-size:.75rem;opacity:.75" id="dist-sub"></div></div>
      </div>

      {{-- Nút lấy GPS --}}
      <button class="btn-gps" id="btn-get-gps" onclick="getDriverGps()">
        📍 Lấy vị trí hiện tại
      </button>

      {{-- Upload ảnh bằng chứng --}}
      <div style="font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.5rem">📸 Ảnh bằng chứng <span style="color:#ef4444">*</span></div>
      <label class="upload-zone" id="proof-zone" for="proof-input">
        <div id="proof-placeholder">
          <div style="font-size:2rem;margin-bottom:.3rem">📷</div>
          <div style="font-size:.85rem;font-weight:600;color:#64748b">Chụp ảnh hoặc chọn từ thư viện</div>
          <div style="font-size:.73rem;color:#94a3b8;margin-top:.15rem">PNG, JPG — Tối đa 8MB (bắt buộc)</div>
        </div>
        <div id="proof-preview" style="display:none">
          <img id="proof-thumb" style="max-height:180px;max-width:100%;border-radius:8px;object-fit:contain" src="" alt="">
          <div style="font-size:.78rem;color:#059669;font-weight:700;margin-top:.4rem">✅ Đã chọn ảnh bằng chứng</div>
        </div>
      </label>
      <input type="file" id="proof-input" accept="image/*" capture="environment" style="display:none" onchange="handleProofSelect(this)">

      {{-- Cảnh báo GPS lệch --}}
      <div id="force-box" class="warn-box" style="display:none;margin-top:.9rem">
        <div style="font-size:.85rem;font-weight:700;color:#d97706;margin-bottom:.4rem">⚠️ GPS lệch vượt ngưỡng — Nhập lý do để tiếp tục</div>
        <textarea id="force-reason" placeholder="Ví dụ: Hộ dân ra đầu ngõ nhận hàng, GPS yếu do mưa..."></textarea>
      </div>

      {{-- Nút hành động --}}
      <div style="display:flex;gap:.65rem;margin-top:1rem">
        <button class="btn-ghost" onclick="closeConfirmModal()" style="flex:0 0 auto;width:auto;padding:.75rem 1.1rem">Huỷ</button>
        <button class="btn-full btn-teal-solid" id="btn-submit-ok" onclick="submitDelivery(false)" disabled style="flex:1">
          ✅ Xác nhận đã giao
        </button>
      </div>
      <button class="btn-full btn-orange-solid" id="btn-submit-force" onclick="submitDelivery(true)" style="display:none;margin-top:.55rem">
        ⚠️ Vẫn xác nhận dù GPS lệch
      </button>

    </div>
  </div>
</div>

{{-- Toast --}}
<div id="toast"></div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
// ── CONFIG ──────────────────────────────────────────────────────
const GPS_TOL        = {{ $gpsTolerance }};
const URL_CONFIRM    = '{{ url("driver/deliveries") }}';
const URL_QR_LOOKUP  = '{{ route("driver.deliveries.qrLookup") }}';
const URL_CCCD_LOOKUP= '{{ route("driver.deliveries.cccdLookup") }}';
const CSRF           = '{{ csrf_token() }}';
// Dữ liệu bản đồ — có thể thay đổi realtime (không dùng const)
let routeMapData = {!! $routeMapJson !!};

// ── STATE ────────────────────────────────────────────────────────
let currentDeliveryId = null;
let hhLat = null, hhLng = null;
let drvLat = null, drvLng = null;
let distance = 0;
let miniMap = null, hhMarker = null, drvMarker = null;
let routeMap = null, drvRouteMarker = null;
let qrInstance = null;
let qrTab = 'cam';
let qrImgFile = null;

// Icons
const greenIcon = L.divIcon({
  className:'',
  html:'<div style="width:16px;height:16px;background:#10b981;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3)"></div>',
  iconSize:[16,16],iconAnchor:[8,8]
});
const redIcon = L.divIcon({
  className:'',
  html:'<div style="width:16px;height:16px;background:#ef4444;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3)"></div>',
  iconSize:[16,16],iconAnchor:[8,8]
});

// ── ALPINE COMPONENT ─────────────────────────────────────────────
function driverApp(){
  // Đọc dữ liệu từ window.__DRIVER_INIT__ (được set bởi PHP trong <script> tag)
  // Tránh lỗi HTML attribute bị vỡ do dấu " trong json_encode
  const _init = window.__DRIVER_INIT__ || {};
  const tripId          = _init.tripId    ?? null;
  const initPending     = _init.pending   ?? 0;
  const initDone        = _init.done      ?? 0;
  const initTotal       = _init.total     ?? 0;
  const initDeliveries  = Array.isArray(_init.deliveries) ? _init.deliveries : [];

  return {
    tab: 'list',
    tripId: tripId,
    pending: initPending,
    success: initDone,
    total:   initTotal,
    // Dữ liệu deliveries từ server-side PHP (tránh flash + API call khi init)
    deliveries: initDeliveries,
    wsLoading: false,
    showCompleteBtn: (initDone >= initTotal && initTotal > 0),

    init(){
      window.driverInstance = this;
      if(this.tripId){
        // KHÔNG gọi loadDeliveries() khi init vì đã có dữ liệu từ server
        // Chỉ lắng nghe WebSocket để nhận cập nhật realtime
        this.listenWebSocket();
      }
    },

    // Load stats từ API (chỉ gọi khi cần refresh)
    loadStats(){
      if(!this.tripId) return;
      fetch(`/driver/trips/${this.tripId}/stats`)
        .then(r => r.json())
        .then(data => {
          this.pending = data.pending_count ?? this.pending;
          this.success = data.success_count ?? this.success;
          this.total   = data.total_count   ?? this.total;
        }).catch(()=>{});
    },

    // Load danh sách deliveries từ API (gọi khi WebSocket báo có thay đổi)
    loadDeliveries(){
      if(!this.tripId) return;
      this.wsLoading = true;
      fetch(`/driver/trips/${this.tripId}/deliveries`)
        .then(r => r.json())
        .then(data => {
          this.deliveries = Array.isArray(data) ? data : [];
          this.wsLoading  = false;
        }).catch(()=>{ this.wsLoading = false; });
    },

    // Lắng nghe WebSocket realtime
    listenWebSocket(){
      if(!this.tripId || !window.Echo) return;
      const self = this;

      // Lắng nghe cập nhật delivery
      window.Echo.private(`deliveries.${this.tripId}`)
        .listen('.DeliveryUpdated', (e) => {
          self.pending = e.pending_count;
          self.success = e.success_count;
          self.total   = e.total_count;
          self.loadDeliveries();
          self.setWsStatus('connected');
          if(self.success >= self.total && self.total > 0){
            self.showCompleteBtn = true;
          }
        })
        .subscribed(() => self.setWsStatus('connected'))
        .error(() => self.setWsStatus('error'));

      // Lắng nghe trạng thái trip hoàn thành
      window.Echo.private(`trips.${this.tripId}`)
        .listen('.TripStatusUpdated', (e) => {
          if(e.status === 'completed'){
            self.showCompleteBtn = true;
            showToast('🎉 Chuyến xe ' + e.trip_code + ' đã hoàn thành!', 5000);
            setTimeout(() => location.reload(), 3000);
          }
        });
    },

    setWsStatus(state){
      const dot   = document.getElementById('ws-dot');
      const label = document.getElementById('ws-label');
      if(!dot || !label) return;
      const cfg = {
        connected: ['#10b981', 'Realtime đang hoạt động'],
        error:     ['#ef4444', 'Mất kết nối WebSocket'],
        loading:   ['#f59e0b', 'Đang kết nối...'],
      };
      const [color, text] = cfg[state] || cfg.loading;
      dot.style.background = color;
      label.textContent    = text;
    },

    openQrModal(deliveryId){
      currentDeliveryId = deliveryId;
      document.getElementById('qr-error').style.display='none';
      document.getElementById('qr-modal').style.display='flex';
      document.body.style.overflow='hidden';
      switchQrTab('cam');
    },
    closeAll(){
      this.stopQr();
      document.getElementById('qr-modal').style.display='none';
      document.getElementById('confirm-modal').style.display='none';
      document.body.style.overflow='';
    },
    stopQr(){
      if(qrInstance){
        qrInstance.stop().catch(()=>{});
        qrInstance=null;
      }
    },
    initRouteMap(){
      setTimeout(()=>{
        if(routeMap){routeMap.invalidateSize();setTimeout(()=>autoDrawRoute(),200);return;}
        const el=document.getElementById('route-map');
        if(!el) return;
        routeMap=L.map('route-map').setView([16.0544,108.2022],13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
          attribution:'&copy; OpenStreetMap',maxZoom:19
        }).addTo(routeMap);
        // Vẽ tất cả điểm giao với marker có thể tham chiếu
        drawAllMarkers();
        // Tự động lấy GPS và vẽ tuyến
        autoDrawRoute();
      },300);
    },
  };
}

// ── QR TAB SWITCH ────────────────────────────────────────────────
function switchQrTab(tab){
  qrTab=tab;
  ['cam','img'].forEach(t=>{
    document.getElementById(`qtab-${t}-pane`).style.display= t===tab?'block':'none';
    document.getElementById(`qtab-${t}`).classList.toggle('active', t===tab);
  });
  if(tab==='cam') startQrCam();
  else window.driverInstance?.stopQr();
}

function startQrCam(){
  if(qrInstance) return;
  const el=document.getElementById('qr-reader');
  if(!el) return;
  qrInstance=new Html5Qrcode('qr-reader');
  qrInstance.start(
    {facingMode:'environment'},
    {fps:10,qrbox:{width:240,height:240}},
    async txt=>{ await handleQrResult(txt); },
    ()=>{}
  ).catch(e=>console.warn('[QR cam]',e));
}

// ── QR IMAGE ─────────────────────────────────────────────────────
function handleQrImageUpload(input){
  if(!input.files.length) return;
  qrImgFile=input.files[0];
  const rd=new FileReader();
  rd.onload=e=>{
    document.getElementById('qr-img-thumb').src=e.target.result;
    document.getElementById('qr-img-placeholder').style.display='none';
    document.getElementById('qr-img-preview').style.display='';
    document.getElementById('qr-img-drop').classList.add('active');
  };
  rd.readAsDataURL(qrImgFile);
}

async function readQrFromImage(){
  if(!qrImgFile){showQrError('Vui lòng chọn ảnh trước.');return;}
  const btn=document.getElementById('btn-read-qr');
  btn.disabled=true; btn.textContent='⏳ Đang đọc...';
  try{
    const scanner=new Html5Qrcode('qr-img-decode-tmp');
    const res=await scanner.scanFile(qrImgFile,false);
    scanner.clear();
    await handleQrResult(res);
  }catch(e){
    showQrError('Không đọc được mã QR từ ảnh. Thử ảnh khác hoặc nhập CCCD.');
  }finally{
    btn.disabled=false; btn.textContent='🔍 Đọc QR từ ảnh';
  }
}

// Ẩn placeholder cho html5-qrcode image scanner
document.addEventListener('DOMContentLoaded',()=>{
  const d=document.createElement('div');
  d.id='qr-img-decode-tmp'; d.style.display='none';
  document.body.appendChild(d);
});

// ── CCCD LOOKUP ──────────────────────────────────────────────────
async function lookupByCccd(){
  const cccd=document.getElementById('cccd-input').value.trim();
  if(cccd.length!==12){showQrError('CCCD phải đủ 12 chữ số.');return;}
  const btn=document.getElementById('btn-cccd-lookup');
  btn.disabled=true; btn.innerHTML='<div class="spin"></div> Đang tra cứu...';
  try{
    const resp=await fetch(URL_CCCD_LOOKUP,{
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body:JSON.stringify({cccd}),
    });
    const data=await resp.json();
    if(resp.ok&&data.found) openConfirmModal(data);
    else showQrError(data.error||'Không tìm thấy đơn giao hàng.');
  }catch(e){showQrError('Lỗi kết nối.');}
  finally{btn.disabled=false;btn.textContent='🔍 Tra cứu';}
}

// ── QR RESULT HANDLER ────────────────────────────────────────────
async function handleQrResult(qrText){
  window.driverInstance?.stopQr();
  showQrError('');
  try{
    const resp=await fetch(URL_QR_LOOKUP,{
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body:JSON.stringify({qr_code:qrText}),
    });
    const data=await resp.json();
    if(resp.ok&&data.found){
      openConfirmModal(data);
    }else{
      showQrError(data.error||'Mã QR không hợp lệ.');
      setTimeout(()=>{if(qrTab==='cam')startQrCam();},1500);
    }
  }catch(e){
    showQrError('Lỗi kết nối khi tra cứu QR.');
    setTimeout(()=>{if(qrTab==='cam')startQrCam();},1500);
  }
}

function showQrError(msg){
  const el=document.getElementById('qr-error');
  if(!msg){el.style.display='none';return;}
  el.textContent=msg; el.style.display='block';
}

// ── OPEN CONFIRM MODAL ───────────────────────────────────────────
function openConfirmModal(data){
  // Đóng QR modal
  window.driverInstance?.stopQr();
  document.getElementById('qr-modal').style.display='none';

  // Lưu state
  currentDeliveryId = data.delivery_id;
  hhLat = data.household_lat;
  hhLng = data.household_lng;
  drvLat=null; drvLng=null; distance=0;

  // Fill thông tin
  document.getElementById('cf-name').textContent  = data.recipient_name||'—';
  document.getElementById('cf-cccd').textContent  = data.recipient_cccd||'—';
  document.getElementById('cf-addr').textContent  = data.address||'—';
  document.getElementById('cf-phone').textContent = data.phone||'—';
  document.getElementById('cf-code').textContent  = data.delivery_code||'—';

  // Reset GPS UI
  setDistBadge('warn','⏳','Chưa lấy vị trí GPS','Nhấn "Lấy vị trí hiện tại" để tiếp tục');
  document.getElementById('btn-submit-ok').disabled=true;
  document.getElementById('btn-submit-force').style.display='none';
  document.getElementById('force-box').style.display='none';
  document.getElementById('force-reason').value='';

  // Reset proof
  document.getElementById('proof-input').value='';
  document.getElementById('proof-placeholder').style.display='';
  document.getElementById('proof-preview').style.display='none';
  document.getElementById('proof-zone').classList.remove('active');

  // Show modal
  document.getElementById('confirm-modal').style.display='flex';
  document.body.style.overflow='hidden';

  // Init mini map
  setTimeout(initMiniMap, 300);
}

function closeConfirmModal(){
  document.getElementById('confirm-modal').style.display='none';
  document.body.style.overflow='';
  if(miniMap){miniMap.remove();miniMap=null;hhMarker=null;drvMarker=null;}
}

// ── MINI MAP ─────────────────────────────────────────────────────
function initMiniMap(){
  if(miniMap){miniMap.remove();miniMap=null;}
  const center=(hhLat&&hhLng)?[hhLat,hhLng]:[16.0544,108.2022];
  miniMap=L.map('gps-mini-map').setView(center,15);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(miniMap);
  if(hhLat&&hhLng){
    hhMarker=L.marker([hhLat,hhLng],{icon:greenIcon}).addTo(miniMap).bindPopup('🏠 Vị trí hộ dân');
  }
}

// ── GPS ──────────────────────────────────────────────────────────
function getDriverGps(){
  const btn=document.getElementById('btn-get-gps');
  btn.disabled=true; btn.innerHTML='<div class="spin" style="border-top-color:#0d9488;border-color:rgba(13,148,136,.2);width:16px;height:16px"></div> Đang định vị...';
  if(!navigator.geolocation){
    setDistBadge('err','❌','Trình duyệt không hỗ trợ GPS','');
    btn.disabled=false; btn.innerHTML='📍 Lấy vị trí hiện tại';
    return;
  }
  navigator.geolocation.getCurrentPosition(
    pos=>{
      drvLat=pos.coords.latitude; drvLng=pos.coords.longitude;
      btn.disabled=false; btn.innerHTML='🔄 Làm mới vị trí';
      // Update map
      if(miniMap){
        if(drvMarker) drvMarker.remove();
        drvMarker=L.marker([drvLat,drvLng],{icon:redIcon}).addTo(miniMap).bindPopup('🚛 Vị trí của bạn');
        if(hhLat&&hhLng){
          miniMap.fitBounds([[hhLat,hhLng],[drvLat,drvLng]],{padding:[30,30]});
        } else {
          miniMap.setView([drvLat,drvLng],15);
        }
      }
      // Tính khoảng cách
      if(hhLat&&hhLng){
        distance=haversine(hhLat,hhLng,drvLat,drvLng);
        updateDistUI(distance);
      } else {
        setDistBadge('ok','ℹ️','Hộ dân chưa có tọa độ GPS','Vẫn có thể xác nhận giao hàng');
        tryEnableSubmit();
      }
    },
    err=>{
      btn.disabled=false; btn.innerHTML='📍 Thử lại';
      setDistBadge('err','❌','Không lấy được GPS',err.message);
      // Cho phép force confirm khi không lấy được GPS
      document.getElementById('force-box').style.display='';
      document.getElementById('btn-submit-force').style.display='flex';
    },
    {enableHighAccuracy:true,timeout:15000,maximumAge:0}
  );
}

function haversine(lat1,lng1,lat2,lng2){
  const R=6371000,r=x=>x*Math.PI/180;
  const dL=r(lat2-lat1),dG=r(lng2-lng1);
  const a=Math.sin(dL/2)**2+Math.cos(r(lat1))*Math.cos(r(lat2))*Math.sin(dG/2)**2;
  return R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
}

function updateDistUI(dist){
  const m=Math.round(dist);
  const str=dist<1000?`${m} mét`:`${(dist/1000).toFixed(2)} km`;
  if(dist<=GPS_TOL){
    setDistBadge('ok','✅',`Trong vùng giao hàng — ${str}`,`${m}m ≤ ngưỡng ${GPS_TOL}m`);
    document.getElementById('btn-submit-force').style.display='none';
    document.getElementById('force-box').style.display='none';
    tryEnableSubmit();
  } else {
    setDistBadge('err','⚠️',`Cách điểm giao ${str}`,`Vượt ngưỡng ${GPS_TOL}m — cần xác nhận thêm`);
    document.getElementById('btn-submit-ok').disabled=true;
    document.getElementById('btn-submit-force').style.display='flex';
    document.getElementById('force-box').style.display='';
  }
}

function setDistBadge(type,icon,text,sub){
  const b=document.getElementById('dist-badge');
  b.className='dist-badge dist-'+type;
  document.getElementById('dist-icon').textContent=icon;
  document.getElementById('dist-text').textContent=text;
  document.getElementById('dist-sub').textContent=sub;
}

function tryEnableSubmit(){
  const hasProof=document.getElementById('proof-input').files.length>0;
  document.getElementById('btn-submit-ok').disabled=!hasProof;
}

// ── PROOF IMAGE ──────────────────────────────────────────────────
function handleProofSelect(input){
  if(!input.files.length) return;
  const rd=new FileReader();
  rd.onload=e=>{
    document.getElementById('proof-thumb').src=e.target.result;
    document.getElementById('proof-placeholder').style.display='none';
    document.getElementById('proof-preview').style.display='';
    document.getElementById('proof-zone').classList.add('active');
    // Nếu GPS đã lấy và trong ngưỡng → enable nút
    if((drvLat&&drvLng&&distance<=GPS_TOL)||(hhLat===null)){
      document.getElementById('btn-submit-ok').disabled=false;
    }
  };
  rd.readAsDataURL(input.files[0]);
}

// ── SUBMIT CONFIRM ───────────────────────────────────────────────
async function submitDelivery(isForce){
  if(!currentDeliveryId) return;
  const proofInput=document.getElementById('proof-input');
  if(!proofInput.files.length){showToast('⚠️ Vui lòng chụp ảnh bằng chứng!');return;}
  if(isForce){
    const reason=document.getElementById('force-reason').value.trim();
    if(!reason){showToast('⚠️ Nhập lý do khi GPS lệch vượt ngưỡng!');document.getElementById('force-reason').focus();return;}
  }
  const btnOk=document.getElementById('btn-submit-ok');
  const btnF =document.getElementById('btn-submit-force');
  btnOk.disabled=true; btnF.disabled=true;
  btnOk.innerHTML='<div class="spin"></div> Đang xử lý...';

  const fd=new FormData();
  fd.append('_token',CSRF);
  fd.append('proof_image',proofInput.files[0]);
  fd.append('force_confirm',isForce?'1':'0');
  if(drvLat!==null) fd.append('actual_lat',drvLat);
  if(drvLng!==null) fd.append('actual_lng',drvLng);
  fd.append('distance_deviation',Math.round(distance));
  if(isForce) fd.append('force_reason',document.getElementById('force-reason').value.trim());

  try{
    const resp=await fetch(`${URL_CONFIRM}/${currentDeliveryId}/confirm`,{method:'POST',body:fd});
    const data=await resp.json();
    if(resp.ok&&data.success){
      showToast(data.message,3500);
      closeConfirmModal();
      // Cập nhật ngay trong Alpine array (không cần gọi API)
      const inst = window.driverInstance;
      if(inst){
        // --- Cập nhật stats (trigger Alpine reactivity đúng cách) ---
        if(data.stats){
          inst.pending = data.stats.pending_count;
          inst.success = data.stats.success_count;
          inst.total   = data.stats.total_count;
          if(data.stats.success_count >= data.stats.total_count && data.stats.total_count > 0){
            inst.showCompleteBtn = true;
          }
        }
        // --- Cập nhật delivery trong array ---
        const idx = inst.deliveries.findIndex(d => d.id === currentDeliveryId);
        if(idx > -1){
          // Dùng splice để Alpine proxy detect thay đổi trong array
          const updated = { ...inst.deliveries[idx],
            status:             data.status,
            delivered_at:       data.delivered_at || '',
            proof_image_url:    data.proof_url || null,
            distance_deviation: distance || null,
          };
          inst.deliveries.splice(idx, 1, updated);
        }
        // --- Realtime Map Update ---
        const mapIdx = routeMapData.findIndex(d => d.id === currentDeliveryId);
        if(mapIdx > -1) routeMapData[mapIdx].status = data.status;
        if(routeMap) updateRouteMap();
      }
      if(data.trip_done) setTimeout(()=>{location.reload();},3000);
    } else {
      showToast('❌ '+(data.error||'Có lỗi xảy ra.'));
      btnOk.disabled=false; btnF.disabled=false;
      btnOk.innerHTML='✅ Xác nhận đã giao';
    }
  }catch(e){
    showToast('❌ Lỗi kết nối.');
    btnOk.disabled=false; btnF.disabled=false;
    btnOk.innerHTML='✅ Xác nhận đã giao';
  }
}

// ── UPDATE UI ROW ─────────────────────────────────────────────────
function markRowDone(id,status,deliveredAt,dist){
  const row=document.getElementById(`row-${id}`);
  if(!row) return;
  row.classList.add('done');
  const spill=row.querySelector('.spill');
  if(spill){
    spill.className='spill '+(status==='success'?'s-success':'s-warning');
    spill.textContent=status==='success'?'✅ Đã giao':'⚠️ Cảnh báo';
  }
  const btn=row.querySelector('.btn');
  if(btn) btn.remove();
}

// ── ROUTE MAP STATE ────────────────────────────────────────
let routingControl  = null;   // LRM routing control
let myLocMarker     = null;   // Marker vị trí tài xế
let nextDestLat     = null;   // Điểm tiếp theo
let nextDestLng     = null;
let mapMarkers      = {};     // {deliveryId: leafletMarker}
let nextDestMarker  = null;   // Marker đỏ điểm tiếp theo

// ── VẼ TẤT CẢ MARKERS ─────────────────────────────────────
function drawAllMarkers(){
  if(!routeMap) return;
  // Xóa markers cũ
  Object.values(mapMarkers).forEach(m=>m.remove());
  mapMarkers={};
  routeMapData.forEach(d=>{
    const done=d.status==='success'||d.status==='warning';
    const color=done?'#10b981':'#f59e0b';
    const sz=done?12:14;
    const ic=L.divIcon({
      className:'',
      html:`<div style="width:${sz}px;height:${sz}px;background:${color};border-radius:50%;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3)"></div>`,
      iconSize:[sz,sz],iconAnchor:[sz/2,sz/2]
    });
    const m=L.marker([d.lat,d.lng],{icon:ic,title:d.name}).addTo(routeMap)
      .bindPopup(`<strong>${d.name}</strong><br><span style="font-size:.8rem;color:${done?'#059669':'#d97706'}">${done?'✅ Đã giao':'⏳ Chờ giao'}</span>`);
    mapMarkers[d.id]=m;
  });
}

// ── CẬP NHẬT BẢN ĐỒ REALTIME (gọi sau mỗi confirm) ──────
function updateRouteMap(){
  if(!routeMap) return;
  // 1. Cập nhật màu từng marker theo trạng thái mới
  routeMapData.forEach(d=>{
    const m=mapMarkers[d.id];
    if(!m) return;
    const done=d.status==='success'||d.status==='warning';
    const color=done?'#10b981':'#f59e0b';
    const sz=done?12:14;
    m.setIcon(L.divIcon({
      className:'',
      html:`<div style="width:${sz}px;height:${sz}px;background:${color};border-radius:50%;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3)"></div>`,
      iconSize:[sz,sz],iconAnchor:[sz/2,sz/2]
    }));
    m.getPopup()?.setContent(`<strong>${d.name}</strong><br><span style="font-size:.8rem;color:${done?'#059669':'#d97706'}">${done?'✅ Đã giao':'⏳ Chờ giao'}</span>`);
  });
  // 2. Xóa marker đỏ cũ của điểm tiếp theo
  if(nextDestMarker){nextDestMarker.remove();nextDestMarker=null;}
  // 3. Vẽ lại tuyến đến điểm TIẾP THEO (dùng vị trí tài xế hiện tại nếu có)
  const dLat=drvRouteMarker?drvRouteMarker.getLatLng().lat:null;
  const dLng=drvRouteMarker?drvRouteMarker.getLatLng().lng:null;
  if(dLat&&dLng){
    _drawRouteFromPos(dLat,dLng);
  } else {
    // Lấy GPS mới nếu chưa có
    navigator.geolocation?.getCurrentPosition(
      pos=>_drawRouteFromPos(pos.coords.latitude,pos.coords.longitude),
      ()=>{},
      {enableHighAccuracy:true,timeout:8000,maximumAge:30000}
    );
  }
}

// ── HELPER: Vẽ route từ vị trí cho trước ─────────────────
function _drawRouteFromPos(lat,lng){
  // Cập nhật marker tài xế
  if(drvRouteMarker) drvRouteMarker.remove();
  const myIcon=L.divIcon({
    className:'',
    html:'<div style="width:20px;height:20px;background:#3b82f6;border-radius:50%;border:3px solid #fff;box-shadow:0 0 0 4px rgba(59,130,246,.3)"></div>',
    iconSize:[20,20],iconAnchor:[10,10]
  });
  drvRouteMarker=L.marker([lat,lng],{icon:myIcon,zIndexOffset:1000}).addTo(routeMap).bindPopup('<strong>🚛 Vị trí của bạn</strong>');

  const next=getNextPending(lat,lng);
  document.getElementById('rp-loading').style.display='none';

  if(!next){
    document.getElementById('rp-name').textContent='✅ Đã giao hết tất cả điểm!';
    document.getElementById('rp-fullname').textContent='Chuyến xe sắp hoàn thành.';
    document.getElementById('rp-addr').textContent='';
    document.getElementById('rp-phone').textContent='';
    document.getElementById('rp-dist-air').textContent='—';
    document.getElementById('rp-dist-road').textContent='—';
    document.getElementById('rp-eta').textContent='—';
    document.getElementById('btn-nav-maps').style.display='none';
    if(routingControl){routingControl.remove();routingControl=null;}
    routeMap.setView([lat,lng],14);
    return;
  }

  nextDestLat=next.lat; nextDestLng=next.lng;

  // Cập nhật panel
  document.getElementById('rp-name').textContent=next.name;
  document.getElementById('rp-fullname').textContent=next.name;
  document.getElementById('rp-addr').textContent=next.address||'';
  document.getElementById('rp-phone').textContent=next.phone?'📞 '+next.phone:'';
  document.getElementById('rp-status-pill').innerHTML=
    '<span style="background:#fef3c7;color:#b45309;padding:.15rem .55rem;border-radius:999px;font-size:.72rem;font-weight:700">⏳ Chờ giao</span>';

  const airDist=haversine(lat,lng,next.lat,next.lng);
  document.getElementById('rp-dist-air').textContent=
    airDist<1000?Math.round(airDist)+'m':(airDist/1000).toFixed(1)+'km';
  document.getElementById('btn-nav-maps').style.display='flex';

  // Marker đỏ điểm tiếp theo
  const destIcon=L.divIcon({
    className:'',
    html:'<div style="width:20px;height:20px;background:#ef4444;border-radius:50%;border:3px solid #fff;box-shadow:0 0 0 4px rgba(239,68,68,.3)"></div>',
    iconSize:[20,20],iconAnchor:[10,10]
  });
  nextDestMarker=L.marker([next.lat,next.lng],{icon:destIcon,zIndexOffset:900})
    .addTo(routeMap).bindPopup(`<strong>🎯 ${next.name}</strong>`);

  // Vẽ route
  if(routingControl) routingControl.remove();
  routingControl=L.Routing.control({
    waypoints:[L.latLng(lat,lng),L.latLng(next.lat,next.lng)],
    routeWhileDragging:false,show:false,addWaypoints:false,
    fitSelectedRoutes:true,
    lineOptions:{styles:[{color:'#3b82f6',weight:5,opacity:.85}],extendToWaypoints:true,missingRouteTolerance:0},
    createMarker:()=>null,
  })
  .on('routesfound',(e)=>{
    const r=e.routes[0];
    const distM=r.summary.totalDistance, timeS=r.summary.totalTime;
    document.getElementById('rp-dist-road').textContent=
      distM<1000?Math.round(distM)+'m':(distM/1000).toFixed(1)+'km';
    const mins=Math.round(timeS/60);
    document.getElementById('rp-eta').textContent=mins<60?mins+' phút':Math.floor(mins/60)+'h '+(mins%60)+'ph';
  })
  .on('routingerror',()=>{
    L.polyline([[lat,lng],[next.lat,next.lng]],{color:'#3b82f6',weight:4,opacity:.7,dashArray:'8 6'}).addTo(routeMap);
    const d=haversine(lat,lng,next.lat,next.lng);
    document.getElementById('rp-dist-road').textContent=d<1000?Math.round(d)+'m (~thẳng)':(d/1000).toFixed(1)+'km (~thẳng)';
    document.getElementById('rp-eta').textContent='~'+(Math.round(d/500))+' phút';
  })
  .addTo(routeMap);
}

// ── AUTO DRAW ROUTE (gọi lần đầu khi mở tab bản đồ) ─────
function autoDrawRoute(){
  document.getElementById('rp-loading').style.display='flex';
  if(!navigator.geolocation){
    document.getElementById('rp-loading').style.display='none';
    document.getElementById('rp-fullname').textContent='Trình duyệt không hỗ trợ GPS';
    return;
  }
  navigator.geolocation.getCurrentPosition(
    pos=>_drawRouteFromPos(pos.coords.latitude,pos.coords.longitude),
    err=>{
      document.getElementById('rp-loading').style.display='none';
      document.getElementById('rp-fullname').textContent='Không lấy được vị trí GPS: '+err.message;
      showToast('❌ GPS: '+err.message);
      fitAllPoints();
    },
    {enableHighAccuracy:true,timeout:15000,maximumAge:0}
  );
}

// Tìm điểm pending gần nhất theo GPS tài xế (dùng routeMapData — có thể update)
function getNextPending(dLat,dLng){
  const pending=routeMapData.filter(d=>d.status==='pending');
  if(!pending.length) return null;
  let best=null, bestDist=Infinity;
  pending.forEach(d=>{
    const dist=haversine(dLat,dLng,d.lat,d.lng);
    if(dist<bestDist){bestDist=dist;best=d;}
  });
  return best;
}

function fitAllPoints(){
  if(!routeMap) return;
  const pts=routeMapData.map(d=>[d.lat,d.lng]);
  if(pts.length>1) routeMap.fitBounds(pts,{padding:[40,40]});
  else if(pts.length===1) routeMap.setView(pts[0],14);
}

function refreshRoute(){
  if(!routeMap){return;}
  if(routingControl){routingControl.remove();routingControl=null;}
  autoDrawRoute();
  showToast('🔄 Đang làm mới tuyến đường...');
}

function openGoogleMaps(){
  if(nextDestLat===null||nextDestLng===null){
    showToast('❌ Chưa xác định điểm giao tiếp theo.');
    return;
  }
  const url=`https://www.google.com/maps/dir/?api=1&destination=${nextDestLat},${nextDestLng}&travelmode=driving`;
  window.open(url,'_blank');
}

// ── TOAST ─────────────────────────────────────────────────────────
function showToast(msg,dur=3000){
  const t=document.getElementById('toast');
  t.textContent=msg; t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),dur);
}
</script>
@endpush

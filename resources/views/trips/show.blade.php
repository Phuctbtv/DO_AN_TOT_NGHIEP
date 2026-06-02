@extends('layouts.app')
@section('title', 'Chi tiết Chuyến xe ' . $trip->trip_code . ' - ĐẠI PHÚC')

@push('styles')
<style>
/* ── Layout ──────────────────────────────────────── */
.trip-grid {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 1.5rem;
  align-items: start;
}
@media (max-width: 900px) {
  .trip-grid { grid-template-columns: 1fr; }
}

/* ── Cards ────────────────────────────────────────── */
.card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 1.25rem;
  box-shadow: 0 2px 12px rgba(0,0,0,.04);
}
.card-header {
  padding: .9rem 1.4rem;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.card-header h3 {
  font-size: .95rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}
.card-body { padding: 1.25rem 1.4rem; }

/* ── Hero banner ─────────────────────────────────── */
.trip-hero {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #1e3a5f 100%);
  border-radius: 16px;
  padding: 1.75rem;
  margin-bottom: 1.25rem;
  color: #fff;
  position: relative;
  overflow: hidden;
}
.trip-hero::before {
  content: '🚛';
  position: absolute;
  right: -1rem;
  bottom: -1.5rem;
  font-size: 7rem;
  opacity: .05;
}

/* ── Meta mini-cards ─────────────────────────────── */
.meta-chips {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: .85rem;
  margin-top: 1.4rem;
}
.meta-chip {
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 10px;
  padding: .85rem;
}
.meta-chip .chip-label {
  font-size: .68rem;
  color: #94a3b8;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: .5px;
  margin-bottom: .3rem;
}
.meta-chip .chip-val {
  font-size: .9rem;
  font-weight: 700;
  color: #fff;
  line-height: 1.3;
}

/* ── Timeline ────────────────────────────────────── */
.timeline { position: relative; padding-left: 2rem; }
.timeline::before {
  content: '';
  position: absolute;
  left: .7rem;
  top: .6rem;
  bottom: .6rem;
  width: 2px;
  background: #e2e8f0;
}
.t-step { position: relative; margin-bottom: 1.6rem; }
.t-step:last-child { margin-bottom: 0; }
.t-dot {
  position: absolute;
  left: -1.7rem;
  top: .2rem;
  width: 1rem;
  height: 1rem;
  border-radius: 50%;
  border: 2px solid #fff;
}
.t-label {
  font-size: .9rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: .5rem;
  margin-bottom: .15rem;
}
.t-desc { font-size: .8rem; color: #64748b; margin-left: 1.5rem; }
.t-time { font-size: .75rem; color: #94a3b8; margin-left: 1.5rem; margin-top: .2rem; }
.t-badge {
  font-size: .68rem;
  padding: .1rem .5rem;
  border-radius: 999px;
  font-weight: 700;
}
.t-pending { opacity: .45; }

/* ── Tables ──────────────────────────────────────── */
.data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: .88rem;
}
.data-table thead tr { background: #f8fafc; }
.data-table th {
  padding: .6rem .9rem;
  text-align: left;
  font-size: .72rem;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: .5px;
  border-bottom: 2px solid #e2e8f0;
  white-space: nowrap;
}
.data-table td {
  padding: .7rem .9rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}
.data-table tr:last-child td { border-bottom: none; }
.data-table tbody tr:hover { background: #f8fafc; }

/* ── Progress bar ────────────────────────────────── */
.prog-wrap { display: flex; align-items: center; gap: .5rem; }
.prog-bar {
  flex: 1; height: 6px; background: #f1f5f9;
  border-radius: 999px; overflow: hidden;
}
.prog-fill { height: 100%; border-radius: 999px; }

/* ── Sidebar info ────────────────────────────────── */
.info-block { margin-bottom: 1.1rem; }
.info-block label {
  display: block;
  font-size: .7rem;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: .5px;
  margin-bottom: .25rem;
}
.info-block .info-val {
  font-size: .88rem;
  font-weight: 600;
  color: #1e293b;
}
.info-block .info-sub {
  font-size: .78rem;
  color: #94a3b8;
  margin-top: .15rem;
}

/* ── Stat numbers ────────────────────────────────── */
.stat-row {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}
.stat-box {
  flex: 1;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: .75rem;
  text-align: center;
}
.stat-box .stat-num {
  font-size: 1.4rem;
  font-weight: 800;
  line-height: 1;
}
.stat-box .stat-lbl {
  font-size: .72rem;
  color: #94a3b8;
  margin-top: .2rem;
}
</style>
@endpush

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  @include('partials.admin-sidebar', ['activeMenu' => 'trips'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '🚛 Chi tiết Chuyến xe'])

    <div style="padding:1.5rem">

      {{-- BREADCRUMB --}}
      <div style="font-size:.82rem;color:#9ca3af;margin-bottom:1.25rem;display:flex;align-items:center;gap:.4rem">
        <a href="{{ route('admin.trips.index') }}" style="color:#0d9488;text-decoration:none">Chuyến xe</a>
        <span>›</span>
        <span style="color:#1e293b;font-weight:700">{{ $trip->trip_code }}</span>
      </div>

      {{-- FLASH --}}
      @if(session('success'))
        <div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:.875rem 1.25rem;border-radius:10px;margin-bottom:1.25rem;font-weight:500">
          ✅ {!! session('success') !!}
        </div>
      @endif
      @if(session('error'))
        <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:.875rem 1.25rem;border-radius:10px;margin-bottom:1.25rem">
          ❌ {{ session('error') }}
        </div>
      @endif

      <div class="trip-grid">

        {{-- ════════════════════ CỘT TRÁI ════════════════════ --}}
        <div>

          {{-- ── HERO ─────────────────────────────────────────── --}}
          <div class="trip-hero">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.75rem">
              <div>
                <div style="font-size:.75rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:.3rem">Mã chuyến xe</div>
                <div style="font-size:1.8rem;font-weight:900;font-family:monospace;color:#5eead4;letter-spacing:1px">
                  {{ $trip->trip_code }}
                </div>
              </div>
              <span id="rt-hero-badge" style="
                background:{{ $trip->status_bg }};
                color:{{ $trip->status_color }};
                padding:.55rem 1.4rem;
                border-radius:999px;
                font-size:.9rem;
                font-weight:800;
                white-space:nowrap;
                box-shadow:0 2px 8px rgba(0,0,0,.15);
              ">
                {{ $trip->status_label }}
              </span>
            </div>

            <div class="meta-chips">
              <div class="meta-chip">
                <div class="chip-label">👨‍✈️ Tài xế</div>
                <div class="chip-val">{{ $trip->driver?->name ?? '—' }}</div>
                <div style="font-size:.72rem;color:#64748b;margin-top:.15rem">{{ $trip->driver?->email ?? '' }}</div>
              </div>
              <div class="meta-chip">
                <div class="chip-label">🏭 Kho xuất phát</div>
                <div class="chip-val">{{ $trip->warehouse?->name ?? '—' }}</div>
                <div style="font-size:.72rem;color:#64748b;margin-top:.15rem">{{ Str::limit($trip->warehouse?->address ?? '', 40) }}</div>
              </div>
              <div class="meta-chip">
                <div class="chip-label">🚗 Phương tiện</div>
                <div class="chip-val">{{ $trip->vehicle_info }}</div>
                <div style="font-size:.72rem;color:#64748b;margin-top:.15rem">Người tạo: {{ $trip->creator?->name ?? '—' }}</div>
              </div>
            </div>
          </div>

          {{-- ── TIMELINE ─────────────────────────────────────── --}}
          <div class="card">
            <div class="card-header">
              <h3>📍 Timeline chuyến xe</h3>
              <span style="display:flex;align-items:center;gap:.35rem;font-size:.8rem;color:#94a3b8">
                <span id="rt-ws-dot" style="width:7px;height:7px;border-radius:50%;background:#cbd5e1;display:inline-block"></span>
                <span id="rt-ws-label">Đang kết nối...</span>
              </span>
            </div>
            <div class="card-body">
              @php
                $steps = [
                  [
                    'key'   => 'preparing',
                    'label' => 'Chuẩn bị',
                    'icon'  => '📋',
                    'time'  => $trip->created_at,
                    'desc'  => 'Chuyến xe được tạo & phân công tài xế',
                  ],
                  [
                    'key'   => 'exporting',
                    'label' => 'Xuất kho',
                    'icon'  => '📤',
                    'time'  => $trip->exported_at,
                    'desc'  => 'Thủ kho đã xác nhận xuất hàng',
                  ],
                  [
                    'key'   => 'shipping',
                    'label' => 'Đang giao',
                    'icon'  => '🚛',
                    'time'  => $trip->started_at,
                    'desc'  => 'Tài xế đang trên đường giao hàng',
                  ],
                  [
                    'key'   => 'completed',
                    'label' => 'Hoàn thành',
                    'icon'  => '✅',
                    'time'  => $trip->completed_at,
                    'desc'  => 'Tất cả hàng hoá đã giao xong',
                  ],
                ];
                $order    = ['preparing'=>0,'exporting'=>1,'shipping'=>2,'completed'=>3,'cancelled'=>4];
                $curOrder = $order[$trip->status] ?? 0;
              @endphp

              <div class="timeline">
                @foreach($steps as $i => $step)
                  @php
                    $stepOrder = $order[$step['key']] ?? 99;
                    $isDone    = $curOrder > $stepOrder;
                    $isCurrent = $trip->status === $step['key'];
                    $isPending = !$isDone && !$isCurrent;

                    if ($isDone)        { $dotColor = '#10b981'; $labelColor = '#0f172a'; }
                    elseif ($isCurrent) { $dotColor = $trip->status_color; $labelColor = $trip->status_color; }
                    else                { $dotColor = '#e2e8f0'; $labelColor = '#94a3b8'; }
                  @endphp

                  <div class="t-step {{ $isPending ? 't-pending' : '' }}">
                    {{-- Dot --}}
                    <div class="t-dot"
                         style="background:{{ $dotColor }};box-shadow:0 0 0 3px {{ $isDone||$isCurrent ? $dotColor.'33' : 'transparent' }}">
                    </div>

                    <div class="t-label" style="color:{{ $labelColor }}">
                      <span style="font-size:1.05rem">{{ $step['icon'] }}</span>
                      <span>{{ $step['label'] }}</span>
                      @if($isCurrent)
                        <span class="t-badge"
                              style="background:{{ $trip->status_bg }};color:{{ $trip->status_color }}">
                          Hiện tại
                        </span>
                      @endif
                      @if($isDone)
                        <span class="t-badge" style="background:#d1fae5;color:#065f46">Hoàn tất</span>
                      @endif
                    </div>

                    <div class="t-desc">{{ $step['desc'] }}</div>

                    @if($step['time'])
                      <div class="t-time">
                        🕐 {{ $step['time']->format('H:i – d/m/Y') }}
                      </div>
                    @elseif(!$isPending)
                      <div class="t-time" style="color:#f59e0b">⏳ Chưa có thời gian</div>
                    @endif
                  </div>
                @endforeach

                {{-- Cancelled step --}}
                @if($trip->isCancelled())
                  <div class="t-step" style="margin-top:1.6rem">
                    <div class="t-dot" style="background:#ef4444;box-shadow:0 0 0 3px #ef444433"></div>
                    <div class="t-label" style="color:#ef4444">
                      <span>❌</span> <span>Đã Huỷ</span>
                    </div>
                    <div class="t-desc">Chuyến xe này đã bị huỷ bỏ</div>
                  </div>
                @endif
              </div>
            </div>
          </div>

          {{-- ── DANH SÁCH HÀNG HOÁ ───────────────────────────── --}}
          <div class="card">
            <div class="card-header">
              <h3>📦 Hàng hoá trên xe</h3>
              <span style="font-size:.8rem;color:#64748b">{{ $trip->tripDetails->count() }} loại</span>
            </div>
            <div style="overflow-x:auto">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Mặt hàng</th>
                    <th>Danh mục</th>
                    <th>Đơn vị</th>
                    <th style="text-align:right">Xuất</th>
                    <th style="text-align:right">Đã giao</th>
                    <th style="min-width:130px">Tiến độ</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($trip->tripDetails as $detail)
                    @php
                      $pct = $detail->quantity_loaded > 0
                           ? round(($detail->quantity_delivered / $detail->quantity_loaded) * 100)
                           : 0;
                      $barColor = $pct >= 100 ? '#10b981' : ($pct > 0 ? '#3b82f6' : '#e2e8f0');
                    @endphp
                    <tr>
                      <td>
                        <div style="font-weight:700;color:#0f172a">{{ $detail->supply?->name ?? '—' }}</div>
                      </td>
                      <td>
                        <span style="background:#dbeafe;color:#1d4ed8;padding:.2rem .6rem;border-radius:20px;font-size:.75rem;font-weight:600">
                          {{ $detail->supply?->category?->name ?? '—' }}
                        </span>
                      </td>
                      <td style="color:#64748b">{{ $detail->supply?->unit ?? '—' }}</td>
                      <td style="text-align:right">
                        <span style="font-weight:800;color:#7c3aed;font-size:1rem">{{ number_format($detail->quantity_loaded) }}</span>
                      </td>
                      <td style="text-align:right">
                        <span style="font-weight:800;color:#059669;font-size:1rem">{{ number_format($detail->quantity_delivered) }}</span>
                      </td>
                      <td>
                        <div class="prog-wrap">
                          <div class="prog-bar">
                            <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
                          </div>
                          <span style="font-size:.75rem;font-weight:700;color:{{ $barColor }};min-width:2.5rem">{{ $pct }}%</span>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" style="text-align:center;padding:2rem;color:#94a3b8">Không có hàng hoá</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          {{-- ── GHI CHÚ ──────────────────────────────────────── --}}
          @if($trip->notes)
            <div class="card" style="margin-bottom:1.25rem">
              <div class="card-header">
                <h3>📝 Ghi chú</h3>
              </div>
              <div class="card-body">
                <p style="color:#475569;font-size:.9rem;line-height:1.7;margin:0">{{ $trip->notes }}</p>
              </div>
            </div>
          @endif

          {{-- ── DANH SÁCH ĐIỂM GIAO ───────────────────────────── --}}
          <div class="card">
            <div class="card-header">
              <h3>
                📍 Danh sách điểm giao
                <span style="font-size:.82rem;font-weight:400;color:#64748b;margin-left:.4rem">
                  ({{ $trip->deliveries->count() }} hộ)
                </span>
              </h3>
              @php
                $doneCount    = $trip->deliveries->where('status', 'success')->count();
                $totalCount   = $trip->deliveries->count();
                $doneProgress = $totalCount > 0 ? round($doneCount / $totalCount * 100) : 0;
              @endphp
              @if($totalCount > 0)
                <span style="font-size:.82rem;color:#64748b">
                  Đã giao: <strong id="rt-header-done" style="color:#10b981">{{ $doneCount }}</strong> / {{ $totalCount }}
                  <span id="rt-header-pct" style="margin-left:.3rem;color:#10b981">({{ $doneProgress }}%)</span>
                </span>
              @endif
            </div>

            @if($trip->deliveries->isEmpty())
              <div style="padding:2.5rem;text-align:center;color:#94a3b8">
                <div style="font-size:2rem;margin-bottom:.5rem">📭</div>
                <div>Chưa có hộ dân nào được phân công.</div>
              </div>
            @else
              <div style="overflow-x:auto">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Mã giao</th>
                      <th>Họ tên hộ dân</th>
                      <th>Địa chỉ</th>
                      <th style="text-align:center">Ưu tiên</th>
                      <th style="text-align:center">Trạng thái</th>
                      <th style="text-align:center">Thời gian giao</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($trip->deliveries as $delivery)
                      @php
                        $hh = $delivery->household;

                        // Màu ưu tiên
                        $pColors = [
                          1 => ['#fee2e2','#dc2626','🔴 Cấp 1'],
                          2 => ['#fef3c7','#d97706','🟡 Cấp 2'],
                          3 => ['#d1fae5','#059669','🟢 Cấp 3'],
                        ];
                        $pc = $pColors[$hh?->priority_level] ?? ['#f1f5f9','#64748b','— Chưa rõ'];

                        // Trạng thái giao
                        $statusMap = [
                          'pending' => ['⏳ Chờ giao',    '#fef3c7','#b45309'],
                          'success' => ['✅ Đã giao',     '#d1fae5','#059669'],
                          'warning' => ['⚠️ Cần xem xét','#fef3c7','#92400e'],
                          'failed'  => ['❌ Thất bại',    '#fee2e2','#dc2626'],
                        ];
                        $sm = $statusMap[$delivery->status] ?? [$delivery->status,'#f1f5f9','#64748b'];
                      @endphp
                      <tr data-delivery-id="{{ $delivery->id }}">
                        {{-- Mã giao --}}
                        <td>
                          <span style="font-family:monospace;font-size:.78rem;font-weight:700;color:#7c3aed;background:#ede9fe;padding:.2rem .5rem;border-radius:5px">
                            {{ $delivery->delivery_code }}
                          </span>
                        </td>

                        {{-- Họ tên --}}
                        <td>
                          <div style="font-weight:700;color:#0f172a;font-size:.88rem">
                            {{ $hh?->household_name ?? $delivery->recipient_name }}
                          </div>
                          @if($delivery->recipient_cccd)
                            <div style="font-size:.75rem;color:#94a3b8">CCCD: {{ $delivery->recipient_cccd }}</div>
                          @endif
                        </td>

                        {{-- Địa chỉ --}}
                        <td style="color:#64748b;font-size:.82rem;max-width:200px">
                          {{ Str::limit($hh?->address ?? '—', 60) }}
                        </td>

                        {{-- Ưu tiên --}}
                        <td style="text-align:center">
                          @if($hh?->priority_level)
                            <span style="background:{{ $pc[0] }};color:{{ $pc[1] }};padding:.25rem .65rem;border-radius:999px;font-size:.75rem;font-weight:700;white-space:nowrap">
                              {{ $pc[2] }}
                            </span>
                          @else
                            <span style="color:#94a3b8;font-size:.82rem">—</span>
                          @endif
                        </td>

                        {{-- Trạng thái --}}
                        <td style="text-align:center">
                          <span class="delivery-status-badge" style="background:{{ $sm[1] }};color:{{ $sm[2] }};padding:.3rem .75rem;border-radius:999px;font-size:.75rem;font-weight:700;white-space:nowrap">
                            {{ $sm[0] }}
                          </span>
                        </td>

                        {{-- Thời gian giao --}}
                        <td style="text-align:center">
                          @if($delivery->delivered_at)
                            <div class="delivery-time" style="font-size:.82rem;font-weight:600;color:#059669">
                              {{ $delivery->delivered_at->format('H:i') }}
                            </div>
                            <div style="font-size:.72rem;color:#94a3b8">
                              {{ $delivery->delivered_at->format('d/m/Y') }}
                            </div>
                          @else
                            <span class="delivery-time" style="color:#cbd5e1;font-size:.8rem">—</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>

        </div>{{-- end cột trái --}}

        {{-- ════════════════════ CỘT PHẢI (Sidebar Info) ════════════════════ --}}
        <div style="position:sticky;top:1rem">

          {{-- Thống kê nhanh --}}
          <div class="card">
            <div class="card-header">
              <h3>📊 Tổng quan</h3>
            </div>
            <div class="card-body">
              @php
                $totalLoaded    = $trip->tripDetails->sum('quantity_loaded');
                $totalDelivered = $trip->tripDetails->sum('quantity_delivered');
                $overallPct     = $totalLoaded > 0
                  ? round($totalDelivered / $totalLoaded * 100) : 0;
              @endphp

              <div class="stat-row">
                <div class="stat-box">
                  <div class="stat-num" style="color:#7c3aed">{{ $trip->tripDetails->count() }}</div>
                  <div class="stat-lbl">Loại hàng</div>
                </div>
                <div class="stat-box">
                  <div class="stat-num" style="color:#0891b2">{{ $trip->deliveries->count() }}</div>
                  <div class="stat-lbl">Hộ dân</div>
                </div>
              </div>

              <div class="stat-row">
                <div class="stat-box">
                  <div class="stat-num" id="rt-done-count" style="color:#059669">{{ $doneCount }}</div>
                  <div class="stat-lbl">Đã giao</div>
                </div>
                <div class="stat-box">
                  <div class="stat-num" id="rt-remain-count" style="color:#f59e0b">{{ $totalCount - $doneCount }}</div>
                  <div class="stat-lbl">Còn lại</div>
                </div>
              </div>

              {{-- Tiến độ tổng --}}
              <div style="margin-top:1.1rem">
                <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:.35rem">
                  <span style="color:#64748b;font-weight:600">Tổng tiến độ giao hàng</span>
                  <span id="rt-pct" style="font-weight:800;color:#059669">{{ $overallPct }}%</span>
                </div>
                <div style="height:8px;background:#f1f5f9;border-radius:999px;overflow:hidden">
                  <div id="rt-bar" style="height:100%;width:{{ $overallPct }}%;background:linear-gradient(90deg,#059669,#10b981);border-radius:999px;transition:width .4s"></div>
                </div>
                <div style="font-size:.75rem;color:#94a3b8;margin-top:.25rem">
                  {{ number_format($totalDelivered) }} / {{ number_format($totalLoaded) }} đơn vị
                </div>
              </div>
            </div>
          </div>

          {{-- Tài xế --}}
          <div class="card">
            <div class="card-header"><h3>👨‍✈️ Tài xế phụ trách</h3></div>
            <div class="card-body">
              <div style="display:flex;align-items:center;gap:.85rem">
                <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#6366f1);display:flex;align-items:center;justify-content:center;font-size:1.15rem;color:#fff;font-weight:800;flex-shrink:0">
                  {{ strtoupper(substr($trip->driver?->name ?? '?', 0, 1)) }}
                </div>
                <div>
                  <div style="font-weight:700;color:#0f172a">{{ $trip->driver?->name ?? '—' }}</div>
                  <div style="font-size:.8rem;color:#94a3b8">{{ $trip->driver?->email ?? '—' }}</div>
                </div>
              </div>

              @if($trip->vehicle_info)
                <div style="margin-top:1rem;padding:.75rem;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0">
                  <div style="font-size:.72rem;color:#94a3b8;font-weight:700;text-transform:uppercase;margin-bottom:.2rem">Phương tiện</div>
                  <div style="font-size:.88rem;font-weight:700;color:#1e293b">🚗 {{ $trip->vehicle_info }}</div>
                </div>
              @endif
            </div>
          </div>

          {{-- Kho --}}
          <div class="card">
            <div class="card-header"><h3>🏭 Kho xuất hàng</h3></div>
            <div class="card-body">
              <div class="info-block">
                <label>Tên kho</label>
                <div class="info-val">{{ $trip->warehouse?->name ?? '—' }}</div>
              </div>
              <div class="info-block">
                <label>Địa chỉ</label>
                <div class="info-val" style="font-weight:400;color:#64748b;font-size:.82rem">{{ $trip->warehouse?->address ?? '—' }}</div>
              </div>
              @if($trip->exported_at)
                <div class="info-block">
                  <label>📤 Xuất kho lúc</label>
                  <div class="info-val" style="color:#7c3aed">{{ $trip->exported_at->format('H:i – d/m/Y') }}</div>
                </div>
              @endif
            </div>
          </div>

          {{-- Tạo bởi --}}
          <div class="card">
            <div class="card-header"><h3>ℹ️ Thông tin tạo</h3></div>
            <div class="card-body">
              <div class="info-block">
                <label>Người tạo</label>
                <div class="info-val">{{ $trip->creator?->name ?? '—' }}</div>
              </div>
              <div class="info-block">
                <label>Ngày tạo</label>
                <div class="info-val">{{ $trip->created_at->format('H:i – d/m/Y') }}</div>
              </div>
              @if($trip->completed_at)
                <div class="info-block">
                  <label>✅ Hoàn thành lúc</label>
                  <div class="info-val" style="color:#059669">{{ $trip->completed_at->format('H:i – d/m/Y') }}</div>
                </div>
              @endif
            </div>
          </div>

          {{-- Nút In PDF --}}
          <a href="{{ route('admin.reports.trips.pdf', $trip) }}" target="_blank"
             style="display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.75rem;background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;border-radius:10px;text-decoration:none;font-weight:700;font-size:.88rem;margin-bottom:.6rem;box-shadow:0 2px 8px rgba(220,38,38,.25);transition:all .2s"
             onmouseover="this.style.opacity='.9'"
             onmouseout="this.style.opacity='1'">
            📄 In báo cáo PDF
          </a>

          {{-- Nút quay lại --}}
          <a href="{{ route('admin.trips.index') }}"
             style="display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.75rem;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;color:#64748b;text-decoration:none;font-weight:600;font-size:.88rem;transition:all .2s"
             onmouseover="this.style.background='#f1f5f9';this.style.color='#334155';"
             onmouseout="this.style.background='#f8fafc';this.style.color='#64748b';">
            ← Quay lại danh sách
          </a>

        </div>{{-- end cột phải --}}

      </div>
    </div>
  </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const TRIP_ID     = {{ $trip->id }};
  const TRIP_STATUS = '{{ $trip->status }}';
  const TRIP_CODE   = '{{ $trip->trip_code }}';

  // Chỉ lắng nghe khi chuyến đang hoạt động
  if(!window.Echo) return;
  if(!['shipping','exporting','preparing'].includes(TRIP_STATUS)) return;

  // ────────────────────────────────────────────────────────
  // 1. Lắng nghe cập nhật delivery (từng lần giao)
  // ────────────────────────────────────────────────────────
  window.Echo.private(`deliveries.${TRIP_ID}`)
    .listen('.DeliveryUpdated', (e) => {
      // a) Cập nhật badge trạng thái từng hàng trong bảng delivery
      if(e.delivery_id){
        updateDeliveryRow(e.delivery_id, e.delivery_status, e.delivered_at);
      }

      // b) Cập nhật stat boxes (Đã giao / Còn lại)
      const doneEl    = document.getElementById('rt-done-count');
      const remainEl  = document.getElementById('rt-remain-count');
      const totalEl   = document.getElementById('rt-total-count');
      const pctEl     = document.getElementById('rt-pct');
      const barEl     = document.getElementById('rt-bar');
      const headerEl  = document.getElementById('rt-header-done');
      const headerPct = document.getElementById('rt-header-pct');

      const done   = e.success_count ?? 0;
      const total  = e.total_count   ?? 0;
      const remain = total - done;
      const pct    = total > 0 ? Math.round(done/total*100) : 0;

      if(doneEl)   doneEl.textContent   = done;
      if(remainEl) remainEl.textContent = remain;
      if(totalEl)  totalEl.textContent  = total;
      if(pctEl)    pctEl.textContent    = pct + '%';
      if(barEl)    barEl.style.width    = pct + '%';
      if(headerEl) headerEl.textContent = done;
      if(headerPct)headerPct.textContent= '(' + pct + '%)';

      // Flash toàn trang
      flashRow(null, '#ecfdf5');
      showTripToast(`✅ Đã giao ${done}/${total} điểm`, 'success');
    })
    .subscribed(() => updateWsDot('connected'))
    .error(() => updateWsDot('error'));

  // ────────────────────────────────────────────────────────
  // 2. Lắng nghe khi trip hoàn thành
  // ────────────────────────────────────────────────────────
  window.Echo.private(`trips.${TRIP_ID}`)
    .listen('.TripStatusUpdated', (e) => {
      if(e.status === 'completed'){
        // Cập nhật hero badge
        const heroBadge = document.getElementById('rt-hero-badge');
        if(heroBadge){
          heroBadge.textContent = '✅ Hoàn thành';
          heroBadge.style.background = '#059669';
          heroBadge.style.color = '#fff';
        }
        showTripToast(`🎉 Chuyến xe ${TRIP_CODE} đã hoàn thành!`, 'success', 6000);
        // Reload sau 5s để cập nhật timeline
        setTimeout(() => location.reload(), 5000);
      }
    });

  // ── Helpers ────────────────────────────────────────────

  function updateDeliveryRow(deliveryId, status, deliveredAt){
    const row = document.querySelector(`tr[data-delivery-id="${deliveryId}"]`);
    if(!row) return;

    const badge = row.querySelector('.delivery-status-badge');
    if(badge){
      const map = {
        success: { text:'✅ Đã giao',     bg:'#d1fae5', color:'#059669' },
        warning: { text:'⚠️ Cần xem xét', bg:'#fef3c7', color:'#92400e' },
        failed:  { text:'❌ Thất bại',    bg:'#fee2e2', color:'#dc2626' },
        pending: { text:'⏳ Chờ giao',    bg:'#fef3c7', color:'#b45309' },
      };
      const s = map[status] || map.pending;
      badge.textContent = s.text;
      badge.style.background = s.bg;
      badge.style.color = s.color;
    }

    const timeEl = row.querySelector('.delivery-time');
    if(timeEl && deliveredAt && status !== 'pending'){
      timeEl.textContent = deliveredAt;
      timeEl.style.color = '#059669';
      timeEl.style.fontWeight = '700';
    }

    // Flash hàng
    row.style.background = '#ecfdf5';
    setTimeout(() => row.style.background = '', 2000);
  }

  function flashRow(el, color){
    const target = el || document.querySelector('.trip-hero');
    if(!target) return;
    target.style.transition = 'box-shadow .3s';
    target.style.boxShadow = `0 0 0 3px ${color}`;
    setTimeout(() => target.style.boxShadow = '', 1500);
  }

  function updateWsDot(state){
    const dot = document.getElementById('rt-ws-dot');
    const lbl = document.getElementById('rt-ws-label');
    if(!dot || !lbl) return;
    dot.style.background = state === 'connected' ? '#10b981' : '#ef4444';
    lbl.textContent      = state === 'connected' ? 'Realtime đang hoạt động' : 'Mất kết nối';
  }

  function showTripToast(msg, type='info', duration=4000){
    const colors = { info:'#0f172a', success:'#059669', warning:'#d97706' };
    const el = document.createElement('div');
    el.style.cssText = `position:fixed;bottom:1.5rem;right:1.5rem;background:${colors[type]};color:#fff;padding:.7rem 1.4rem;border-radius:10px;font-size:.85rem;font-weight:700;z-index:9999;opacity:0;transition:opacity .3s;box-shadow:0 4px 20px rgba(0,0,0,.25);max-width:320px`;
    el.textContent = msg;
    document.body.appendChild(el);
    requestAnimationFrame(() => el.style.opacity = 1);
    setTimeout(() => { el.style.opacity = 0; setTimeout(() => el.remove(), 300); }, duration);
  }
});
</script>
@endpush

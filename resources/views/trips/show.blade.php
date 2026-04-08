@extends('layouts.app')
@section('title', 'Chi tiết Chuyến xe {{ $trip->trip_code }} - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  @include('partials.admin-sidebar', ['activeMenu' => 'trips'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '🚛 Chi tiết Chuyến xe'])

    <div style="padding:1.5rem">

      {{-- BREADCRUMB --}}
      <div style="font-size:.83rem;color:#9ca3af;margin-bottom:1.25rem">
        <a href="{{ route('admin.trips.index') }}" style="color:#0d9488;text-decoration:none">Chuyến xe</a>
        &rsaquo; {{ $trip->trip_code }}
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

      <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

        {{-- ===== CỘT TRÁI ===== --}}
        <div>

          {{-- HEADER CARD --}}
          <div style="background:linear-gradient(135deg,#0f172a,#1e293b);border-radius:16px;padding:1.75rem;margin-bottom:1.25rem;color:#fff">
            <div style="display:flex;justify-content:space-between;align-items:flex-start">
              <div>
                <div style="font-size:.8rem;color:#94a3b8;margin-bottom:.25rem">MÃ CHUYẾN XE</div>
                <div style="font-size:1.75rem;font-weight:800;font-family:monospace;color:#5eead4">{{ $trip->trip_code }}</div>
              </div>
              <span style="background:{{ $trip->status_bg }};color:{{ $trip->status_color }};padding:.5rem 1.25rem;border-radius:999px;font-size:.875rem;font-weight:700">
                {{ $trip->status_label }}
              </span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:1.5rem">
              <div style="background:rgba(255,255,255,.06);padding:.875rem;border-radius:10px">
                <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.25rem">Tài xế</div>
                <div style="font-weight:700">{{ $trip->driver?->name ?? '—' }}</div>
              </div>
              <div style="background:rgba(255,255,255,.06);padding:.875rem;border-radius:10px">
                <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.25rem">Kho xuất</div>
                <div style="font-weight:700">{{ $trip->warehouse?->name ?? '—' }}</div>
              </div>
              <div style="background:rgba(255,255,255,.06);padding:.875rem;border-radius:10px">
                <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.25rem">Phương tiện</div>
                <div style="font-weight:700">{{ $trip->vehicle_info }}</div>
              </div>
            </div>
          </div>

          {{-- TIMELINE --}}
          <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem;margin-bottom:1.25rem">
            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:1.5rem">📍 Timeline chuyến xe</h3>

            @php
              $steps = [
                ['key'=>'preparing', 'label'=>'Chuẩn bị',    'icon'=>'📋', 'time'=>$trip->created_at,   'desc'=>'Chuyến xe được tạo & phân công tài xế'],
                ['key'=>'exporting', 'label'=>'Xuất kho',     'icon'=>'📤', 'time'=>$trip->exported_at,  'desc'=>'Hàng hoá đã được xuất kho'],
                ['key'=>'shipping',  'label'=>'Đang giao',    'icon'=>'🚛', 'time'=>$trip->started_at,   'desc'=>'Xe đang trên đường giao hàng'],
                ['key'=>'completed', 'label'=>'Hoàn thành',   'icon'=>'✅', 'time'=>$trip->completed_at, 'desc'=>'Chuyến xe hoàn thành thành công'],
              ];
              $order    = ['preparing'=>0,'exporting'=>1,'shipping'=>2,'completed'=>3,'cancelled'=>4];
              $curOrder = $order[$trip->status] ?? 0;
            @endphp

            <div style="position:relative;padding-left:2rem">
              {{-- Line --}}
              <div style="position:absolute;left:.75rem;top:.5rem;bottom:.5rem;width:2px;background:#e2e8f0"></div>

              @foreach($steps as $i => $step)
                @php
                  $stepOrder = $order[$step['key']] ?? 99;
                  $isDone    = $curOrder > $stepOrder;
                  $isCurrent = $trip->status === $step['key'];
                  $isPending = !$isDone && !$isCurrent;

                  $dotBg = $isDone   ? '#10b981'
                         : ($isCurrent ? $trip->status_color
                         : '#e2e8f0');
                @endphp
                <div style="position:relative;margin-bottom:{{ $i < count($steps)-1 ? '1.5rem' : '0' }}">
                  {{-- Dot --}}
                  <div style="position:absolute;left:-1.75rem;top:.2rem;width:1rem;height:1rem;border-radius:50%;background:{{ $dotBg }};border:2px solid #fff;box-shadow:0 0 0 2px {{ $dotBg }}"></div>

                  <div style="opacity:{{ $isPending ? '.5' : '1' }}">
                    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.15rem">
                      <span style="font-size:1rem">{{ $step['icon'] }}</span>
                      <span style="font-weight:{{ $isCurrent ? '800' : '600' }};font-size:.9rem;color:{{ $isCurrent ? $trip->status_color : ($isDone ? '#0f172a' : '#64748b') }}">
                        {{ $step['label'] }}
                        @if($isCurrent)
                          <span style="font-size:.7rem;background:{{ $trip->status_bg }};color:{{ $trip->status_color }};padding:.1rem .5rem;border-radius:999px;margin-left:.35rem">Hiện tại</span>
                        @endif
                      </span>
                    </div>
                    <div style="font-size:.8rem;color:#64748b;margin-left:1.5rem">{{ $step['desc'] }}</div>
                    @if($step['time'])
                      <div style="font-size:.75rem;color:#94a3b8;margin-left:1.5rem;margin-top:.15rem">
                        🕐 {{ $step['time']->format('H:i d/m/Y') }}
                      </div>
                    @endif
                  </div>
                </div>
              @endforeach

              @if($trip->isCancelled())
                <div style="position:relative;margin-top:1.5rem">
                  <div style="position:absolute;left:-1.75rem;top:.2rem;width:1rem;height:1rem;border-radius:50%;background:#ef4444;border:2px solid #fff;box-shadow:0 0 0 2px #ef4444"></div>
                  <div>
                    <div style="font-weight:700;font-size:.9rem;color:#ef4444">❌ Đã Huỷ</div>
                    <div style="font-size:.8rem;color:#64748b">Chuyến xe này đã bị huỷ</div>
                  </div>
                </div>
              @endif
            </div>
          </div>

          {{-- DANH SÁCH HÀNG --}}
          <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem;margin-bottom:1.25rem">
            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:1rem">📦 Danh sách hàng hoá</h3>
            <table style="width:100%;border-collapse:collapse">
              <thead>
                <tr style="background:#f8fafc">
                  @foreach(['Mặt hàng','Danh mục','Đơn vị','Xuất','Đã giao','%'] as $h)
                    <th style="padding:.6rem .875rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">{{ $h }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @forelse($trip->tripDetails as $detail)
                  @php
                    $pct = $detail->quantity_loaded > 0
                        ? round(($detail->quantity_delivered / $detail->quantity_loaded) * 100)
                        : 0;
                  @endphp
                  <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:.65rem .875rem;font-weight:600;color:#0f172a">{{ $detail->supply?->name ?? '—' }}</td>
                    <td style="padding:.65rem .875rem;color:#64748b;font-size:.85rem">{{ $detail->supply?->category?->name ?? '—' }}</td>
                    <td style="padding:.65rem .875rem;color:#64748b;font-size:.85rem">{{ $detail->supply?->unit ?? '—' }}</td>
                    <td style="padding:.65rem .875rem;font-weight:600;color:#0d9488">{{ $detail->quantity_loaded }}</td>
                    <td style="padding:.65rem .875rem;font-weight:600;color:#10b981">{{ $detail->quantity_delivered }}</td>
                    <td style="padding:.65rem .875rem">
                      <div style="display:flex;align-items:center;gap:.5rem">
                        <div style="flex:1;height:6px;background:#f1f5f9;border-radius:999px;overflow:hidden">
                          <div style="height:100%;background:{{ $pct === 100 ? '#10b981' : '#3b82f6' }};width:{{ $pct }}%;border-radius:999px"></div>
                        </div>
                        <span style="font-size:.75rem;font-weight:600;color:#64748b;min-width:2.5rem">{{ $pct }}%</span>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="6" style="padding:1.5rem;text-align:center;color:#94a3b8">Không có hàng hoá</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          @if($trip->notes)
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:1.25rem;margin-bottom:1.25rem">
              <h4 style="font-size:.875rem;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:.5rem">📝 Ghi chú</h4>
              <p style="color:#475569;font-size:.9rem;line-height:1.6;margin:0">{{ $trip->notes }}</p>
            </div>
          @endif

          {{-- DANH SÁCH HỘ DÂN NHẬN HÀNG --}}
          <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
              <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0">
                📍 Điểm giao hàng
                <span style="font-size:.82rem;font-weight:400;color:#64748b">({{ $trip->deliveries->count() }} hộ)</span>
              </h3>
              @php
                $doneCount = $trip->deliveries->where('status', 'success')->count();
              @endphp
              @if($trip->deliveries->count() > 0)
                <span style="font-size:.82rem;color:#64748b">
                  Đã giao: <strong style="color:#10b981">{{ $doneCount }}</strong> / {{ $trip->deliveries->count() }}
                </span>
              @endif
            </div>

            @if($trip->deliveries->isEmpty())
              <div style="text-align:center;padding:1.5rem;color:#94a3b8;font-size:.875rem">
                Chưa có hộ dân nào được phân công.
              </div>
            @else
              <div style="max-height:380px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:10px">
                <table style="width:100%;border-collapse:collapse">
                  <thead>
                    <tr style="background:#f8fafc">
                      <th style="padding:.55rem .875rem;text-align:left;font-size:.72rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Mã giao</th>
                      <th style="padding:.55rem .875rem;text-align:left;font-size:.72rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Họ tên</th>
                      <th style="padding:.55rem .875rem;text-align:left;font-size:.72rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Địa chỉ</th>
                      <th style="padding:.55rem .875rem;text-align:center;font-size:.72rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Ưu tiên</th>
                      <th style="padding:.55rem .875rem;text-align:center;font-size:.72rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Trạng thái</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($trip->deliveries as $delivery)
                      @php
                        $hh = $delivery->household;
                        $pColors = [1=>['#fee2e2','#ef4444','🔴'],2=>['#fef3c7','#f59e0b','🟡'],3=>['#d1fae5','#10b981','🟢']];
                        $pc = $pColors[$hh?->priority_level] ?? ['#f1f5f9','#64748b','⬜'];
                        $statusMap = [
                          'pending' => ['Chờ giao','#fef3c7','#b45309'],
                          'success' => ['Hoàn thành','#d1fae5','#059669'],
                          'warning' => ['Cần xem xét','#fee2e2','#dc2626'],
                          'failed'  => ['Thất bại','#fee2e2','#dc2626'],
                        ];
                        $sm = $statusMap[$delivery->status] ?? [$delivery->status,'#f1f5f9','#64748b'];
                      @endphp
                      <tr style="border-bottom:1px solid #f1f5f9">
                        <td style="padding:.6rem .875rem;font-family:monospace;font-size:.78rem;color:#0d9488;font-weight:600">
                          {{ $delivery->delivery_code }}
                        </td>
                        <td style="padding:.6rem .875rem">
                          <div style="font-weight:600;color:#0f172a;font-size:.875rem">{{ $hh?->household_name ?? $delivery->recipient_name }}</div>
                          <div style="font-size:.78rem;color:#94a3b8">{{ $delivery->recipient_cccd }}</div>
                        </td>
                        <td style="padding:.6rem .875rem;color:#64748b;font-size:.82rem;max-width:180px">
                          {{ Str::limit($hh?->address ?? '—', 55) }}
                        </td>
                        <td style="padding:.6rem .875rem;text-align:center">
                          @if($hh?->priority_level)
                            <span style="background:{{ $pc[0] }};color:{{ $pc[1] }};padding:.2rem .5rem;border-radius:999px;font-size:.72rem;font-weight:700">
                              {{ $pc[2] }} Cấp {{ $hh->priority_level }}
                            </span>
                          @else
                            <span style="color:#94a3b8">—</span>
                          @endif
                        </td>
                        <td style="padding:.6rem .875rem;text-align:center">
                          <span style="background:{{ $sm[1] }};color:{{ $sm[2] }};padding:.25rem .65rem;border-radius:999px;font-size:.72rem;font-weight:600;white-space:nowrap">
                            {{ $sm[0] }}
                          </span>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>

        </div>

        {{-- ===== CỘT PHẢI: Actions ===== --}}
        <div style="position:sticky;top:1rem">

          {{-- CẬP NHẬT TRẠNG THÁI --}}
          @php
            $nextStatuses = match($trip->status) {
              'preparing' => [['exporting', '📤 Xuất kho', '#8b5cf6']],
              'exporting' => [['shipping', '🚛 Bắt đầu giao', '#3b82f6']],
              'shipping'  => [['completed', '✅ Hoàn thành', '#10b981']],
              default     => [],
            };
          @endphp

          @if(!empty($nextStatuses) && !$trip->isCancelled())
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem;margin-bottom:1rem">
              <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:1rem">⚡ Cập nhật trạng thái</h3>
              @foreach($nextStatuses as [$status, $label, $color])
                <form method="POST" action="{{ route('admin.trips.updateStatus', $trip) }}"
                      onsubmit="return confirm('Xác nhận chuyển sang: {{ $label }}?')">
                  @csrf
                  <input type="hidden" name="status" value="{{ $status }}">
                  <button type="submit"
                          style="width:100%;background:{{ $color }};color:#fff;border:none;padding:1rem;border-radius:10px;font-size:1rem;font-weight:700;cursor:pointer;margin-bottom:.5rem">
                    {{ $label }}
                  </button>
                </form>
              @endforeach
            </div>
          @endif

          {{-- HUỶ --}}
          @if($trip->isPreparing())
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem;margin-bottom:1rem">
              <h3 style="font-size:.875rem;font-weight:700;color:#ef4444;margin-bottom:.875rem">⚠️ Tuỳ chọn nguy hiểm</h3>
              <form method="POST" action="{{ route('admin.trips.updateStatus', $trip) }}"
                    onsubmit="return confirm('Xác nhận HUỶ chuyến xe này?')">
                @csrf
                <input type="hidden" name="status" value="cancelled">
                <button type="submit"
                        style="width:100%;background:#fff;color:#ef4444;border:2px solid #ef4444;padding:.75rem;border-radius:10px;font-size:.9rem;font-weight:700;cursor:pointer">
                  ❌ Huỷ chuyến xe
                </button>
              </form>
            </div>
          @endif

          {{-- THÔNG TIN TÀI XẾ --}}
          <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.25rem;margin-bottom:1rem">
            <h4 style="font-size:.875rem;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:.875rem">Tài xế phụ trách</h4>
            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem">
              <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0891b2);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#fff;font-weight:700">
                {{ substr($trip->driver?->name ?? '?', 0, 1) }}
              </div>
              <div>
                <div style="font-weight:700;color:#0f172a">{{ $trip->driver?->name ?? '—' }}</div>
                <div style="font-size:.8rem;color:#64748b">{{ $trip->driver?->email ?? '—' }}</div>
              </div>
            </div>
          </div>

          {{-- THÔNG TIN KHO --}}
          <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.25rem">
            <h4 style="font-size:.875rem;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:.875rem">Kho xuất hàng</h4>
            <div style="font-size:.875rem;color:#475569;line-height:1.8">
              <div>🏭 <strong>{{ $trip->warehouse?->name ?? '—' }}</strong></div>
              <div style="font-size:.82rem;color:#94a3b8">{{ $trip->warehouse?->address ?? '—' }}</div>
              <div style="margin-top:.5rem;font-size:.8rem;color:#64748b">
                📅 Tạo bởi: {{ $trip->creator?->name ?? '—' }}<br>
                🕐 {{ $trip->created_at->format('H:i d/m/Y') }}
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>
</div>
@endsection

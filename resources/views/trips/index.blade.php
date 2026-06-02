@extends('layouts.app')
@section('title', 'Quản lý Chuyến xe - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  @include('partials.admin-sidebar', ['activeMenu' => 'trips'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '🚛 Quản lý Chuyến xe'])

    <div style="padding:1.5rem">

      {{-- FLASH --}}
      @if(session('success'))
        <div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:.875rem 1.25rem;border-radius:10px;margin-bottom:1.25rem;font-weight:500">
          ✅ {!! session('success') !!}
        </div>
      @endif
      @if(session('error'))
        <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:.875rem 1.25rem;border-radius:10px;margin-bottom:1.25rem;font-weight:500">
          ❌ {{ session('error') }}
        </div>
      @endif

      {{-- STAT PILLS + ACTION BUTTONS --}}
      <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.25rem">
        @foreach([
          ['all','Tất cả',$statusCounts['all'],'#64748b','#f1f5f9'],
          ['preparing','Chuẩn bị',$statusCounts['preparing'],'#f59e0b','#fef3c7'],
          ['exporting','Xuất kho',$statusCounts['exporting'],'#8b5cf6','#ede9fe'],
          ['shipping','Đang giao',$statusCounts['shipping'],'#3b82f6','#dbeafe'],
          ['completed','Hoàn thành',$statusCounts['completed'],'#10b981','#d1fae5'],
          ['cancelled','Đã huỷ',$statusCounts['cancelled'],'#ef4444','#fee2e2'],
        ] as [$val,$label,$count,$fg,$bg])
          <a href="{{ route('admin.trips.index', ['status' => $val === 'all' ? null : $val]) }}"
             style="display:inline-flex;align-items:center;gap:.4rem;padding:.4rem 1rem;border-radius:999px;font-size:.8rem;font-weight:600;text-decoration:none;
                    background:{{ request('status',$val==='all'?null:null) === ($val==='all'?null:$val) || (!request('status') && $val==='all') ? $bg : '#f8fafc' }};
                    color:{{ request('status',$val==='all'?null:null) === ($val==='all'?null:$val) || (!request('status') && $val==='all') ? $fg : '#64748b' }};
                    border:1px solid {{ $fg }}">
            {{ $label }} <span style="background:{{ $fg }};color:#fff;border-radius:999px;padding:.05rem .45rem;font-size:.7rem">{{ $count }}</span>
          </a>
        @endforeach

        <div style="margin-left:auto;display:flex;gap:.6rem">
          {{-- Nút Xuất Excel --}}
          <button onclick="document.getElementById('modal-export-trip').style.display='flex'"
                  style="display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(135deg,#059669,#0d9488);color:#fff;padding:.5rem 1.15rem;border-radius:8px;font-size:.875rem;font-weight:700;border:none;cursor:pointer;box-shadow:0 2px 8px rgba(13,148,136,.25)">
            📥 Xuất Excel
          </button>
          <a href="{{ route('admin.trips.create') }}"
             style="display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(135deg,#0d9488,#0891b2);color:#fff;padding:.5rem 1.25rem;border-radius:8px;font-size:.875rem;font-weight:700;text-decoration:none">
            ➕ Tạo chuyến xe
          </a>
        </div>
      </div>

      {{-- MODAL XUẤT EXCEL CHUYẾN XE --}}
      <div id="modal-export-trip"
           style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center"
           onclick="if(event.target===this)this.style.display='none'">
        <div style="background:#fff;border-radius:16px;padding:2rem;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
            <div>
              <h3 style="font-size:1.1rem;font-weight:700;color:#0f172a;margin:0">📥 Xuất Báo Cáo Chuyến Xe</h3>
              <p style="font-size:.8rem;color:#64748b;margin:.25rem 0 0">Tuỳ chỉnh bộ lọc trước khi xuất file Excel</p>
            </div>
            <button onclick="document.getElementById('modal-export-trip').style.display='none'"
                    style="background:#f1f5f9;border:none;width:32px;height:32px;border-radius:8px;font-size:1.1rem;cursor:pointer;color:#64748b">✕</button>
          </div>

          <form method="GET" action="{{ route('admin.reports.trips.export') }}">
            {{-- Trạng thái --}}
            <div style="margin-bottom:1rem">
              <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem">Trạng thái</label>
              <select name="status"
                      style="width:100%;padding:.6rem .9rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.88rem;color:#0f172a;background:#fff">
                <option value="">Tất cả trạng thái</option>
                <option value="preparing">📋 Chuẩn bị</option>
                <option value="exporting">📤 Xuất kho</option>
                <option value="shipping">🚛 Đang giao</option>
                <option value="completed">✅ Hoàn thành</option>
                <option value="cancelled">❌ Đã huỷ</option>
              </select>
            </div>

            {{-- Khoảng thời gian --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.5rem">
              <div>
                <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem">Từ ngày</label>
                <input type="date" name="date_from"
                       style="width:100%;padding:.6rem .9rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.88rem;color:#0f172a">
              </div>
              <div>
                <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem">Đến ngày</label>
                <input type="date" name="date_to"
                       style="width:100%;padding:.6rem .9rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.88rem;color:#0f172a">
              </div>
            </div>

            {{-- Preview info --}}
            <div style="background:#eff6ff;border:1px solid #93c5fd;border-radius:8px;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.82rem;color:#1e40af">
              📊 File Excel sẽ bao gồm: <strong>STT, Mã chuyến, Tài xế, Kho xuất, Phương tiện, Số điểm giao, Đã giao, Trạng thái, Ngày tạo, Ngày xuất kho, Ngày hoàn thành</strong>
            </div>

            <div style="display:flex;gap:.75rem">
              <button type="button" onclick="document.getElementById('modal-export-trip').style.display='none'"
                      style="flex:1;padding:.7rem;border:1.5px solid #e2e8f0;background:#fff;border-radius:8px;font-weight:600;color:#64748b;cursor:pointer">
                Huỷ
              </button>
              <button type="submit"
                      style="flex:2;padding:.7rem;background:linear-gradient(135deg,#059669,#0d9488);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:.95rem;box-shadow:0 2px 8px rgba(13,148,136,.3)">
                📥 Tải xuống Excel
              </button>
            </div>
          </form>
        </div>
      </div>

      {{-- SEARCH --}}
      <form method="GET" action="{{ route('admin.trips.index') }}"
            style="display:flex;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap">
        @if(request('status'))
          <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="🔍 Tìm mã chuyến, tên tài xế..."
               style="flex:1;min-width:220px;padding:.6rem 1rem;border:1px solid #e2e8f0;border-radius:8px;font-size:.875rem">
        <button type="submit"
                style="padding:.6rem 1.25rem;background:#0d9488;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer">
          Tìm
        </button>
        @if(request()->hasAny(['search','status']))
          <a href="{{ route('admin.trips.index') }}"
             style="padding:.6rem 1rem;border:1px solid #e2e8f0;border-radius:8px;color:#64748b;font-size:.875rem;text-decoration:none">
            ✕ Xoá lọc
          </a>
        @endif
      </form>

      {{-- TABLE --}}
      <div class="table-wrap">
        <table style="width:100%;border-collapse:collapse">
          <thead>
            <tr style="background:#f8fafc">
              @foreach(['Mã chuyến','Tài xế','Kho xuất','Phương tiện','Hàng hoá','Đã giao','Trạng thái','Ngày tạo',''] as $h)
                <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0;white-space:nowrap">{{ $h }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
          @forelse($trips as $trip)
              @php
                $doneCount  = $trip->deliveries->whereIn('status',['success','warning'])->count();
                $totalCount = $trip->deliveries->count();
              @endphp
              <tr data-trip-id="{{ $trip->id }}" data-trip-status="{{ $trip->status }}"
                  style="border-bottom:1px solid #f1f5f9;transition:background .3s"
                  onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <td style="padding:.75rem 1rem;font-family:monospace;font-weight:700;color:#0d9488">
                  {{ $trip->trip_code }}
                </td>
                <td style="padding:.75rem 1rem;font-weight:500;color:#0f172a">
                  {{ $trip->driver?->name ?? '—' }}
                </td>
                <td style="padding:.75rem 1rem;color:#475569">
                  {{ $trip->warehouse?->name ?? '—' }}
                </td>
                <td style="padding:.75rem 1rem;color:#475569;font-size:.85rem">
                  {{ $trip->vehicle_info }}
                </td>
                <td style="padding:.75rem 1rem;text-align:center;font-weight:600;color:#0f172a">
                  {{ $trip->tripDetails->count() }} loại
                </td>
                {{-- Cột số điểm giao (realtime qua WebSocket) --}}
                <td style="padding:.75rem 1rem;text-align:center" class="col-deliveries">
                  @if($totalCount > 0)
                    <span style="color:{{ $doneCount==$totalCount?'#059669':'#3b82f6' }};font-weight:600">
                      {{ $doneCount }}/{{ $totalCount }}
                    </span>
                    <span style="color:#94a3b8;font-size:.75rem"> điểm</span>
                  @else
                    <span style="color:#94a3b8">—</span>
                  @endif
                </td>
                <td style="padding:.75rem 1rem">
                  <span class="trip-status-badge"
                        style="background:{{ $trip->status_bg }};color:{{ $trip->status_color }};padding:.25rem .75rem;border-radius:999px;font-size:.75rem;font-weight:600;white-space:nowrap">
                    {{ $trip->status_label }}
                  </span>
                </td>
                <td style="padding:.75rem 1rem;color:#64748b;font-size:.82rem;white-space:nowrap">
                  {{ $trip->created_at->format('d/m/Y H:i') }}
                </td>
                <td style="padding:.75rem 1rem;text-align:center">
                  <div style="display:flex;gap:.4rem;justify-content:center">
                    <a href="{{ route('admin.trips.show', $trip) }}"
                       style="background:#0d9488;color:#fff;padding:.3rem .75rem;border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none">
                      👁️ Xem
                    </a>
                    @if(in_array($trip->status, ['preparing','cancelled']))
                      <form method="POST" action="{{ route('admin.trips.destroy', $trip) }}"
                            onsubmit="return confirm('Xoá chuyến xe {{ $trip->trip_code }}?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="background:#fee2e2;color:#ef4444;border:none;padding:.3rem .75rem;border-radius:6px;font-size:.75rem;font-weight:600;cursor:pointer">
                          🗑️
                        </button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" style="padding:3rem;text-align:center;color:#94a3b8">
                  <div style="font-size:3rem;margin-bottom:.5rem">🚛</div>
                  Chưa có chuyến xe nào
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @include('partials.pagination', ['paginator' => $trips])

    </div>
  </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // WebSocket Realtime: lắng nghe cập nhật chuyến xe đang giao
  if(!window.Echo) return;

  // Lấy tất cả trip IDs đang shipping/exporting từ data attribute trên các row
  const activeRows = document.querySelectorAll('tr[data-trip-id][data-trip-status]');
  const activeIds  = [];

  activeRows.forEach(row => {
    const status = row.dataset.tripStatus;
    if(status === 'shipping' || status === 'exporting'){
      activeIds.push(parseInt(row.dataset.tripId));
    }
  });

  if(!activeIds.length) return;

  activeIds.forEach(tripId => {
    // Lắng nghe cập nhật số điểm giao
    window.Echo.private(`deliveries.${tripId}`)
      .listen('.DeliveryUpdated', (e) => {
        const row = document.querySelector(`tr[data-trip-id="${tripId}"]`);
        if(!row) return;

        // Cập nhật cột "Đã giao"
        const delivCell = row.querySelector('.col-deliveries');
        if(delivCell){
          delivCell.textContent = `${e.success_count}/${e.total_count} điểm`;
        }

        // Flash màu để báo cập nhật
        row.style.background = '#ecfdf5';
        setTimeout(() => row.style.background = '', 1500);
      });

    // Lắng nghe khi chuyến hoàn thành → đổi badge trạng thái
    window.Echo.private(`trips.${tripId}`)
      .listen('.TripStatusUpdated', (e) => {
        const row = document.querySelector(`tr[data-trip-id="${tripId}"]`);
        if(!row) return;

        const badge = row.querySelector('.trip-status-badge');
        if(badge && e.status === 'completed'){
          badge.textContent = '✅ Hoàn thành';
          badge.style.background = '#d1fae5';
          badge.style.color = '#065f46';
        }

        row.style.background = '#d1fae5';
        setTimeout(() => row.style.background = '', 2000);

        // Toast thông báo
        showAdminToast(`🎉 Chuyến ${e.trip_code} đã hoàn thành!`, 'success');
      });
  });
});

// Toast nhỏ cho admin trips page
function showAdminToast(msg, type='info'){
  const colors = { info:'#0f172a', success:'#059669', warning:'#d97706' };
  const el = document.createElement('div');
  el.style.cssText = `position:fixed;bottom:1.5rem;right:1.5rem;background:${colors[type]||colors.info};color:#fff;padding:.65rem 1.2rem;border-radius:9px;font-size:.82rem;font-weight:600;z-index:9999;opacity:0;transition:opacity .3s;box-shadow:0 4px 20px rgba(0,0,0,.25)`;
  el.textContent = msg;
  document.body.appendChild(el);
  requestAnimationFrame(() => el.style.opacity = 1);
  setTimeout(() => { el.style.opacity = 0; setTimeout(() => el.remove(), 300); }, 4000);
}
</script>
@endpush

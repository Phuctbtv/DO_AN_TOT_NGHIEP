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

      {{-- STAT PILLS --}}
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

        <a href="{{ route('admin.trips.create') }}"
           style="margin-left:auto;display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(135deg,#0d9488,#0891b2);color:#fff;padding:.5rem 1.25rem;border-radius:8px;font-size:.875rem;font-weight:700;text-decoration:none">
          ➕ Tạo chuyến xe
        </a>
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
              @foreach(['Mã chuyến','Tài xế','Kho xuất','Phương tiện','Hàng hoá','Trạng thái','Ngày tạo',''] as $h)
                <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0;white-space:nowrap">{{ $h }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @forelse($trips as $trip)
              <tr style="border-bottom:1px solid #f1f5f9" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
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
                <td style="padding:.75rem 1rem">
                  <span style="background:{{ $trip->status_bg }};color:{{ $trip->status_color }};padding:.25rem .75rem;border-radius:999px;font-size:.75rem;font-weight:600;white-space:nowrap">
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
                <td colspan="8" style="padding:3rem;text-align:center;color:#94a3b8">
                  <div style="font-size:3rem;margin-bottom:.5rem">🚛</div>
                  Chưa có chuyến xe nào
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($trips->hasPages())
        <div style="margin-top:1.25rem">{{ $trips->links() }}</div>
      @endif

    </div>
  </main>
</div>
@endsection

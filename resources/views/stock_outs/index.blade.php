@extends('layouts.app')
@section('title', 'Danh sách chờ xuất kho - ĐẠI PHÚC')

@push('styles')
<style>
  /* ── Trip card hover animation ──────────────────────────── */
  .trip-row { transition: background .15s; }
  .trip-row:hover { background: #f8fafc !important; }

  .btn-export {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .45rem 1rem;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: #fff; border: none; border-radius: 8px;
    font-size: .82rem; font-weight: 700; cursor: pointer;
    text-decoration: none; white-space: nowrap;
    box-shadow: 0 4px 12px rgba(139,92,246,.35);
    transition: all .2s;
  }
  .btn-export:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(139,92,246,.45); }

  .status-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .25rem .75rem; border-radius: 20px;
    font-size: .78rem; font-weight: 700;
  }
  .pulse-dot {
    width: 7px; height: 7px; border-radius: 50%; background: #f59e0b;
    animation: pulse 1.5s infinite;
  }
  @keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .5; transform: scale(.7); }
  }
</style>
@endpush

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  {{-- SIDEBAR --}}
  @include('partials.warehouse-sidebar', ['activeMenu' => 'stock_outs'])

  {{-- MAIN --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '📤 Xuất kho'])

    <div style="padding:1.5rem">

      {{-- FLASH MESSAGES --}}
      @if(session('success'))
        <div style="background:#dcfce7;color:#15803d;border:1px solid #86efac;border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1.25rem;font-size:.9rem;display:flex;align-items:center;gap:.6rem">
          {!! session('success') !!}
        </div>
      @endif
      @if(session('error'))
        <div style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1.25rem;font-size:.9rem">
          {!! session('error') !!}
        </div>
      @endif

      {{-- HEADER INFO CARD --}}
      <div style="
        display:flex;align-items:center;gap:1rem;
        padding:.9rem 1.25rem;
        background:linear-gradient(135deg,#faf5ff,#ede9fe);
        border:1.5px solid #c4b5fd;border-radius:12px;
        margin-bottom:1.5rem;
      ">
        <div style="font-size:1.8rem">📤</div>
        <div style="flex:1">
          <div style="font-size:.78rem;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:.5px">Chờ xuất kho</div>
          <div style="font-size:.95rem;font-weight:700;color:#1e1b4b">Danh sách chuyến xe cần xác nhận xuất kho</div>
          <div style="font-size:.8rem;color:#6d28d9">Chỉ hiển thị chuyến thuộc kho bạn quản lý — trạng thái: Chuẩn bị</div>
        </div>
        <div style="text-align:right">
          <div style="font-size:2rem;font-weight:800;color:#7c3aed">{{ $trips->total() }}</div>
          <div style="font-size:.75rem;color:#8b5cf6">chuyến chờ xuất</div>
        </div>
      </div>

      {{-- TABLE CARD --}}
      <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;box-shadow:0 2px 12px rgba(0,0,0,.05);overflow:hidden">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
          <h3 style="font-size:1rem;font-weight:700;color:#0f172a">🚛 Chuyến xe chờ xuất kho</h3>
          <span style="font-size:.82rem;color:#64748b">{{ $trips->total() }} chuyến</span>
        </div>

        <div style="overflow-x:auto">
          <table style="width:100%;border-collapse:collapse;font-size:.9rem">
            <thead>
              <tr style="background:#f8fafc">
                @foreach(['Mã chuyến','Tài xế','Kho xuất','Số hàng hoá','Ngày tạo','Thao tác'] as $h)
                  <th style="padding:.75rem 1rem;text-align:{{ $loop->last ? 'center' : 'left' }};font-size:.73rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0;white-space:nowrap">
                    {{ $h }}
                  </th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @forelse($trips as $trip)
                <tr class="trip-row" style="border-bottom:1px solid #f1f5f9">

                  {{-- Mã chuyến --}}
                  <td style="padding:.85rem 1rem">
                    <span style="font-family:monospace;font-weight:800;color:#7c3aed;background:#ede9fe;padding:.2rem .6rem;border-radius:6px;font-size:.85rem">
                      {{ $trip->trip_code }}
                    </span>
                    <div style="margin-top:.25rem">
                      <span class="status-badge" style="background:#fef3c7;color:#92400e">
                        <span class="pulse-dot"></span>
                        Chuẩn bị
                      </span>
                    </div>
                  </td>

                  {{-- Tài xế --}}
                  <td style="padding:.85rem 1rem">
                    <div style="display:flex;align-items:center;gap:.5rem">
                      <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#6366f1);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:800;flex-shrink:0">
                        {{ strtoupper(substr($trip->driver->name ?? 'D', 0, 1)) }}
                      </div>
                      <div>
                        <div style="font-weight:600;color:#1e293b;font-size:.88rem">{{ $trip->driver->name ?? '—' }}</div>
                        <div style="font-size:.75rem;color:#94a3b8">{{ $trip->vehicle_info }}</div>
                      </div>
                    </div>
                  </td>

                  {{-- Kho xuất --}}
                  <td style="padding:.85rem 1rem">
                    <span style="background:#dbeafe;color:#1d4ed8;padding:.25rem .7rem;border-radius:20px;font-size:.8rem;font-weight:600;white-space:nowrap">
                      🏭 {{ $trip->warehouse->name ?? '—' }}
                    </span>
                  </td>

                  {{-- Số mặt hàng --}}
                  <td style="padding:.85rem 1rem">
                    <span style="font-size:1.1rem;font-weight:800;color:#0f172a">{{ $trip->tripDetails->count() }}</span>
                    <span style="font-size:.78rem;color:#94a3b8;margin-left:.2rem">loại hàng</span>
                  </td>

                  {{-- Ngày tạo --}}
                  <td style="padding:.85rem 1rem;white-space:nowrap">
                    <div style="font-size:.88rem;font-weight:600;color:#1e293b">{{ $trip->created_at->format('d/m/Y') }}</div>
                    <div style="font-size:.75rem;color:#94a3b8">{{ $trip->created_at->format('H:i') }}</div>
                  </td>

                  {{-- Thao tác --}}
                  <td style="padding:.85rem 1rem;text-align:center">
                    <a href="{{ route('warehouse.stock_outs.show', $trip) }}" class="btn-export">
                      📤 Xác nhận xuất
                    </a>
                  </td>

                </tr>
              @empty
                <tr>
                  <td colspan="6" style="padding:4rem;text-align:center;color:#94a3b8">
                    <div style="font-size:3rem;margin-bottom:.75rem">🎉</div>
                    <div style="font-weight:600;color:#64748b;font-size:1rem;margin-bottom:.3rem">Không có chuyến nào chờ xuất kho</div>
                    <div style="font-size:.85rem">Tất cả chuyến xe đang chuẩn bị đều đã được xử lý!</div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @include('partials.pagination', ['paginator' => $trips])
      </div>

    </div>
  </main>
</div>
@endsection

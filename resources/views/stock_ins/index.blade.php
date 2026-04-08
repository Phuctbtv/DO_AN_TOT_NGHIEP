@extends('layouts.app')
@section('title', 'Lịch sử nhập kho - ĐẠI PHÚC')

@push('styles')
<style>
  .si-thumb {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: transform .2s;
  }
  .si-thumb:hover { transform: scale(1.1); }
  .si-no-img {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: #cbd5e0;
  }

  /* Lightbox */
  .lb-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.85);
    display: flex; align-items: center; justify-content: center;
    z-index: 9999;
  }
  .lb-overlay img {
    max-width: 90vw; max-height: 85vh;
    border-radius: 12px;
    box-shadow: 0 24px 64px rgba(0,0,0,.5);
  }
  .lb-close {
    position: absolute; top: 1.5rem; right: 1.5rem;
    background: rgba(255,255,255,.15);
    border: none; color: #fff; border-radius: 50%;
    width: 40px; height: 40px; font-size: 1.2rem;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
  }
</style>
@endpush

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true, lb: null }">

  {{-- SIDEBAR --}}
  @include('partials.warehouse-sidebar', ['activeMenu' => 'stock_ins'])

  {{-- MAIN --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '📋 Lịch sử nhập kho'])

    <div style="padding:1.5rem">

      {{-- BANNER KHO ĐANG QUẢN LÝ --}}
      @php $myWarehouses = $warehouses; @endphp
      @if($myWarehouses->isNotEmpty())
        <div style="
          display:flex;align-items:center;gap:1rem;
          padding:.85rem 1.25rem;
          background:linear-gradient(135deg,#f0fdfa,#e0f2fe);
          border:1.5px solid #5eead4;
          border-radius:12px;
          margin-bottom:1.25rem;
        ">
          <div style="font-size:1.4rem">🏭</div>
          <div style="flex:1">
            @if($myWarehouses->count() === 1)
              <span style="font-size:.78rem;color:#0d9488;font-weight:700;text-transform:uppercase;letter-spacing:.4px">Kho của bạn</span>
              <div style="font-weight:700;color:#0f172a;font-size:.95rem">{{ $myWarehouses->first()->name }}</div>
              <div style="font-size:.78rem;color:#64748b">📍 {{ $myWarehouses->first()->address }}</div>
            @else
              <span style="font-size:.78rem;color:#0d9488;font-weight:700;text-transform:uppercase;letter-spacing:.4px">Bạn quản lý {{ $myWarehouses->count() }} kho</span>
              <div style="font-size:.82rem;color:#64748b">{{ $myWarehouses->pluck('name')->join(' · ') }}</div>
            @endif
          </div>
          <a href="{{ route('warehouse.stock_ins.create') }}"
             style="padding:.55rem 1.1rem;background:#0d9488;color:#fff;border-radius:8px;font-size:.82rem;font-weight:700;text-decoration:none;white-space:nowrap">
            📥 Nhập hàng
          </a>
        </div>
      @endif

      {{-- FLASH MESSAGES --}}
      @if(session('success'))
        <div style="background:#dcfce7;color:#15803d;border:1px solid #86efac;border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1.25rem;font-size:.9rem;display:flex;align-items:center;gap:.6rem">
          {!! session('success') !!}
        </div>
      @endif
      @if(session('error'))
        <div style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1.25rem;font-size:.9rem">
          ❌ {{ session('error') }}
        </div>
      @endif

      {{-- TOOLBAR --}}
      <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.25rem;flex-wrap:wrap">
        {{-- Search & Filter --}}
        <form method="GET" action="{{ route('warehouse.stock_ins.index') }}"
              style="display:flex;gap:.6rem;flex-wrap:wrap;flex:1">
          <input type="text" name="search"
                 value="{{ request('search') }}"
                 placeholder="🔍 Tìm theo mặt hàng..."
                 style="flex:1;min-width:180px;padding:.55rem .9rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:.88rem;outline:none;font-family:inherit">

          @if($warehouses->count() > 1)
            <select name="warehouse_id"
                    style="padding:.55rem .9rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:.88rem;font-family:inherit;background:#fff">
              <option value="">Tất cả kho</option>
              @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                  {{ $wh->name }}
                </option>
              @endforeach
            </select>
          @endif

          <button type="submit"
                  style="padding:.55rem 1.1rem;background:#0d9488;color:#fff;border:none;border-radius:8px;font-size:.88rem;font-weight:700;cursor:pointer;font-family:inherit">
            Lọc
          </button>
          @if(request()->anyFilled(['search','warehouse_id']))
            <a href="{{ route('warehouse.stock_ins.index') }}"
               style="padding:.55rem 1rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:.88rem;color:#64748b;text-decoration:none;display:flex;align-items:center">
              ✕ Xóa lọc
            </a>
          @endif
        </form>

        {{-- New Button --}}
        <a href="{{ route('warehouse.stock_ins.create') }}"
           style="padding:.6rem 1.2rem;background:linear-gradient(135deg,#0d9488,#0891b2);color:#fff;border-radius:8px;font-size:.88rem;font-weight:700;text-decoration:none;white-space:nowrap;box-shadow:0 4px 12px rgba(13,148,136,.3);transition:all .2s"
           onmouseover="this.style.transform='translateY(-1px)'"
           onmouseout="this.style.transform=''">
          📥 Tạo phiếu nhập mới
        </a>
      </div>

      {{-- TABLE CARD --}}
      <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;box-shadow:0 2px 12px rgba(0,0,0,.05);overflow:hidden">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
          <h3 style="font-size:1rem;font-weight:700;color:#0f172a">
            📦 Danh sách phiếu nhập kho
          </h3>
          <span style="font-size:.82rem;color:#64748b">
            {{ $stockIns->total() }} phiếu
          </span>
        </div>

        <div style="overflow-x:auto">
          @php $singleWarehouse = $warehouses->count() === 1; @endphp
          <table style="width:100%;border-collapse:collapse;font-size:.9rem">
            <thead>
              <tr style="background:#f8fafc">
                <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0;white-space:nowrap">STT</th>
                <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0;white-space:nowrap">Ngày nhập</th>
                @if(!$singleWarehouse)
                <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Kho</th>
                @endif
                <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Mặt hàng</th>
                <th style="padding:.75rem 1rem;text-align:right;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0;white-space:nowrap">Số lượng</th>
                <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Nguồn tài trợ</th>
                <th style="padding:.75rem 1rem;text-align:center;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Ảnh</th>
                <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Người nhập</th>
                <th style="padding:.75rem 1rem;text-align:center;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Hành động</th>
              </tr>
            </thead>
            <tbody>
              @forelse($stockIns as $si)
                <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s"
                    onmouseover="this.style.background='#f8fafc'"
                    onmouseout="this.style.background=''">

                  {{-- STT --}}
                  <td style="padding:.8rem 1rem;color:#9ca3af;font-size:.82rem">
                    {{ ($stockIns->currentPage() - 1) * $stockIns->perPage() + $loop->iteration }}
                  </td>

                  {{-- Ngày nhập --}}
                  <td style="padding:.8rem 1rem;white-space:nowrap">
                    <div style="font-weight:600;color:#1e293b;font-size:.88rem">
                      {{ $si->received_date->format('d/m/Y') }}
                    </div>
                    <div style="font-size:.75rem;color:#94a3b8">
                      {{ $si->received_date->format('H:i') }}
                    </div>
                  </td>

                  {{-- Kho --}}
                  @if(!$singleWarehouse)
                  <td style="padding:.8rem 1rem">
                    <span style="background:#dbeafe;color:#1d4ed8;padding:.2rem .65rem;border-radius:20px;font-size:.8rem;font-weight:600;white-space:nowrap">
                      🏭 {{ $si->warehouse->name ?? '—' }}
                    </span>
                  </td>
                  @endif

                  {{-- Mặt hàng --}}
                  <td style="padding:.8rem 1rem">
                    <div style="font-weight:700;color:#1e293b">{{ $si->supply->name ?? '—' }}</div>
                    <div style="font-size:.78rem;color:#94a3b8">{{ $si->supply->unit ?? '' }}</div>
                  </td>

                  {{-- Số lượng --}}
                  <td style="padding:.8rem 1rem;text-align:right">
                    <span style="font-size:1.05rem;font-weight:800;color:#0d9488">
                      +{{ number_format($si->quantity) }}
                    </span>
                    <span style="font-size:.78rem;color:#94a3b8;margin-left:.25rem">{{ $si->supply->unit ?? '' }}</span>
                  </td>

                  {{-- Nguồn tài trợ --}}
                  <td style="padding:.8rem 1rem;max-width:180px">
                    @if($si->donor_info)
                      <span style="font-size:.85rem;color:#374151">{{ $si->donor_info }}</span>
                    @else
                      <span style="color:#d1d5db;font-style:italic;font-size:.82rem">—</span>
                    @endif
                  </td>

                  {{-- Ảnh thumbnail --}}
                  <td style="padding:.8rem 1rem;text-align:center">
                    @if($si->image_url)
                      <img src="{{ $si->image_url }}"
                           alt="Ảnh hàng hóa"
                           class="si-thumb"
                           @click="lb = $event.target.src"
                           title="Nhấn để xem ảnh đầy đủ">
                    @else
                      <div class="si-no-img">📷</div>
                    @endif
                  </td>

                  {{-- Người nhập --}}
                  <td style="padding:.8rem 1rem">
                    <div style="display:flex;align-items:center;gap:.5rem">
                      <div style="width:28px;height:28px;border-radius:50%;background:#0d9488;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0">
                        {{ strtoupper(substr($si->creator->name ?? 'U', 0, 1)) }}
                      </div>
                      <span style="font-size:.85rem;color:#374151">{{ $si->creator->name ?? '—' }}</span>
                    </div>
                  </td>

                  {{-- Hành động --}}
                  <td style="padding:.8rem 1rem;text-align:center">
                    <div style="display:flex;gap:.4rem;justify-content:center">
                      <a href="{{ route('warehouse.stock_ins.show', $si) }}"
                         style="padding:.3rem .7rem;background:#dbeafe;color:#1d4ed8;border-radius:6px;font-size:.8rem;text-decoration:none;white-space:nowrap">
                        👁️ Xem
                      </a>
                      <form action="{{ route('warehouse.stock_ins.destroy', $si) }}" method="POST"
                            onsubmit="return confirm('⚠️ Xác nhận xóa phiếu nhập này?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="padding:.3rem .7rem;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:.8rem;cursor:pointer;white-space:nowrap">
                          🗑️ Xóa
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="{{ $singleWarehouse ? 7 : 8 }}" style="padding:3rem;text-align:center;color:#94a3b8">
                    <div style="font-size:2.5rem;margin-bottom:.75rem">📭</div>
                    <div style="font-weight:600;color:#64748b;margin-bottom:.3rem">Chưa có phiếu nhập nào</div>
                    <div style="font-size:.85rem">
                      <a href="{{ route('warehouse.stock_ins.create') }}" style="color:#0d9488;text-decoration:none">
                        ➕ Tạo phiếu nhập đầu tiên
                      </a>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- PAGINATION --}}
        @if($stockIns->hasPages())
          <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end">
            {{ $stockIns->links() }}
          </div>
        @endif
      </div>

    </div>{{-- end padding --}}

    {{-- LIGHTBOX --}}
    <div x-show="lb"
         style="display:none"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="lb-overlay"
         @click="lb = null">
      <button class="lb-close" @click.stop="lb = null">✕</button>
      <img :src="lb" alt="Ảnh hàng hóa" @click.stop>
    </div>

  </main>
</div>
@endsection

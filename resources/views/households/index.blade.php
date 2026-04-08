@extends('layouts.app')
@section('title', 'Quản lý Hộ dân - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  @include('partials.admin-sidebar', ['activeMenu' => 'households'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '🏠 Quản lý Hộ dân'])

    <div style="padding:1.5rem">

      {{-- FLASH MESSAGES --}}
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

      {{-- HEADER + FILTER --}}
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:1rem">
        <div>
          <h2 style="font-size:1.25rem;font-weight:700;color:#0f172a;margin:0">Danh sách Hộ dân</h2>
          <p style="color:#64748b;font-size:.875rem;margin:0">Quản lý tất cả hộ dân đã đăng ký cứu trợ</p>
        </div>
        <a href="{{ route('admin.households.pending') }}"
           style="display:inline-flex;align-items:center;gap:.5rem;background:#f59e0b;color:#fff;padding:.6rem 1.25rem;border-radius:8px;font-weight:600;font-size:.875rem;text-decoration:none">
          ⏳ Chờ duyệt
          @if($pendingCount > 0)
            <span style="background:#fff;color:#f59e0b;border-radius:999px;padding:.1rem .5rem;font-size:.75rem;font-weight:700">{{ $pendingCount }}</span>
          @endif
        </a>
      </div>

      {{-- SEARCH + FILTER --}}
      <form method="GET" action="{{ route('admin.households.index') }}"
            style="display:flex;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="🔍 Tìm theo tên, CCCD, SĐT..."
               style="flex:1;min-width:200px;padding:.6rem 1rem;border:1px solid #e2e8f0;border-radius:8px;font-size:.875rem">
        <select name="status"
                style="padding:.6rem 1rem;border:1px solid #e2e8f0;border-radius:8px;font-size:.875rem;min-width:160px">
          <option value="">Tất cả trạng thái</option>
          <option value="pending"  @selected(request('status')==='pending')>⏳ Chờ duyệt</option>
          <option value="active"   @selected(request('status')==='active')>✅ Đã duyệt</option>
          <option value="rejected" @selected(request('status')==='rejected')>❌ Từ chối</option>
        </select>
        <button type="submit"
                style="padding:.6rem 1.25rem;background:#0d9488;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer">
          Lọc
        </button>
        @if(request()->hasAny(['search','status']))
          <a href="{{ route('admin.households.index') }}"
             style="padding:.6rem 1rem;border:1px solid #e2e8f0;border-radius:8px;color:#64748b;font-size:.875rem;text-decoration:none">
            ✕ Xoá bộ lọc
          </a>
        @endif
      </form>

      {{-- TABLE --}}
      <div class="table-wrap">
        <table style="width:100%;border-collapse:collapse">
          <thead>
            <tr style="background:#f8fafc">
              <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">#</th>
              <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Họ tên</th>
              <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">CCCD</th>
              <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">SĐT</th>
              <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Địa chỉ</th>
              <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Trạng thái</th>
              <th style="padding:.75rem 1rem;text-align:left;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Ngày đăng ký</th>
              <th style="padding:.75rem 1rem;text-align:center;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            @forelse($households as $hh)
              <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <td style="padding:.75rem 1rem;color:#94a3b8;font-size:.8rem">{{ $hh->id }}</td>
                <td style="padding:.75rem 1rem;font-weight:600;color:#0f172a">
                  {{ $hh->household_name }}
                  @if($hh->scene_image)
                    <span title="Có ảnh hiện trường" style="margin-left:.25rem">📸</span>
                  @endif
                </td>
                <td style="padding:.75rem 1rem;font-family:monospace;color:#475569">
                  {{ $hh->resident?->identity_card ?? '—' }}
                </td>
                <td style="padding:.75rem 1rem;color:#475569">{{ $hh->phone ?? '—' }}</td>
                <td style="padding:.75rem 1rem;color:#475569;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                  {{ $hh->address }}
                </td>
                <td style="padding:.75rem 1rem">
                  @php
                    $colors = ['pending'=>['#fef3c7','#f59e0b'], 'active'=>['#d1fae5','#10b981'], 'rejected'=>['#fee2e2','#ef4444']];
                    [$bg,$fg] = $colors[$hh->status] ?? ['#f1f5f9','#64748b'];
                  @endphp
                  <span style="background:{{ $bg }};color:{{ $fg }};padding:.25rem .75rem;border-radius:999px;font-size:.75rem;font-weight:600">
                    {{ $hh->status_label }}
                  </span>
                </td>
                <td style="padding:.75rem 1rem;color:#64748b;font-size:.82rem">
                  {{ $hh->created_at->format('d/m/Y H:i') }}
                </td>
                <td style="padding:.75rem 1rem;text-align:center">
                  <a href="{{ route('admin.households.show', $hh) }}?from=index"
                     style="background:#0d9488;color:#fff;padding:.35rem .85rem;border-radius:6px;font-size:.8rem;font-weight:600;text-decoration:none">
                    👁️ Xem
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" style="padding:2.5rem;text-align:center;color:#94a3b8">
                  <div style="font-size:2.5rem;margin-bottom:.5rem">📭</div>
                  Không có hộ dân nào
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- PAGINATION --}}
      @if($households->hasPages())
        <div style="margin-top:1.25rem">
          {{ $households->links() }}
        </div>
      @endif

    </div>
  </main>
</div>
@endsection

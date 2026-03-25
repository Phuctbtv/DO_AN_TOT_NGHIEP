@extends('layouts.app')
@section('title', 'Quản lý Nhu yếu phẩm - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  {{-- ==================== SIDEBAR ==================== --}}
  @include('partials.admin-sidebar', ['activeMenu' => 'supplies'])

  {{-- ==================== MAIN ==================== --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '🛒 Quản lý Nhu yếu phẩm'])
    <div style="padding:1.5rem">

      {{-- FLASH MESSAGES --}}
      @if(session('success'))
        <div style="background:#dcfce7;color:#15803d;border:1px solid #86efac;border-radius:8px;padding:.85rem 1.1rem;margin-bottom:1.25rem;font-size:.9rem;display:flex;align-items:center;gap:.5rem">
          ✅ {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;border-radius:8px;padding:.85rem 1.1rem;margin-bottom:1.25rem;font-size:.9rem;display:flex;align-items:center;gap:.5rem">
          ❌ {{ session('error') }}
        </div>
      @endif

      {{-- SEARCH + ADD BUTTON --}}
      <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.25rem;flex-wrap:wrap">
        <form method="GET" action="{{ route('admin.supplies.index') }}" style="display:flex;gap:.6rem;flex:1;max-width:420px">
          <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="🔍 Tìm theo tên nhu yếu phẩm..."
            style="flex:1;padding:.55rem .9rem;border:1px solid #d1d5db;border-radius:8px;font-size:.88rem;outline:none"
          >
          <button type="submit" class="btn btn-outline btn-sm">Tìm</button>
          @if(request('search'))
            <a href="{{ route('admin.supplies.index') }}" class="btn btn-outline btn-sm" style="color:#6b7280">✕ Xoá</a>
          @endif
        </form>
        <a href="{{ route('admin.supplies.create') }}" class="btn btn-primary" style="white-space:nowrap">
          + Thêm nhu yếu phẩm
        </a>
      </div>

      {{-- TABLE --}}
      <div class="table-wrap">
        <div class="table-header">
          <h3>📋 Danh sách nhu yếu phẩm
            @if(request('search'))
              <span style="font-size:.82rem;font-weight:400;color:#6b7280"> – kết quả cho "{{ request('search') }}"</span>
            @endif
          </h3>
          <span style="font-size:.82rem;color:#6b7280">{{ $supplies->total() }} mục</span>
        </div>

        <table>
          <thead>
            <tr>
              <th style="width:60px">STT</th>
              <th>Tên nhu yếu phẩm</th>
              <th>Danh mục</th>
              <th style="width:110px">Đơn vị</th>
              <th style="width:160px">Mức cảnh báo tồn</th>
              <th style="width:130px;text-align:center">Hành động</th>
            </tr>
          </thead>
          <tbody>
            @forelse($supplies as $supply)
              <tr>
                <td style="color:#9ca3af;font-size:.85rem">
                  {{ ($supplies->currentPage() - 1) * $supplies->perPage() + $loop->iteration }}
                </td>
                <td>
                  <strong style="color:#1e293b">{{ $supply->name }}</strong>
                </td>
                <td>
                  <span style="background:#eff6ff;color:#2563eb;padding:.2rem .6rem;border-radius:20px;font-size:.8rem">
                    {{ $supply->category?->name ?? '—' }}
                  </span>
                </td>
                <td style="font-size:.88rem">{{ $supply->unit }}</td>
                <td>
                  <span style="background:{{ $supply->min_stock_alert > 0 ? '#fef3c7' : '#f1f5f9' }};color:{{ $supply->min_stock_alert > 0 ? '#b45309' : '#64748b' }};padding:.2rem .6rem;border-radius:20px;font-size:.8rem">
                    {{ number_format($supply->min_stock_alert) }} {{ $supply->unit }}
                  </span>
                </td>
                <td style="text-align:center">
                  <div style="display:flex;gap:.4rem;justify-content:center">
                    <a href="{{ route('admin.supplies.edit', $supply) }}"
                       style="padding:.3rem .7rem;background:#dbeafe;color:#1d4ed8;border-radius:6px;font-size:.8rem;text-decoration:none">
                      ✏️ Sửa
                    </a>
                    <form action="{{ route('admin.supplies.destroy', $supply) }}" method="POST"
                          onsubmit="return confirm('Xác nhận xoá nhu yếu phẩm này?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              style="padding:.3rem .7rem;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:.8rem;cursor:pointer">
                        🗑️ Xoá
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align:center;color:#9ca3af;padding:2.5rem">
                  Không tìm thấy nhu yếu phẩm nào.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        {{-- PAGINATION --}}
        @if($supplies->hasPages())
          <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end">
            {{ $supplies->links() }}
          </div>
        @endif
      </div>

    </div>{{-- end padding --}}
  </main>
</div>
@endsection

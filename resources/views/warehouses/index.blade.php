@extends('layouts.app')
@section('title', 'Quản lý Kho hàng - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  {{-- SIDEBAR --}}
  @include('partials.admin-sidebar', ['activeMenu' => 'warehouses'])

  {{-- MAIN --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '📦 Quản lý Kho hàng'])
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
        <form method="GET" action="{{ route('admin.warehouses.index') }}" style="display:flex;gap:.6rem;flex:1;max-width:420px">
          <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="🔍 Tìm theo tên hoặc địa chỉ kho..."
            style="flex:1;padding:.55rem .9rem;border:1px solid #d1d5db;border-radius:8px;font-size:.88rem;outline:none"
          >
          <button type="submit" class="btn btn-outline btn-sm">Tìm</button>
          @if(request('search'))
            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-outline btn-sm" style="color:#6b7280">✕ Xoá</a>
          @endif
        </form>
        <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary" style="white-space:nowrap">
          + Thêm kho mới
        </a>
      </div>

      {{-- TABLE --}}
      <div class="table-wrap">
        <div class="table-header">
          <h3>🏭 Danh sách kho hàng
            @if(request('search'))
              <span style="font-size:.82rem;font-weight:400;color:#6b7280"> – kết quả cho "{{ request('search') }}"</span>
            @endif
          </h3>
          <span style="font-size:.82rem;color:#6b7280">{{ $warehouses->total() }} kho</span>
        </div>

        <table>
          <thead>
            <tr>
              <th style="width:60px">STT</th>
              <th>Tên kho</th>
              <th>Địa chỉ</th>
              <th style="width:120px">Vĩ độ (Lat)</th>
              <th style="width:120px">Kinh độ (Lng)</th>
              <th>Quản lý kho</th>
              <th style="width:130px;text-align:center">Hành động</th>
            </tr>
          </thead>
          <tbody>
            @forelse($warehouses as $warehouse)
              <tr>
                <td style="color:#9ca3af;font-size:.85rem">
                  {{ ($warehouses->currentPage() - 1) * $warehouses->perPage() + $loop->iteration }}
                </td>
                <td>
                  <strong style="color:#1e293b">{{ $warehouse->name }}</strong>
                </td>
                <td style="font-size:.88rem;color:#4b5563;max-width:220px">
                  {{ $warehouse->address }}
                </td>
                <td style="font-size:.85rem;color:#6b7280">
                  {{ $warehouse->lat ?? '—' }}
                </td>
                <td style="font-size:.85rem;color:#6b7280">
                  {{ $warehouse->lng ?? '—' }}
                </td>
                <td>
                  @if($warehouse->manager)
                    <div style="display:flex;align-items:center;gap:.4rem">
                      <span style="width:28px;height:28px;border-radius:50%;background:#6366f1;color:#fff;font-size:.75rem;display:inline-flex;align-items:center;justify-content:center;font-weight:600">
                        {{ strtoupper(substr($warehouse->manager->name, 0, 1)) }}
                      </span>
                      <span style="font-size:.88rem">{{ $warehouse->manager->name }}</span>
                    </div>
                  @else
                    <span style="color:#9ca3af;font-size:.85rem">Chưa phân công</span>
                  @endif
                </td>
                <td style="text-align:center">
                  <div style="display:flex;gap:.4rem;justify-content:center">
                    <a href="{{ route('admin.warehouses.edit', $warehouse) }}"
                       style="padding:.3rem .7rem;background:#dbeafe;color:#1d4ed8;border-radius:6px;font-size:.8rem;text-decoration:none">
                      ✏️ Sửa
                    </a>
                    <form action="{{ route('admin.warehouses.destroy', $warehouse) }}" method="POST"
                          onsubmit="return confirm('Xác nhận xoá kho « {{ $warehouse->name }} »?')">
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
                <td colspan="7" style="text-align:center;color:#9ca3af;padding:2.5rem">
                  Không tìm thấy kho nào.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        {{-- PAGINATION --}}
        @if($warehouses->hasPages())
          <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end">
            {{ $warehouses->links() }}
          </div>
        @endif
      </div>

    </div>{{-- end padding --}}
  </main>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Quản lý Tài khoản - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  {{-- SIDEBAR --}}
  @include('partials.admin-sidebar', ['activeMenu' => request('role') === 'driver' ? 'drivers' : 'users'])

  {{-- MAIN --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => request('role') === 'driver' ? '🚛 Danh sách Tài xế' : '👥 Quản lý Tài khoản'])
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

      {{-- TOOLBAR --}}
      <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.25rem;flex-wrap:wrap">

        {{-- Search + Role Filter --}}
        <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;gap:.6rem;flex:1;flex-wrap:wrap">
          <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="🔍 Tìm theo tên hoặc email..."
            style="flex:1;min-width:180px;padding:.55rem .9rem;border:1px solid #d1d5db;border-radius:8px;font-size:.88rem;outline:none"
          >
          <select name="role" style="padding:.55rem .9rem;border:1px solid #d1d5db;border-radius:8px;font-size:.88rem;background:#fff;color:#374151">
            <option value="">-- Tất cả vai trò --</option>
            <option value="warehouse_manager" @selected(request('role') === 'warehouse_manager')>🏭 Thủ kho</option>
            <option value="driver"            @selected(request('role') === 'driver')>🚛 Tài xế</option>
            <option value="resident"          @selected(request('role') === 'resident')>🏠 Người dân</option>
          </select>
          <button type="submit" class="btn btn-outline btn-sm">Lọc</button>
          @if(request('search') || request('role'))
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm" style="color:#6b7280">✕ Xoá lọc</a>
          @endif
        </form>

        <a href="{{ route('admin.users.create', request('role') === 'driver' ? ['from' => 'driver'] : []) }}" class="btn btn-primary" style="white-space:nowrap">
          + {{ request('role') === 'driver' ? 'Thêm Tài xế' : 'Thêm tài khoản' }}
        </a>
      </div>

      {{-- TABLE --}}
      <div class="table-wrap">
        <div class="table-header">
          <h3>{{ request('role') === 'driver' ? '🚛 Danh sách Tài xế' : '👤 Danh sách tài khoản' }}
            @if(request('search') || request('role'))
              <span style="font-size:.82rem;font-weight:400;color:#6b7280">– đang lọc</span>
            @endif
          </h3>
          <span style="font-size:.82rem;color:#6b7280">{{ $users->total() }} tài khoản</span>
        </div>

        <table>
          <thead>
            <tr>
              <th style="width:50px">STT</th>
              <th>Họ tên</th>
              <th>Email</th>
              <th>Số điện thoại</th>
              <th style="width:150px">Vai trò</th>
              <th style="width:110px">Ngày tạo</th>
              <th style="width:130px;text-align:center">Hành động</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $user)
              <tr>
                <td style="color:#9ca3af;font-size:.85rem">
                  {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                </td>
                <td>
                  <div style="display:flex;align-items:center;gap:.5rem">
                    <span style="width:32px;height:32px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:.82rem;color:#fff;flex-shrink:0;
                      background:{{ $user->role === 'warehouse_manager' ? '#6366f1' : ($user->role === 'driver' ? '#0891b2' : '#16a34a') }}">
                      {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                    <span style="font-weight:600;color:#1e293b">{{ $user->name }}</span>
                  </div>
                </td>
                <td style="font-size:.88rem;color:#4b5563">{{ $user->email }}</td>
                <td style="font-size:.88rem;color:#4b5563">{{ $user->phone ?? '—' }}</td>
                <td>
                  @php
                    $roleLabels = [
                      'warehouse_manager' => ['label'=>'🏭 Thủ kho',  'bg'=>'#ede9fe','color'=>'#5b21b6'],
                      'driver'            => ['label'=>'🚛 Tài xế',   'bg'=>'#cffafe','color'=>'#0e7490'],
                      'resident'          => ['label'=>'🏠 Người dân','bg'=>'#dcfce7','color'=>'#15803d'],
                    ];
                    $r = $roleLabels[$user->role] ?? ['label'=>$user->role,'bg'=>'#f3f4f6','color'=>'#374151'];
                  @endphp
                  <span style="padding:.25rem .7rem;border-radius:999px;font-size:.78rem;font-weight:600;background:{{ $r['bg'] }};color:{{ $r['color'] }}">
                    {{ $r['label'] }}
                  </span>
                </td>
                <td style="font-size:.83rem;color:#6b7280">{{ $user->created_at->format('d/m/Y') }}</td>
                <td style="text-align:center">
                  <div style="display:flex;gap:.4rem;justify-content:center">
                    <a href="{{ route('admin.users.edit', [$user, 'from' => request('role') === 'driver' ? 'driver' : null]) }}"
                       style="padding:.3rem .7rem;background:#dbeafe;color:#1d4ed8;border-radius:6px;font-size:.8rem;text-decoration:none">
                      ✏️ Sửa
                    </a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                          onsubmit="return confirm('Xác nhận xoá tài khoản « {{ $user->name }} »?')">
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
                  Không tìm thấy tài khoản nào.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        {{-- PAGINATION --}}
        @if($users->hasPages())
          <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end">
            {{ $users->links() }}
          </div>
        @endif
      </div>

    </div>{{-- end padding --}}
  </main>
</div>
@endsection

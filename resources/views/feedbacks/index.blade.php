@extends('layouts.app')
@section('title', 'Quản lý phản hồi - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true, openId: null }">

  @include('partials.admin-sidebar', ['activeMenu' => 'feedbacks'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '💬 Quản lý phản hồi'])

    <div style="padding:1.5rem">

      {{-- Flash --}}
      @if(session('success'))
        <div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:.85rem 1.25rem;border-radius:10px;margin-bottom:1rem;font-size:.875rem">
          ✅ {{ session('success') }}
        </div>
      @endif

      {{-- STAT CARDS --}}
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem">
        <div style="background:#fff;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05)">
          <div style="font-size:.75rem;color:#64748b;margin-bottom:.25rem">Chờ xử lý</div>
          <div style="font-size:2rem;font-weight:800;color:#f59e0b">{{ $pendingCount }}</div>
        </div>
        <div style="background:#fff;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05)">
          <div style="font-size:.75rem;color:#64748b;margin-bottom:.25rem">Đang xử lý</div>
          <div style="font-size:2rem;font-weight:800;color:#3b82f6">{{ $processingCount }}</div>
        </div>
        <div style="background:#fff;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,.05)">
          <div style="font-size:.75rem;color:#64748b;margin-bottom:.25rem">Đã giải quyết</div>
          <div style="font-size:2rem;font-weight:800;color:#10b981">{{ $resolvedCount }}</div>
        </div>
      </div>

      {{-- FILTER --}}
      <div class="table-wrap">
        <div class="table-header" style="flex-wrap:wrap;gap:.75rem">
          <h3 style="margin:0">📋 Danh sách phản hồi</h3>
          <form method="GET" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center">
            <input type="text" name="search" value="{{ request('search') }}"
              placeholder="Tìm kiếm tên, CCCD, nội dung..."
              style="border:1px solid #e2e8f0;border-radius:8px;padding:.45rem .75rem;font-size:.85rem;min-width:220px">
            <select name="status" style="border:1px solid #e2e8f0;border-radius:8px;padding:.45rem .75rem;font-size:.85rem">
              <option value="">Tất cả trạng thái</option>
              <option value="pending"    {{ request('status') === 'pending'    ? 'selected' : '' }}>Chờ xử lý</option>
              <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
              <option value="resolved"   {{ request('status') === 'resolved'   ? 'selected' : '' }}>Đã giải quyết</option>
            </select>
            <button type="submit" class="btn btn-teal btn-sm">🔍 Lọc</button>
            @if(request()->hasAny(['search','status']))
              <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-outline btn-sm">✕ Xoá lọc</a>
            @endif
          </form>
        </div>

        {{-- TABLE --}}
        <div style="overflow-x:auto">
          <table>
            <thead>
              <tr>
                <th style="width:40px">#</th>
                <th>Người gửi</th>
                <th>CCCD</th>
                <th>Nội dung</th>
                <th>Trạng thái</th>
                <th>Ngày gửi</th>
                <th style="width:100px">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              @forelse($feedbacks as $fb)
              <tr>
                <td style="color:#94a3b8;font-size:.8rem">{{ $fb->id }}</td>
                <td>
                  <div style="font-weight:600;font-size:.875rem">{{ $fb->name }}</div>
                  @if($fb->phone)
                    <div style="font-size:.75rem;color:#64748b">📞 {{ $fb->phone }}</div>
                  @endif
                </td>
                <td style="font-size:.8rem;font-family:monospace">{{ $fb->identity_card }}</td>
                <td>
                  <div style="font-size:.85rem;max-width:320px">
                    {{ Str::limit($fb->content, 80) }}
                  </div>
                  @if($fb->admin_note)
                    <div style="font-size:.75rem;color:#0d9488;margin-top:.2rem">
                      💬 Ghi chú: {{ Str::limit($fb->admin_note, 60) }}
                    </div>
                  @endif
                </td>
                <td>
                  @php
                    $sc = match($fb->status) {
                      'pending'    => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'Chờ xử lý'],
                      'processing' => ['bg'=>'#dbeafe','color'=>'#1e40af','label'=>'Đang xử lý'],
                      'resolved'   => ['bg'=>'#d1fae5','color'=>'#065f46','label'=>'Đã giải quyết'],
                      default      => ['bg'=>'#f1f5f9','color'=>'#475569','label'=>$fb->status],
                    };
                  @endphp
                  <span style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:.25rem .6rem;border-radius:6px;font-size:.75rem;font-weight:600">
                    {{ $sc['label'] }}
                  </span>
                </td>
                <td style="font-size:.8rem;white-space:nowrap;color:#64748b">
                  {{ $fb->created_at->format('H:i d/m/Y') }}
                </td>
                <td>
                  <button
                    @click="openId = (openId === {{ $fb->id }} ? null : {{ $fb->id }})"
                    class="btn btn-outline btn-sm"
                    style="font-size:.75rem;padding:.3rem .6rem">
                    ✏️ Xử lý
                  </button>
                </td>
              </tr>

              {{-- Expand row --}}
              <tr x-show="openId === {{ $fb->id }}" x-cloak>
                <td colspan="7" style="background:#f8fafc;padding:1rem 1.5rem">
                  <div style="max-width:600px">
                    <div style="font-size:.85rem;color:#374151;margin-bottom:1rem;line-height:1.7;background:#fff;padding:1rem;border-radius:8px;border:1px solid #e2e8f0">
                      <strong>Nội dung đầy đủ:</strong><br>{{ $fb->content }}
                    </div>
                    <form method="POST" action="{{ route('admin.feedbacks.update', $fb) }}" style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:flex-end">
                      @csrf @method('PATCH')
                      <div>
                        <label style="font-size:.75rem;font-weight:600;color:#475569;display:block;margin-bottom:.25rem">Trạng thái</label>
                        <select name="status" style="border:1px solid #e2e8f0;border-radius:8px;padding:.45rem .75rem;font-size:.85rem">
                          <option value="pending"    {{ $fb->status === 'pending'    ? 'selected' : '' }}>Chờ xử lý</option>
                          <option value="processing" {{ $fb->status === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                          <option value="resolved"   {{ $fb->status === 'resolved'   ? 'selected' : '' }}>Đã giải quyết</option>
                        </select>
                      </div>
                      <div style="flex:1;min-width:200px">
                        <label style="font-size:.75rem;font-weight:600;color:#475569;display:block;margin-bottom:.25rem">Ghi chú admin</label>
                        <input type="text" name="admin_note" value="{{ $fb->admin_note }}"
                          placeholder="Nhập ghi chú xử lý..."
                          style="width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:.45rem .75rem;font-size:.85rem">
                      </div>
                      <div>
                        <button type="submit" class="btn btn-teal btn-sm">💾 Lưu thay đổi</button>
                      </div>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" style="text-align:center;padding:3rem;color:#94a3b8">
                  <div style="font-size:2.5rem;margin-bottom:.5rem">📭</div>
                  <p>Chưa có phản hồi nào</p>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- PAGINATION --}}
        @if($feedbacks->hasPages())
          <div style="padding:1rem 1.5rem;border-top:1px solid #f1f5f9">
            @include('partials.pagination', ['paginator' => $feedbacks])
          </div>
        @endif
      </div>

    </div>
  </main>
</div>
@endsection

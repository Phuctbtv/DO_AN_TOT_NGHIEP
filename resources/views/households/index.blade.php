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

      {{-- HEADER + ACTION BUTTONS --}}
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap;gap:1rem">
        <div>
          <h2 style="font-size:1.25rem;font-weight:700;color:#0f172a;margin:0">Danh sách Hộ dân</h2>
          <p style="color:#64748b;font-size:.875rem;margin:0">Quản lý tất cả hộ dân đã đăng ký cứu trợ</p>
        </div>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap">
          {{-- Nút Xuất Excel --}}
          <button onclick="document.getElementById('modal-export-hd').style.display='flex'"
                  style="display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(135deg,#059669,#0d9488);color:#fff;padding:.6rem 1.25rem;border-radius:8px;font-weight:600;font-size:.875rem;border:none;cursor:pointer;box-shadow:0 2px 8px rgba(13,148,136,.3)">
            📥 Xuất Excel
          </button>
          <a href="{{ route('admin.households.pending') }}"
             style="display:inline-flex;align-items:center;gap:.5rem;background:#f59e0b;color:#fff;padding:.6rem 1.25rem;border-radius:8px;font-weight:600;font-size:.875rem;text-decoration:none">
            ⏳ Chờ duyệt
            @if($pendingCount > 0)
              <span style="background:#fff;color:#f59e0b;border-radius:999px;padding:.1rem .5rem;font-size:.75rem;font-weight:700">{{ $pendingCount }}</span>
            @endif
          </a>
        </div>
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

      {{-- MODAL XUẤT EXCEL HỘ DÂN --}}
      <div id="modal-export-hd"
           style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center"
           onclick="if(event.target===this)this.style.display='none'">
        <div style="background:#fff;border-radius:16px;padding:2rem;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative">
          {{-- Header modal --}}
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
            <div>
              <h3 style="font-size:1.1rem;font-weight:700;color:#0f172a;margin:0">📥 Xuất Báo Cáo Hộ Dân</h3>
              <p style="font-size:.8rem;color:#64748b;margin:.25rem 0 0">Tuỳ chỉnh bộ lọc trước khi xuất file Excel</p>
            </div>
            <button onclick="document.getElementById('modal-export-hd').style.display='none'"
                    style="background:#f1f5f9;border:none;width:32px;height:32px;border-radius:8px;font-size:1.1rem;cursor:pointer;color:#64748b">✕</button>
          </div>

          <form method="GET" action="{{ route('admin.reports.households.export') }}">
            {{-- Trạng thái --}}
            <div style="margin-bottom:1rem">
              <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem">Trạng thái</label>
              <select name="status" id="hd-status"
                      style="width:100%;padding:.6rem .9rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.88rem;color:#0f172a;background:#fff">
                <option value="">Tất cả trạng thái</option>
                <option value="active">✅ Đã duyệt</option>
                <option value="pending">⏳ Chờ duyệt</option>
                <option value="rejected">❌ Từ chối</option>
              </select>
            </div>

            {{-- Mức ưu tiên --}}
            <div style="margin-bottom:1rem">
              <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem">Mức ưu tiên</label>
              <select name="priority" id="hd-priority"
                      style="width:100%;padding:.6rem .9rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.88rem;color:#0f172a;background:#fff">
                <option value="">Tất cả mức ưu tiên</option>
                <option value="1">🔴 Cấp 1 – Khẩn cấp</option>
                <option value="2">🟡 Cấp 2 – Cần thiết</option>
                <option value="3">🟢 Cấp 3 – Bình thường</option>
              </select>
            </div>

            {{-- Khoảng thời gian --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.5rem">
              <div>
                <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem">Từ ngày</label>
                <input type="date" name="date_from" id="hd-date-from"
                       style="width:100%;padding:.6rem .9rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.88rem;color:#0f172a">
              </div>
              <div>
                <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem">Đến ngày</label>
                <input type="date" name="date_to" id="hd-date-to"
                       style="width:100%;padding:.6rem .9rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.88rem;color:#0f172a">
              </div>
            </div>

            {{-- Preview info --}}
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.82rem;color:#166534">
              📊 File Excel sẽ bao gồm: <strong>STT, Họ tên, CCCD, SĐT, Địa chỉ, Số thành viên, Mức ưu tiên, Trạng thái, Ngày đăng ký, Ngày duyệt</strong>
            </div>

            <div style="display:flex;gap:.75rem">
              <button type="button" onclick="document.getElementById('modal-export-hd').style.display='none'"
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

      @include('partials.pagination', ['paginator' => $households])

    </div>
  </main>
</div>
@endsection

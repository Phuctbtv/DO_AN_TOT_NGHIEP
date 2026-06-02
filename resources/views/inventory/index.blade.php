@extends('layouts.app')
@section('title', 'Tồn kho hiện tại - ĐẠI PHÚC')

@push('styles')
<style>
/* ── Summary cards ──────────────────────────────── */
.inv-summary {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}
@media (max-width: 700px) { .inv-summary { grid-template-columns: repeat(2,1fr); } }

.sum-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 1.1rem 1.25rem;
  box-shadow: 0 2px 8px rgba(0,0,0,.04);
  display: flex; align-items: center; gap: .85rem;
  transition: transform .15s;
}
.sum-card:hover { transform: translateY(-2px); }
.sum-icon {
  width: 46px; height: 46px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.35rem; flex-shrink: 0;
}
.sum-num  { font-size: 1.6rem; font-weight: 900; line-height: 1; }
.sum-lbl  { font-size: .75rem; color: #94a3b8; font-weight: 600; margin-top: .1rem; }

/* ── Filter toolbar ──────────────────────────────── */
.filter-bar {
  display: flex; gap: .6rem; flex-wrap: wrap; align-items: center;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: .85rem 1.1rem;
  margin-bottom: 1.25rem;
  box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.filter-input, .filter-select {
  padding: .5rem .85rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 8px;
  font-size: .87rem;
  font-family: inherit;
  outline: none;
  transition: border-color .15s;
}
.filter-input:focus, .filter-select:focus { border-color: #0d9488; }
.filter-input { flex: 1; min-width: 160px; }

/* ── Table ──────────────────────────────────────── */
.inv-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
.inv-table thead tr { background: #f8fafc; }
.inv-table th {
  padding: .7rem 1rem;
  text-align: left;
  font-size: .72rem; font-weight: 700;
  color: #64748b; text-transform: uppercase; letter-spacing: .5px;
  border-bottom: 2px solid #e2e8f0; white-space: nowrap;
}
.inv-table td { padding: .8rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.inv-table tbody tr { transition: background .12s; }
.inv-table tbody tr:hover { background: #f8fafc; }
.inv-table tbody tr:last-child td { border-bottom: none; }

/* ── Stock badges ───────────────────────────────── */
.badge-ok    { background:#d1fae5;color:#065f46;padding:.28rem .8rem;border-radius:999px;font-size:.75rem;font-weight:700;white-space:nowrap;display:inline-flex;align-items:center;gap:.3rem; }
.badge-low   { background:#fef3c7;color:#92400e;padding:.28rem .8rem;border-radius:999px;font-size:.75rem;font-weight:700;white-space:nowrap;display:inline-flex;align-items:center;gap:.3rem; }
.badge-empty { background:#fee2e2;color:#991b1b;padding:.28rem .8rem;border-radius:999px;font-size:.75rem;font-weight:700;white-space:nowrap;display:inline-flex;align-items:center;gap:.3rem; }

/* ── Stock number styles ────────────────────────── */
.stock-ok    { color:#059669; font-size:1.05rem; font-weight:800; }
.stock-low   { color:#d97706; font-size:1.05rem; font-weight:800; }
.stock-empty { color:#dc2626; font-size:1.05rem; font-weight:800; }

/* ── Mini bar ───────────────────────────────────── */
.mini-bar { height:5px; border-radius:3px; background:#f1f5f9; overflow:hidden; margin-top:.3rem; min-width:60px; }
.mini-fill{ height:100%; border-radius:3px; }

/* ── Warehouse pill ─────────────────────────────── */
.wh-pill {
  display:inline-flex; align-items:center; gap:.4rem;
  background:linear-gradient(135deg,#0d9488,#0891b2);
  color:#fff; padding:.3rem .85rem; border-radius:20px;
  font-size:.8rem; font-weight:700;
}

/* ── Pulse dot for low ──────────────────────────── */
.pulse-low {
  width:7px;height:7px;border-radius:50%;background:#f59e0b;
  animation:plow 1.5s infinite;
}
@keyframes plow { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.6)} }
</style>
@endpush

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  @include('partials.warehouse-sidebar', ['activeMenu' => 'inventory'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '🗄️ Tồn kho hiện tại'])

    <div style="padding:1.5rem">

      {{-- ── FLASH ─────────────────────────────────────────────── --}}
      @if(session('error'))
        <div style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1.25rem">
          ❌ {{ session('error') }}
        </div>
      @endif

      @if(!$warehouse)
        <div style="background:#fef3c7;color:#92400e;border:1px solid #fcd34d;border-radius:12px;padding:1.5rem;text-align:center">
          <div style="font-size:2rem;margin-bottom:.5rem">⚠️</div>
          <div style="font-weight:700">Bạn chưa được phân công quản lý kho nào.</div>
          <div style="font-size:.85rem;margin-top:.3rem">Vui lòng liên hệ Admin để được cấp quyền.</div>
        </div>
      @else

      {{-- ── CHỌN KHO (nếu quản lý nhiều kho) ──────────────────── --}}
      @if($warehouses->count() > 1)
        <form method="GET" action="{{ route('warehouse.inventory.index') }}"
              style="margin-bottom:1.25rem;display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
          <label style="font-size:.85rem;font-weight:700;color:#374151">🏭 Chọn kho:</label>
          <select name="warehouse_id" onchange="this.form.submit()"
                  class="filter-select" style="min-width:200px">
            @foreach($warehouses as $wh)
              <option value="{{ $wh->id }}" {{ $wh->id === $warehouse->id ? 'selected' : '' }}>
                {{ $wh->name }}
              </option>
            @endforeach
          </select>
          {{-- Giữ nguyên filter khi đổi kho --}}
          @foreach(request()->except('warehouse_id') as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
          @endforeach
        </form>
      @endif

      {{-- ── BANNER KHO ĐANG XEM ─────────────────────────────────── --}}
      <div style="
        background:linear-gradient(135deg,#f0fdfa,#e0f2fe);
        border:1.5px solid #5eead4;border-radius:12px;
        padding:.85rem 1.25rem;margin-bottom:1.5rem;
        display:flex;align-items:center;gap:1rem;
      ">
        <div style="font-size:1.6rem">🏭</div>
        <div style="flex:1">
          <div style="font-size:.72rem;color:#0d9488;font-weight:700;text-transform:uppercase;letter-spacing:.5px">Kho đang xem</div>
          <div style="font-weight:800;color:#0f172a;font-size:1rem">{{ $warehouse->name }}</div>
          <div style="font-size:.78rem;color:#64748b">📍 {{ $warehouse->address }}</div>
        </div>
        <div class="wh-pill">🗄️ {{ $summary['total'] }} loại hàng</div>
      </div>

      {{-- ── SUMMARY CARDS ────────────────────────────────────────── --}}
      <div class="inv-summary">
        <div class="sum-card">
          <div class="sum-icon" style="background:#ede9fe">📦</div>
          <div>
            <div class="sum-num" style="color:#7c3aed">{{ $summary['total'] }}</div>
            <div class="sum-lbl">Tổng loại hàng</div>
          </div>
        </div>
        <div class="sum-card">
          <div class="sum-icon" style="background:#d1fae5">✅</div>
          <div>
            <div class="sum-num" style="color:#059669">{{ $summary['ok'] }}</div>
            <div class="sum-lbl">Còn hàng</div>
          </div>
        </div>
        <div class="sum-card">
          <div class="sum-icon" style="background:#fef3c7">⚠️</div>
          <div>
            <div class="sum-num" style="color:#d97706">{{ $summary['low'] }}</div>
            <div class="sum-lbl">Sắp hết hàng</div>
          </div>
        </div>
        <div class="sum-card">
          <div class="sum-icon" style="background:#fee2e2">🚨</div>
          <div>
            <div class="sum-num" style="color:#dc2626">{{ $summary['empty'] }}</div>
            <div class="sum-lbl">Hết hàng</div>
          </div>
        </div>
      </div>

      {{-- ── FILTER BAR ───────────────────────────────────────────── --}}
      <form method="GET" action="{{ route('warehouse.inventory.index') }}" class="filter-bar">

        {{-- Giữ kho khi filter --}}
        @if($warehouses->count() > 1)
          <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}">
        @endif

        {{-- Tìm kiếm --}}
        <input type="text"
               name="search"
               class="filter-input"
               placeholder="🔍 Tìm theo tên hàng..."
               value="{{ request('search') }}">

        {{-- Lọc danh mục --}}
        <select name="category_id" class="filter-select">
          <option value="">📂 Tất cả danh mục</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>

        {{-- Lọc trạng thái --}}
        <select name="status" class="filter-select">
          <option value="">🔖 Tất cả trạng thái</option>
          <option value="ok"    {{ request('status') === 'ok'    ? 'selected' : '' }}>✅ Còn hàng</option>
          <option value="low"   {{ request('status') === 'low'   ? 'selected' : '' }}>⚠️ Sắp hết</option>
          <option value="empty" {{ request('status') === 'empty' ? 'selected' : '' }}>🚨 Hết hàng</option>
        </select>

        <button type="submit"
                style="padding:.5rem 1.1rem;background:#0d9488;color:#fff;border:none;border-radius:8px;font-size:.87rem;font-weight:700;cursor:pointer;font-family:inherit">
          Lọc
        </button>

        @if(request()->anyFilled(['search','category_id','status']))
          <a href="{{ route('warehouse.inventory.index', $warehouses->count() > 1 ? ['warehouse_id' => $warehouse->id] : []) }}"
             style="padding:.5rem .95rem;border:1.5px solid #e2e8f0;border-radius:8px;color:#64748b;text-decoration:none;font-size:.87rem;display:flex;align-items:center">
            ✕ Xóa lọc
          </a>
        @endif
      </form>

      {{-- ── TABLE CARD ──────────────────────────────────────────── --}}
      <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;box-shadow:0 2px 12px rgba(0,0,0,.05);overflow:hidden">
        <div style="padding:.9rem 1.25rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
          <h3 style="font-size:.95rem;font-weight:700;color:#0f172a;margin:0">📋 Danh sách tồn kho</h3>
          <span style="font-size:.82rem;color:#64748b">{{ $inventoryRows->count() }} mặt hàng</span>
        </div>

        <div style="overflow-x:auto">
          <table class="inv-table">
            <thead>
              <tr>
                <th>STT</th>
                <th>Nhu yếu phẩm</th>
                <th>Danh mục</th>
                <th>Đơn vị</th>
                <th style="text-align:right">Tổng nhập</th>
                <th style="text-align:right">Tổng xuất</th>
                <th style="text-align:right">Tồn kho</th>
                <th style="text-align:right">Mức cảnh báo</th>
                <th style="text-align:center">Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              @forelse($inventoryRows as $i => $row)
                @php
                  $stockClass = match($row->status) {
                    'ok'    => 'stock-ok',
                    'low'   => 'stock-low',
                    'empty' => 'stock-empty',
                    default => ''
                  };
                  // Progress bar: tỉ lệ tồn / (tổng nhập) để hiển thị trực quan
                  $barPct   = $row->total_in > 0 ? min(100, round($row->stock / $row->total_in * 100)) : 0;
                  $barColor = match($row->status) {
                    'ok'    => '#10b981',
                    'low'   => '#f59e0b',
                    'empty' => '#ef4444',
                    default => '#94a3b8',
                  };
                @endphp
                <tr>
                  {{-- STT --}}
                  <td style="color:#9ca3af;font-size:.8rem;width:42px">{{ $i + 1 }}</td>

                  {{-- Tên hàng --}}
                  <td>
                    <div style="font-weight:700;color:#1e293b">{{ $row->supply->name }}</div>
                  </td>

                  {{-- Danh mục --}}
                  <td>
                    @if($row->supply->category)
                      <span style="background:#dbeafe;color:#1d4ed8;padding:.2rem .65rem;border-radius:20px;font-size:.75rem;font-weight:600;white-space:nowrap">
                        {{ $row->supply->category->name }}
                      </span>
                    @else
                      <span style="color:#d1d5db;font-size:.82rem">—</span>
                    @endif
                  </td>

                  {{-- Đơn vị --}}
                  <td style="color:#64748b;font-size:.85rem">{{ $row->supply->unit ?? '—' }}</td>

                  {{-- Tổng nhập --}}
                  <td style="text-align:right">
                    <span style="font-weight:700;color:#0891b2">+{{ number_format($row->total_in) }}</span>
                  </td>

                  {{-- Tổng xuất --}}
                  <td style="text-align:right">
                    <span style="font-weight:700;color:#7c3aed">-{{ number_format($row->total_out) }}</span>
                  </td>

                  {{-- Tồn kho --}}
                  <td style="text-align:right">
                    <span class="{{ $stockClass }}">{{ number_format($row->stock) }}</span>
                    {{-- mini progress bar --}}
                    <div class="mini-bar">
                      <div class="mini-fill" style="width:{{ $barPct }}%;background:{{ $barColor }}"></div>
                    </div>
                  </td>

                  {{-- Mức cảnh báo --}}
                  <td style="text-align:right">
                    @if($row->min_alert > 0)
                      <span style="font-size:.85rem;color:#64748b">{{ number_format($row->min_alert) }}</span>
                    @else
                      <span style="color:#d1d5db;font-size:.8rem">—</span>
                    @endif
                  </td>

                  {{-- Trạng thái --}}
                  <td style="text-align:center">
                    @if($row->status === 'ok')
                      <span class="badge-ok">✅ Còn hàng</span>
                    @elseif($row->status === 'low')
                      <span class="badge-low"><span class="pulse-low"></span>Sắp hết</span>
                    @else
                      <span class="badge-empty">🚨 Hết hàng</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" style="padding:4rem;text-align:center;color:#94a3b8">
                    <div style="font-size:2.5rem;margin-bottom:.75rem">📭</div>
                    <div style="font-weight:600;color:#64748b;margin-bottom:.3rem">
                      @if(request()->anyFilled(['search','category_id','status']))
                        Không tìm thấy kết quả phù hợp
                      @else
                        Kho chưa có hàng nào được nhập
                      @endif
                    </div>
                    @if(request()->anyFilled(['search','category_id','status']))
                      <a href="{{ route('warehouse.inventory.index', $warehouses->count() > 1 ? ['warehouse_id' => $warehouse->id] : []) }}"
                         style="color:#0d9488;text-decoration:none;font-size:.88rem">
                        ✕ Xóa bộ lọc
                      </a>
                    @else
                      <a href="{{ route('warehouse.stock_ins.create') }}"
                         style="color:#0d9488;text-decoration:none;font-size:.88rem">
                        📥 Nhập hàng ngay
                      </a>
                    @endif
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- ── FOOTER TỔNG KẾT ─────────────────────────────────── --}}
        @if($inventoryRows->isNotEmpty())
          <div style="padding:.9rem 1.25rem;background:#f8fafc;border-top:2px solid #e2e8f0;display:flex;gap:2rem;flex-wrap:wrap;align-items:center">
            @php
              $totalInSum  = $inventoryRows->sum('total_in');
              $totalOutSum = $inventoryRows->sum('total_out');
              $stockSum    = $inventoryRows->sum('stock');
            @endphp
            <div style="font-size:.85rem;color:#64748b">
              <strong style="color:#0891b2">Tổng nhập:</strong> {{ number_format($totalInSum) }} đơn vị
            </div>
            <div style="font-size:.85rem;color:#64748b">
              <strong style="color:#7c3aed">Tổng xuất:</strong> {{ number_format($totalOutSum) }} đơn vị
            </div>
            <div style="font-size:.85rem;color:#64748b">
              <strong style="color:#059669">Tổng tồn:</strong>
              <span style="font-size:1rem;font-weight:800;color:#059669">{{ number_format($stockSum) }}</span> đơn vị
            </div>
          </div>
        @endif
      </div>

      @endif {{-- end if $warehouse --}}

    </div>
  </main>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Tổng quan Kho - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: false }">

  @include('partials.warehouse-sidebar', ['activeMenu' => 'overview'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '📊 Tổng quan Kho hàng'])

    <div style="padding:1.25rem 1.5rem">

      {{-- Warehouse Selector --}}
      @if($warehouses->count() > 1)
      <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap">
        <span style="font-size:.875rem;font-weight:600;color:#475569">🏭 Kho:</span>
        <form method="GET" action="{{ route('warehouse.overview') }}" style="display:flex;gap:.5rem;align-items:center">
          <select name="warehouse_id" class="form-control" style="width:auto;padding:.4rem .9rem;font-size:.875rem" onchange="this.form.submit()">
            @foreach($warehouses as $wh)
              <option value="{{ $wh->id }}" {{ $warehouse?->id === $wh->id ? 'selected' : '' }}>
                {{ $wh->name }}
              </option>
            @endforeach
          </select>
        </form>
        @if($warehouse)
          <span style="font-size:.8rem;color:#64748b">📍 {{ $warehouse->address }}</span>
        @endif
      </div>
      @endif

      @if(!$warehouse)
        <div class="alert alert-warning">⚠️ Bạn chưa được phân công quản lý kho nào. Vui lòng liên hệ Admin.</div>
      @else

      {{-- ======= STAT CARDS ======= --}}
      <div class="overview-stats">

        {{-- Card 1: Số loại hàng --}}
        <div class="ov-card ov-card--teal animate-in">
          <div class="ov-card__icon">📦</div>
          <div class="ov-card__body">
            <div class="ov-card__value">{{ number_format($stats['total_types']) }}</div>
            <div class="ov-card__label">Loại hàng hóa</div>
            <div class="ov-card__sub">Đang có trong kho</div>
          </div>
          <div class="ov-card__bg-icon">📦</div>
        </div>

        {{-- Card 2: Tổng tồn kho --}}
        <div class="ov-card ov-card--blue animate-in delay-1">
          <div class="ov-card__icon">🗄️</div>
          <div class="ov-card__body">
            <div class="ov-card__value">{{ number_format($stats['total_stock']) }}</div>
            <div class="ov-card__label">Tổng tồn kho</div>
            <div class="ov-card__sub">Số lượng hiện có</div>
          </div>
          <div class="ov-card__bg-icon">🗄️</div>
        </div>

        {{-- Card 3: Nhập tháng --}}
        <div class="ov-card ov-card--green animate-in delay-2">
          <div class="ov-card__icon">📥</div>
          <div class="ov-card__body">
            <div class="ov-card__value">{{ number_format($stats['total_in_month']) }}</div>
            <div class="ov-card__label">Nhập trong tháng</div>
            <div class="ov-card__sub">{{ now()->format('m/Y') }}</div>
          </div>
          <div class="ov-card__bg-icon">📥</div>
        </div>

        {{-- Card 4: Xuất tháng --}}
        <div class="ov-card ov-card--orange animate-in delay-3">
          <div class="ov-card__icon">📤</div>
          <div class="ov-card__body">
            <div class="ov-card__value">{{ number_format($stats['total_out_month']) }}</div>
            <div class="ov-card__label">Xuất trong tháng</div>
            <div class="ov-card__sub">{{ now()->format('m/Y') }}</div>
          </div>
          <div class="ov-card__bg-icon">📤</div>
        </div>

        {{-- Card 5: Sắp hết --}}
        <div class="ov-card ov-card--yellow animate-in delay-4">
          <div class="ov-card__icon">⚠️</div>
          <div class="ov-card__body">
            <div class="ov-card__value">{{ $stats['low_count'] }}</div>
            <div class="ov-card__label">Sắp hết hàng</div>
            <div class="ov-card__sub">Tồn &lt; mức cảnh báo</div>
          </div>
          <div class="ov-card__bg-icon">⚠️</div>
        </div>

        {{-- Card 6: Hết hàng --}}
        <div class="ov-card ov-card--red animate-in delay-4">
          <div class="ov-card__icon">🔴</div>
          <div class="ov-card__body">
            <div class="ov-card__value">{{ $stats['empty_count'] }}</div>
            <div class="ov-card__label">Đã hết hàng</div>
            <div class="ov-card__sub">Tồn kho = 0</div>
          </div>
          <div class="ov-card__bg-icon">🔴</div>
        </div>

      </div>

      {{-- ======= INVENTORY TABLE + RECENT ======= --}}
      <div class="overview-grid" style="margin-top:1.5rem">

        {{-- Tồn kho nhanh --}}
        <div class="table-wrap">
          <div class="table-header">
            <h3>📋 Tồn kho hiện tại</h3>
            <div style="display:flex;gap:.5rem">
              @if($stats['low_count'] + $stats['empty_count'] > 0)
                <a href="{{ route('warehouse.alerts', ['warehouse_id' => $warehouse->id]) }}"
                   class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border:none">
                  🔔 {{ $stats['low_count'] + $stats['empty_count'] }} cảnh báo
                </a>
              @endif
              <a href="{{ route('warehouse.inventory.index', ['warehouse_id' => $warehouse->id]) }}"
                 class="btn btn-outline btn-sm">Xem đầy đủ →</a>
            </div>
          </div>
          <div style="overflow-x:auto">
            <table>
              <thead>
                <tr>
                  <th>Mặt hàng</th>
                  <th>Danh mục</th>
                  <th>Đơn vị</th>
                  <th>Tồn kho</th>
                  <th>Trạng thái</th>
                </tr>
              </thead>
              <tbody>
                @forelse($inventory->take(8) as $row)
                <tr>
                  <td><strong>{{ $row->supply->name }}</strong></td>
                  <td style="color:#64748b;font-size:.8rem">{{ $row->supply->category?->name ?? '—' }}</td>
                  <td>{{ $row->supply->unit }}</td>
                  <td style="font-weight:700">{{ number_format($row->stock) }}</td>
                  <td>
                    @if($row->status === 'empty')
                      <span class="status-pill danger">🔴 Hết hàng</span>
                    @elseif($row->status === 'low')
                      <span class="status-pill warning">🟠 Sắp hết</span>
                    @else
                      <span class="status-pill success">✅ Đủ hàng</span>
                    @endif
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" style="text-align:center;color:#94a3b8;padding:2rem">
                    Chưa có dữ liệu tồn kho
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        {{-- Recent nhập / xuất --}}
        <div style="display:flex;flex-direction:column;gap:1rem">

          {{-- Recent Imports --}}
          <div class="wh-card">
            <div class="wh-card-header">📥 Nhập kho gần đây
              <a href="{{ route('warehouse.stock_ins.index') }}" style="margin-left:auto;font-size:.75rem;font-weight:500;color:#0d9488">Xem tất cả</a>
            </div>
            <div class="wh-card-body" style="padding:0">
              @forelse($recentIns as $item)
              <div class="ticker-item">
                <span class="ticker-time">{{ $item->received_date?->format('d/m') }}</span>
                <span class="ticker-text">
                  <strong>+{{ number_format($item->quantity) }}</strong>
                  {{ $item->supply?->unit }}
                  {{ $item->supply?->name }}
                  @if($item->donor_info)
                    <span style="color:#94a3b8;font-size:.8rem">từ {{ $item->donor_info }}</span>
                  @endif
                </span>
              </div>
              @empty
              <div style="padding:1rem 1.25rem;color:#94a3b8;font-size:.85rem">Chưa có phiếu nhập nào.</div>
              @endforelse
            </div>
          </div>

          {{-- Recent Exports --}}
          <div class="wh-card">
            <div class="wh-card-header">📤 Xuất kho gần đây
              <a href="{{ route('warehouse.stock_outs.index') }}" style="margin-left:auto;font-size:.75rem;font-weight:500;color:#f97316">Xem tất cả</a>
            </div>
            <div class="wh-card-body" style="padding:0">
              @forelse($recentOuts as $item)
              <div class="ticker-item">
                <span class="ticker-time">{{ $item->exported_date?->format('d/m') }}</span>
                <span class="ticker-text">
                  <strong>-{{ number_format($item->quantity) }}</strong>
                  {{ $item->supply?->unit }}
                  {{ $item->supply?->name }}
                  @if($item->trip)
                    <span style="color:#94a3b8;font-size:.8rem">→ Chuyến #{{ $item->trip->id }}</span>
                  @endif
                </span>
              </div>
              @empty
              <div style="padding:1rem 1.25rem;color:#94a3b8;font-size:.85rem">Chưa có phiếu xuất nào.</div>
              @endforelse
            </div>
          </div>

        </div>
      </div>

      {{-- Quick Actions --}}
      <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid #f1f5f9">
        <a href="{{ route('warehouse.stock_ins.create') }}" class="btn btn-teal">📥 Nhập kho mới</a>
        <a href="{{ route('warehouse.stock_outs.index') }}" class="btn btn-orange">📤 Xuất kho</a>
        <a href="{{ route('warehouse.statistics', ['warehouse_id' => $warehouse->id]) }}" class="btn btn-outline">📈 Xem thống kê</a>
        <button onclick="document.getElementById('modal-stock-report').style.display='flex'"
                class="btn btn-outline"
                style="border-color:#0d9488;color:#0d9488;background:#fff;cursor:pointer;font-family:inherit">
          📊 Xuất báo cáo Excel
        </button>
        @if($stats['low_count'] + $stats['empty_count'] > 0)
          <a href="{{ route('warehouse.alerts', ['warehouse_id' => $warehouse->id]) }}"
             class="btn" style="background:#fef3c7;color:#d97706;border:2px solid #fde68a">
            🔔 {{ $stats['low_count'] + $stats['empty_count'] }} mặt hàng cần bổ sung
          </a>
        @endif
      </div>

      @endif {{-- end if warehouse --}}

    </div>
  </main>
</div>
@endsection

@push('styles')
<style>
/* === OVERVIEW STAT CARDS === */
.overview-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.ov-card {
  position: relative;
  border-radius: 14px;
  padding: 1.35rem 1.25rem;
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  overflow: hidden;
  transition: all 0.3s cubic-bezier(.4,0,.2,1);
  box-shadow: 0 4px 24px rgba(0,0,0,0.08);
  cursor: default;
}
.ov-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 36px rgba(0,0,0,0.15);
}
.ov-card--teal  { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); color:#fff; }
.ov-card--blue  { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color:#fff; }
.ov-card--green { background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color:#fff; }
.ov-card--orange{ background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color:#fff; }
.ov-card--yellow{ background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color:#fff; }
.ov-card--red   { background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color:#fff; }

.ov-card__icon {
  font-size: 1.75rem;
  flex-shrink: 0;
  width: 50px; height: 50px;
  background: rgba(255,255,255,0.2);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
}
.ov-card__body  { flex: 1; position: relative; z-index: 2; }
.ov-card__value { font-size: 2rem; font-weight: 800; line-height: 1; margin-bottom: .2rem; }
.ov-card__label { font-size: .875rem; font-weight: 600; opacity: .95; }
.ov-card__sub   { font-size: .75rem; opacity: .75; margin-top: .15rem; }

.ov-card__bg-icon {
  position: absolute; right: -8px; bottom: -8px;
  font-size: 5rem; opacity: .12;
  pointer-events: none; line-height: 1;
  z-index: 1;
}

/* === OVERVIEW GRID === */
.overview-grid {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 1.25rem;
  align-items: start;
}

@media (max-width: 1024px) {
  .overview-grid { grid-template-columns: 1fr; }
  .overview-stats { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 640px) {
  .overview-stats { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

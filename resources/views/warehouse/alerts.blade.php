@extends('layouts.app')
@section('title', 'Cảnh báo tồn kho - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: false }">

  @include('partials.warehouse-sidebar', ['activeMenu' => 'alerts'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '🔔 Cảnh báo tồn kho'])

    <div style="padding:1.25rem 1.5rem">

      {{-- Warehouse Selector --}}
      @if($warehouses->count() > 1)
      <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap">
        <span style="font-size:.875rem;font-weight:600;color:#475569">🏭 Kho:</span>
        <form method="GET" action="{{ route('warehouse.alerts') }}" style="display:flex;gap:.5rem;align-items:center">
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

      {{-- Flash messages --}}
      @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem">✅ {{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert" style="background:#fee2e2;color:#991b1b;margin-bottom:1rem">❌ {{ session('error') }}</div>
      @endif
      @if(session('info'))
        <div class="alert alert-info" style="margin-bottom:1rem">ℹ️ {{ session('info') }}</div>
      @endif

      @if(!$warehouse)
        <div class="alert alert-warning">⚠️ Bạn chưa được phân công quản lý kho nào.</div>
      @else

      {{-- ======= SUMMARY BANNER ======= --}}
      @php
        $emptyCount = $alertRows->where('status', 'empty')->count();
        $lowCount   = $alertRows->where('status', 'low')->count();
      @endphp

      @if($alertRows->isEmpty())
        {{-- OK Banner --}}
        <div class="alert-banner alert-banner--ok animate-in">
          <div style="font-size:3rem">✅</div>
          <div>
            <div style="font-size:1.15rem;font-weight:700;color:#166534">Tất cả mặt hàng đều đủ hàng!</div>
            <div style="font-size:.875rem;color:#15803d;margin-top:.25rem">
              Kho {{ $warehouse->name }} hiện không có mặt hàng nào cần bổ sung.
            </div>
          </div>
        </div>
      @else
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem">
          <div class="alert-card alert-card--red animate-in">
            <span style="font-size:2rem">🔴</span>
            <div>
              <div class="alert-card__value">{{ $emptyCount }}</div>
              <div class="alert-card__label">Mặt hàng đã HẾT</div>
            </div>
          </div>
          <div class="alert-card alert-card--orange animate-in delay-1">
            <span style="font-size:2rem">🟠</span>
            <div>
              <div class="alert-card__value">{{ $lowCount }}</div>
              <div class="alert-card__label">Mặt hàng SẮP HẾT</div>
            </div>
          </div>
          <div class="alert-card alert-card--blue animate-in delay-2">
            <span style="font-size:2rem">📋</span>
            <div>
              <div class="alert-card__value">{{ $alertRows->count() }}</div>
              <div class="alert-card__label">Tổng cần bổ sung</div>
            </div>
          </div>
        </div>
      @endif

      {{-- ======= DANH SÁCH CẢNH BÁO ======= --}}
      @if(!$alertRows->isEmpty())
      <div class="table-wrap">
        <div class="table-header">
          <h3>📋 Danh sách mặt hàng cần bổ sung</h3>
          <div style="display:flex;gap:.5rem">
            <a href="{{ route('warehouse.stock_ins.create') }}" class="btn btn-teal btn-sm">
              📥 Nhập kho ngay
            </a>
          </div>
        </div>
        <div style="overflow-x:auto">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Mặt hàng</th>
                <th>Danh mục</th>
                <th>Đơn vị</th>
                <th>Tồn kho hiện tại</th>
                <th>Mức cảnh báo</th>
                <th>Còn thiếu</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              @foreach($alertRows as $i => $row)
              <tr class="alert-row alert-row--{{ $row->status }}">
                <td style="color:#94a3b8;font-size:.8rem">{{ $i + 1 }}</td>
                <td>
                  <div style="font-weight:700;font-size:.925rem">{{ $row->supply->name }}</div>
                </td>
                <td>
                  <span style="background:#f1f5f9;padding:.2rem .6rem;border-radius:6px;font-size:.78rem;color:#475569">
                    {{ $row->supply->category?->name ?? '—' }}
                  </span>
                </td>
                <td style="font-size:.875rem">{{ $row->supply->unit }}</td>
                <td>
                  <span style="font-weight:700;font-size:1.05rem;color:{{ $row->stock === 0 ? '#dc2626' : '#d97706' }}">
                    {{ number_format($row->stock) }}
                  </span>
                </td>
                <td style="color:#64748b">
                  {{ $row->min_alert > 0 ? number_format($row->min_alert) : '—' }}
                </td>
                <td>
                  @if($row->min_alert > 0)
                    @php $missing = max(0, $row->min_alert - $row->stock); @endphp
                    @if($missing > 0)
                      <span style="color:#dc2626;font-weight:700">-{{ number_format($missing) }}</span>
                    @else
                      <span style="color:#94a3b8">—</span>
                    @endif
                  @else
                    <span style="color:#94a3b8">—</span>
                  @endif
                </td>
                <td>
                  @if($row->status === 'empty')
                    <span class="alert-badge alert-badge--red">🔴 Hết hàng</span>
                  @else
                    <span class="alert-badge alert-badge--orange">🟠 Sắp hết</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('warehouse.stock_ins.create') }}"
                     class="btn btn-sm btn-teal"
                     title="Nhập thêm {{ $row->supply->name }}">
                    📥 Nhập thêm
                  </a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- ======= GỬI YÊU CẦU BỔ SUNG ======= --}}
      <div class="request-panel animate-in" style="margin-top:1.5rem">
        <div class="request-panel__icon">📨</div>
        <div class="request-panel__body">
          <div class="request-panel__title">Gửi yêu cầu bổ sung tới Admin</div>
          <div class="request-panel__desc">
            Có <strong>{{ $alertRows->count() }}</strong> mặt hàng cần bổ sung tại kho
            <strong>{{ $warehouse->name }}</strong>.
            Nhấn nút bên dưới để thông báo Admin qua Telegram.
          </div>
        </div>
        <div class="request-panel__action">
          <form method="POST" action="{{ route('warehouse.alerts.request') }}"
                onsubmit="return confirmRequest()">
            @csrf
            <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}">
            <button type="submit" class="btn btn-orange">
              📲 Gửi thông báo Admin
            </button>
          </form>
        </div>
      </div>

      @endif {{-- end if alertRows not empty --}}

      {{-- Quick nav --}}
      <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid #f1f5f9">
        <a href="{{ route('warehouse.overview', ['warehouse_id' => $warehouse->id]) }}" class="btn btn-outline">
          ← Quay về Tổng quan
        </a>
        <a href="{{ route('warehouse.inventory.index', ['warehouse_id' => $warehouse->id]) }}" class="btn btn-outline">
          🗄️ Xem toàn bộ tồn kho
        </a>
        <a href="{{ route('warehouse.stock_ins.create') }}" class="btn btn-teal">
          📥 Nhập kho mới
        </a>
      </div>

      @endif {{-- end if warehouse --}}

    </div>
  </main>
</div>
@endsection

@push('styles')
<style>
/* === ALERT BANNER === */
.alert-banner {
  display: flex; align-items: center; gap: 1.25rem;
  background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 14px;
  padding: 1.5rem 1.75rem; margin-bottom: 1.5rem;
}
.alert-banner--ok { background: #f0fdf4; border-color: #bbf7d0; }

/* === ALERT CARDS === */
.alert-card {
  display: flex; align-items: center; gap: 1rem;
  border-radius: 12px; padding: 1.25rem 1.5rem;
  box-shadow: 0 4px 24px rgba(0,0,0,.07);
  transition: all .3s;
}
.alert-card:hover { transform: translateY(-3px); box-shadow: 0 10px 32px rgba(0,0,0,.12); }
.alert-card--red    { background: linear-gradient(135deg, #fee2e2, #fecaca); }
.alert-card--orange { background: linear-gradient(135deg, #fef3c7, #fde68a); }
.alert-card--blue   { background: linear-gradient(135deg, #dbeafe, #bfdbfe); }

.alert-card__value { font-size: 2rem; font-weight: 800; line-height: 1; }
.alert-card__label { font-size: .8rem; font-weight: 600; color: #475569; margin-top: .15rem; }

/* === ALERT TABLE ROWS === */
.alert-row--empty {
  background: #fff5f5;
}
.alert-row--low {
  background: #fffbeb;
}

/* === ALERT BADGES === */
.alert-badge {
  display: inline-flex; align-items: center; gap: .3rem;
  padding: .25rem .7rem; border-radius: 999px; font-size: .78rem; font-weight: 700;
}
.alert-badge--red    { background: #fee2e2; color: #dc2626; }
.alert-badge--orange { background: #fef3c7; color: #d97706; }

/* === REQUEST PANEL === */
.request-panel {
  display: flex; align-items: center; gap: 1.25rem;
  background: #fff7ed; border: 2px solid #fed7aa;
  border-radius: 14px; padding: 1.25rem 1.5rem;
  flex-wrap: wrap;
}
.request-panel__icon { font-size: 2.5rem; flex-shrink: 0; }
.request-panel__body { flex: 1; min-width: 200px; }
.request-panel__title  { font-size: 1rem; font-weight: 700; color: #9a3412; margin-bottom: .25rem; }
.request-panel__desc   { font-size: .875rem; color: #92400e; }
.request-panel__action { flex-shrink: 0; }
</style>
@endpush

@push('scripts')
<script>
function confirmRequest() {
  return confirm('Xác nhận gửi thông báo bổ sung kho tới Admin qua Telegram?');
}
</script>
@endpush

@extends('layouts.app')
@section('title', 'Thống kê - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: false }">

  @include('partials.warehouse-sidebar', ['activeMenu' => 'statistics'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '📈 Thống kê nhập xuất'])

    <div style="padding:1.25rem 1.5rem">

      {{-- ======= HEADER FILTERS ======= --}}
      <form method="GET" action="{{ route('warehouse.statistics') }}"
            style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem;
                   background:#fff;padding:1rem 1.25rem;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.06)">

        @if($warehouses->count() > 1)
        <div style="display:flex;align-items:center;gap:.5rem">
          <span style="font-size:.875rem;font-weight:600;color:#475569">🏭 Kho:</span>
          <select name="warehouse_id" class="form-control" style="width:auto;padding:.4rem .9rem;font-size:.875rem">
            @foreach($warehouses as $wh)
              <option value="{{ $wh->id }}" {{ $warehouse?->id === $wh->id ? 'selected' : '' }}>
                {{ $wh->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="stat-divider"></div>
        @endif

        <div style="display:flex;align-items:center;gap:.5rem">
          <span style="font-size:.875rem;font-weight:600;color:#475569">📅 Kỳ:</span>
          <div style="display:flex;gap:.4rem">
            <button type="submit" name="period" value="week"
                    class="period-btn {{ $period === 'week' ? 'period-btn--active' : '' }}">
              7 ngày gần nhất
            </button>
            <button type="submit" name="period" value="month"
                    class="period-btn {{ $period === 'month' ? 'period-btn--active' : '' }}">
              30 ngày gần nhất
            </button>
          </div>
        </div>

        @if($warehouse)
          <span style="font-size:.8rem;color:#94a3b8;margin-left:auto">
            📍 {{ $warehouse->name }}
          </span>
        @endif

        {{-- Nút xuất báo cáo Excel --}}
        <button type="button"
                onclick="document.getElementById('modal-stock-report').style.display='flex'"
                style="display:inline-flex;align-items:center;gap:.45rem;padding:.45rem 1.1rem;
                       background:linear-gradient(135deg,#0d9488,#0891b2);color:#fff;border:none;
                       border-radius:8px;font-size:.85rem;font-weight:700;cursor:pointer;
                       box-shadow:0 2px 8px rgba(13,148,136,.25);flex-shrink:0">
          📊 Xuất báo cáo Excel
        </button>
      </form>

      @if(!$warehouse)
        <div class="alert alert-warning">⚠️ Bạn chưa được phân công quản lý kho nào.</div>
      @else

      {{-- ======= SUMMARY MINI CARDS ======= --}}
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem">
        <div class="stat-mini stat-mini--green">
          <div class="stat-mini__icon">📥</div>
          <div>
            <div class="stat-mini__value">{{ number_format($totalIn) }}</div>
            <div class="stat-mini__label">Tổng nhập kỳ này</div>
          </div>
        </div>
        <div class="stat-mini stat-mini--orange">
          <div class="stat-mini__icon">📤</div>
          <div>
            <div class="stat-mini__value">{{ number_format($totalOut) }}</div>
            <div class="stat-mini__label">Tổng xuất kỳ này</div>
          </div>
        </div>
        <div class="stat-mini stat-mini--teal">
          <div class="stat-mini__icon">📊</div>
          <div>
            <div class="stat-mini__value">{{ number_format($totalIn - $totalOut) }}</div>
            <div class="stat-mini__label">Chênh lệch (nhập - xuất)</div>
          </div>
        </div>
        <div class="stat-mini stat-mini--blue">
          <div class="stat-mini__icon">📅</div>
          <div>
            <div class="stat-mini__value">
              {{ $period === 'week' ? '7 ngày' : '30 ngày' }}
            </div>
            <div class="stat-mini__label">Kỳ thống kê</div>
          </div>
        </div>
      </div>

      {{-- ======= BIỂU ĐỒ CỘT ======= --}}
      <div class="chart-wrap">
        <div class="chart-wrap__header">
          <h3>📊 Biểu đồ nhập xuất theo ngày</h3>
          <div style="display:flex;gap:.5rem;align-items:center">
            <span class="chart-legend chart-legend--green"></span> <span style="font-size:.8rem;color:#475569">Nhập kho</span>
            <span class="chart-legend chart-legend--orange" style="margin-left:.5rem"></span> <span style="font-size:.8rem;color:#475569">Xuất kho</span>
          </div>
        </div>
        <div style="position:relative;height:360px;padding:.5rem 0">
          <canvas id="stockChart"></canvas>
        </div>
      </div>

      {{-- ======= BẢNG CHI TIẾT ======= --}}
      <div class="table-wrap" style="margin-top:1.5rem">
        <div class="table-header">
          <h3>📋 Chi tiết theo ngày</h3>
        </div>
        <div style="overflow-x:auto">
          <table>
            <thead>
              <tr>
                <th>Ngày</th>
                <th style="color:#16a34a">📥 Nhập</th>
                <th style="color:#f97316">📤 Xuất</th>
                <th>Chênh lệch</th>
              </tr>
            </thead>
            <tbody>
              @php
                $labels  = $chartLabels;
                $ins     = $chartDataIn;
                $outs    = $chartDataOut;
                $count   = count($labels);
              @endphp
              @for ($i = 0; $i < $count; $i++)
                @php
                  $diff = $ins[$i] - $outs[$i];
                @endphp
                <tr>
                  <td style="font-weight:600">{{ $labels[$i] }}</td>
                  <td style="color:#16a34a;font-weight:600">
                    {{ $ins[$i] > 0 ? '+'.number_format($ins[$i]) : '—' }}
                  </td>
                  <td style="color:#f97316;font-weight:600">
                    {{ $outs[$i] > 0 ? '-'.number_format($outs[$i]) : '—' }}
                  </td>
                  <td>
                    @if($diff > 0)
                      <span style="color:#16a34a">+{{ number_format($diff) }}</span>
                    @elseif($diff < 0)
                      <span style="color:#dc2626">{{ number_format($diff) }}</span>
                    @else
                      <span style="color:#94a3b8">0</span>
                    @endif
                  </td>
                </tr>
              @endfor
            </tbody>
            <tfoot>
              <tr style="background:#f8fafc;font-weight:700">
                <td>Tổng cộng</td>
                <td style="color:#16a34a">+{{ number_format($totalIn) }}</td>
                <td style="color:#f97316">-{{ number_format($totalOut) }}</td>
                <td>
                  @php $netDiff = $totalIn - $totalOut; @endphp
                  <span style="color: {{ $netDiff >= 0 ? '#16a34a' : '#dc2626' }}">
                    {{ $netDiff >= 0 ? '+' : '' }}{{ number_format($netDiff) }}
                  </span>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      @endif {{-- end if warehouse --}}

    </div>
  </main>
</div>
@endsection

@push('styles')
<style>
.period-btn {
  padding: .4rem .9rem; border-radius: 8px; font-size: .8rem; font-weight: 600;
  border: 2px solid #e2e8f0; background: #fff; cursor: pointer;
  transition: all 0.2s; color: #64748b;
}
.period-btn:hover { border-color: #0d9488; color: #0d9488; }
.period-btn--active { background: #0d9488; color: #fff !important; border-color: #0d9488 !important; }

.stat-divider { width: 1px; height: 28px; background: #e2e8f0; }

.stat-mini {
  display: flex; align-items: center; gap: .875rem;
  background: #fff; border-radius: 12px; padding: 1rem 1.25rem;
  box-shadow: 0 4px 24px rgba(0,0,0,.06); border-left: 4px solid transparent;
  transition: all .3s;
}
.stat-mini:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(0,0,0,.1); }
.stat-mini--green  { border-color: #16a34a; }
.stat-mini--orange { border-color: #f97316; }
.stat-mini--teal   { border-color: #0d9488; }
.stat-mini--blue   { border-color: #2563eb; }

.stat-mini__icon {
  font-size: 1.5rem; width: 44px; height: 44px; border-radius: 10px;
  background: #f1f5f9; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.stat-mini__value { font-size: 1.5rem; font-weight: 800; line-height: 1; }
.stat-mini__label { font-size: .75rem; color: #64748b; margin-top: .15rem; }

.chart-wrap {
  background: #fff; border-radius: 14px;
  box-shadow: 0 4px 24px rgba(0,0,0,.06); padding: 1.25rem;
}
.chart-wrap__header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 1rem; flex-wrap: wrap; gap: .5rem;
}
.chart-wrap__header h3 { font-size: 1rem; font-weight: 700; }
.chart-legend {
  display: inline-block; width: 14px; height: 14px; border-radius: 4px;
}
.chart-legend--green  { background: rgba(22,163,74,.8); }
.chart-legend--orange { background: rgba(249,115,22,.8); }
</style>
@endpush

@push('scripts')
<script>
(function() {
  const labels   = @json($chartLabels);
  const dataIn   = @json($chartDataIn);
  const dataOut  = @json($chartDataOut);

  const ctx = document.getElementById('stockChart');
  if (!ctx) return;

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Nhập kho',
          data: dataIn,
          backgroundColor: 'rgba(22,163,74,0.75)',
          borderColor: 'rgba(22,163,74,1)',
          borderWidth: 1.5,
          borderRadius: 6,
          borderSkipped: false,
        },
        {
          label: 'Xuất kho',
          data: dataOut,
          backgroundColor: 'rgba(249,115,22,0.75)',
          borderColor: 'rgba(249,115,22,1)',
          borderWidth: 1.5,
          borderRadius: 6,
          borderSkipped: false,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: 'rgba(30,41,59,0.92)',
          titleColor: '#e2e8f0',
          bodyColor: '#94a3b8',
          padding: 12,
          cornerRadius: 8,
          callbacks: {
            label: function(context) {
              const val = context.parsed.y;
              return ` ${context.dataset.label}: ${val.toLocaleString('vi-VN')}`;
            }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { font: { size: 11 }, color: '#94a3b8' }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(0,0,0,0.04)' },
          ticks: {
            font: { size: 11 }, color: '#94a3b8',
            callback: (v) => v.toLocaleString('vi-VN')
          }
        }
      }
    }
  });
})();
</script>
@endpush

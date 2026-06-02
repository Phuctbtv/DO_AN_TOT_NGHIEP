@extends('layouts.app')
@section('title', 'Xác nhận xuất kho – ' . $trip->trip_code . ' - ĐẠI PHÚC')

@push('styles')
<style>
  /* ── Info cards ─────────────────────────────────────────── */
  .info-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.05);
    overflow: hidden;
    margin-bottom: 1.5rem;
  }
  .info-card-header {
    padding: .85rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
  }
  .info-card-header h3 { font-size: .95rem; font-weight: 700; color: #0f172a; margin: 0; }

  /* ── Trip meta grid ─────────────────────────────────────── */
  .meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    padding: 1.25rem;
  }
  .meta-item label {
    display: block; font-size: .72rem; font-weight: 700;
    color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: .3rem;
  }
  .meta-item .meta-value {
    font-size: .92rem; font-weight: 700; color: #1e293b;
  }

  /* ── Stock status badges ─────────────────────────────────── */
  .badge-ok      { background:#dcfce7;color:#15803d;padding:.25rem .75rem;border-radius:20px;font-size:.8rem;font-weight:700;display:inline-flex;align-items:center;gap:.3rem;white-space:nowrap; }
  .badge-short   { background:#fee2e2;color:#b91c1c;padding:.25rem .75rem;border-radius:20px;font-size:.8rem;font-weight:700;display:inline-flex;align-items:center;gap:.3rem;white-space:nowrap; }

  /* ── Table rows ─────────────────────────────────────────── */
  .stock-row { transition: background .15s; }
  .stock-row:hover { background: #f8fafc; }
  .stock-row.row-short { background: #fff5f5; }
  .stock-row.row-short:hover { background: #fee2e2; }

  /* ── Confirm button ─────────────────────────────────────── */
  .btn-confirm-export {
    display: inline-flex; align-items: center; gap: .55rem;
    padding: .75rem 2rem;
    background: linear-gradient(135deg, #059669, #0d9488);
    color: #fff; border: none; border-radius: 10px;
    font-size: 1rem; font-weight: 800; cursor: pointer;
    box-shadow: 0 6px 20px rgba(5,150,105,.4);
    transition: all .2s;
  }
  .btn-confirm-export:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(5,150,105,.5); }
  .btn-confirm-export:active { transform: translateY(0); }

  /* ── Back button ─────────────────────────────────────────── */
  .btn-back {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .6rem 1.2rem;
    background: #fff; color: #64748b;
    border: 1.5px solid #e2e8f0; border-radius: 8px;
    font-size: .88rem; font-weight: 600; text-decoration: none;
    transition: all .2s;
  }
  .btn-back:hover { background: #f8fafc; border-color: #cbd5e1; color: #334155; }

  /* ── Alert shortage banner ───────────────────────────────── */
  .shortage-banner {
    background: linear-gradient(135deg, #fff5f5, #fee2e2);
    border: 2px solid #fca5a5; border-radius: 12px;
    padding: 1rem 1.25rem; margin-bottom: 1.5rem;
    display: flex; align-items: flex-start; gap: .75rem;
  }
  .all-ok-banner {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border: 2px solid #86efac; border-radius: 12px;
    padding: 1rem 1.25rem; margin-bottom: 1.5rem;
    display: flex; align-items: center; gap: .75rem;
  }

  /* ── Progress bar for stock ratio ───────────────────────── */
  .stock-bar { height: 5px; border-radius: 4px; overflow: hidden; background: #e2e8f0; margin-top: .3rem; }
  .stock-bar-fill { height: 100%; border-radius: 4px; transition: width .4s; }
</style>
@endpush

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true, confirming: false }">

  {{-- SIDEBAR --}}
  @include('partials.warehouse-sidebar', ['activeMenu' => 'stock_outs'])

  {{-- MAIN --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '📤 Xác nhận xuất kho'])

    <div style="padding:1.5rem">

      {{-- FLASH MESSAGES --}}
      @if(session('success'))
        <div style="background:#dcfce7;color:#15803d;border:1px solid #86efac;border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1.25rem;font-size:.9rem">
          {!! session('success') !!}
        </div>
      @endif
      @if(session('error'))
        <div style="background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1.25rem;font-size:.9rem">
          {!! session('error') !!}
        </div>
      @endif

      {{-- ── THÔNG TIN CHUYẾN XE ─────────────────────────────── --}}
      <div class="info-card">
        <div class="info-card-header">
          <h3>🚛 Thông tin chuyến xe</h3>
          <span style="font-family:monospace;font-weight:800;color:#7c3aed;background:#ede9fe;padding:.25rem .75rem;border-radius:8px;font-size:.88rem">
            {{ $trip->trip_code }}
          </span>
        </div>
        <div class="meta-grid">
          <div class="meta-item">
            <label>👨‍✈️ Tài xế</label>
            <div class="meta-value">{{ $trip->driver->name ?? '—' }}</div>
          </div>
          <div class="meta-item">
            <label>🏭 Kho xuất</label>
            <div class="meta-value">{{ $trip->warehouse->name ?? '—' }}</div>
          </div>
          <div class="meta-item">
            <label>🚗 Phương tiện</label>
            <div class="meta-value">{{ $trip->vehicle_info }}</div>
          </div>
          <div class="meta-item">
            <label>📦 Số loại hàng</label>
            <div class="meta-value">{{ $details->count() }} loại</div>
          </div>
          <div class="meta-item">
            <label>📅 Ngày tạo</label>
            <div class="meta-value">{{ $trip->created_at->format('d/m/Y H:i') }}</div>
          </div>
          <div class="meta-item">
            <label>👤 Người tạo</label>
            <div class="meta-value">{{ $trip->creator->name ?? '—' }}</div>
          </div>
          @if($trip->notes)
          <div class="meta-item" style="grid-column:1/-1">
            <label>📝 Ghi chú</label>
            <div class="meta-value" style="font-weight:400;color:#64748b">{{ $trip->notes }}</div>
          </div>
          @endif
        </div>
      </div>

      {{-- ── TRẠNG THÁI TỒN KHO TỔNG QUAN ────────────────────── --}}
      @if($allSufficient)
        <div class="all-ok-banner">
          <span style="font-size:2rem">✅</span>
          <div>
            <div style="font-weight:800;color:#15803d;font-size:1rem">Tất cả mặt hàng đều đủ tồn kho!</div>
            <div style="font-size:.85rem;color:#166534">Bạn có thể tiến hành xác nhận xuất kho ngay bây giờ.</div>
          </div>
        </div>
      @else
        <div class="shortage-banner">
          <span style="font-size:2rem">⚠️</span>
          <div>
            <div style="font-weight:800;color:#b91c1c;font-size:1rem">Một số mặt hàng không đủ tồn kho!</div>
            <div style="font-size:.85rem;color:#991b1b">Vui lòng kiểm tra bảng bên dưới. Cần bổ sung hàng trước khi xuất kho.</div>
          </div>
        </div>
      @endif

      {{-- ── BẢNG HÀNG CẦN XUẤT ─────────────────────────────── --}}
      <div class="info-card">
        <div class="info-card-header">
          <h3>📦 Danh sách hàng cần xuất</h3>
          <div style="display:flex;gap:.75rem;font-size:.8rem">
            <span style="display:flex;align-items:center;gap:.3rem">
              <span style="width:10px;height:10px;border-radius:50%;background:#10b981;display:inline-block"></span>
              Đủ tồn
            </span>
            <span style="display:flex;align-items:center;gap:.3rem">
              <span style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block"></span>
              Thiếu hàng
            </span>
          </div>
        </div>

        <div style="overflow-x:auto">
          <table style="width:100%;border-collapse:collapse;font-size:.9rem">
            <thead>
              <tr style="background:#f8fafc">
                <th style="padding:.75rem 1rem;text-align:left;font-size:.73rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">STT</th>
                <th style="padding:.75rem 1rem;text-align:left;font-size:.73rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Mặt hàng</th>
                <th style="padding:.75rem 1rem;text-align:right;font-size:.73rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0;white-space:nowrap">Yêu cầu</th>
                <th style="padding:.75rem 1rem;text-align:right;font-size:.73rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0;white-space:nowrap">Tồn kho</th>
                <th style="padding:.75rem 1rem;text-align:center;font-size:.73rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e2e8f0">Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              @foreach($details as $detail)
                @php
                  $ratio = $detail->quantity_loaded > 0
                    ? min(100, round($detail->available_stock / $detail->quantity_loaded * 100))
                    : 100;
                  $barColor = $detail->is_sufficient ? '#10b981' : '#ef4444';
                @endphp
                <tr class="stock-row {{ $detail->is_sufficient ? '' : 'row-short' }}"
                    style="border-bottom:1px solid #f1f5f9">

                  {{-- STT --}}
                  <td style="padding:.85rem 1rem;color:#9ca3af;font-size:.82rem;width:48px">{{ $loop->iteration }}</td>

                  {{-- Mặt hàng --}}
                  <td style="padding:.85rem 1rem">
                    <div style="font-weight:700;color:#1e293b">{{ $detail->supply->name ?? '—' }}</div>
                    <div style="font-size:.78rem;color:#94a3b8">
                      {{ $detail->supply->unit ?? '' }}
                      @if($detail->supply->category)
                        · {{ $detail->supply->category->name }}
                      @endif
                    </div>
                  </td>

                  {{-- Yêu cầu --}}
                  <td style="padding:.85rem 1rem;text-align:right">
                    <span style="font-size:1.05rem;font-weight:800;color:#7c3aed">
                      {{ number_format($detail->quantity_loaded) }}
                    </span>
                    <span style="font-size:.78rem;color:#94a3b8;margin-left:.2rem">{{ $detail->supply->unit ?? '' }}</span>
                  </td>

                  {{-- Tồn kho --}}
                  <td style="padding:.85rem 1rem;text-align:right;min-width:120px">
                    <span style="font-size:1.05rem;font-weight:800;color:{{ $detail->is_sufficient ? '#10b981' : '#ef4444' }}">
                      {{ number_format($detail->available_stock) }}
                    </span>
                    <span style="font-size:.78rem;color:#94a3b8;margin-left:.2rem">{{ $detail->supply->unit ?? '' }}</span>
                    {{-- Progress bar --}}
                    <div class="stock-bar" style="margin-top:.4rem">
                      <div class="stock-bar-fill" style="width:{{ $ratio }}%;background:{{ $barColor }}"></div>
                    </div>
                    <div style="font-size:.72rem;color:#94a3b8;margin-top:.2rem;text-align:right">{{ $ratio }}%</div>
                  </td>

                  {{-- Trạng thái --}}
                  <td style="padding:.85rem 1rem;text-align:center">
                    @if($detail->is_sufficient)
                      <span class="badge-ok">✅ Đủ hàng</span>
                    @else
                      @php $shortage = $detail->quantity_loaded - $detail->available_stock; @endphp
                      <div>
                        <span class="badge-short">❌ Thiếu {{ number_format($shortage) }}</span>
                      </div>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- ── SUMMARY ROW ─────────────────────────────────────── --}}
        <div style="padding:1rem 1.25rem;background:#f8fafc;border-top:2px solid #e2e8f0;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap">
          <div style="font-size:.85rem;color:#64748b">
            <strong style="color:#0f172a">Tổng số loại hàng:</strong> {{ $details->count() }}
          </div>
          <div style="font-size:.85rem;color:#64748b">
            <strong style="color:#10b981">Đủ tồn:</strong> {{ $details->where('is_sufficient', true)->count() }}
          </div>
          <div style="font-size:.85rem;color:#64748b">
            <strong style="color:#ef4444">Thiếu hàng:</strong> {{ $details->where('is_sufficient', false)->count() }}
          </div>
        </div>
      </div>

      {{-- ── ACTION BUTTONS ───────────────────────────────────── --}}
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">

        {{-- Quay lại --}}
        <a href="{{ route('warehouse.stock_outs.index') }}" class="btn-back">
          ← Quay lại danh sách
        </a>

        {{-- Xác nhận xuất kho (chỉ hiện khi đủ tồn) --}}
        @if($allSufficient)
          <form action="{{ route('warehouse.stock_outs.confirm', $trip) }}" method="POST"
                x-on:submit.prevent="
                  confirming = true;
                  if(confirm('⚠️ Xác nhận xuất kho chuyến {{ $trip->trip_code }}?\n\nHành động này sẽ:\n• Trừ tồn kho tất cả mặt hàng\n• Chuyển trạng thái sang Đang giao\n• Không thể hoàn tác!')) {
                    $el.submit();
                  } else {
                    confirming = false;
                  }
                ">
            @csrf
            <button type="submit" class="btn-confirm-export" :disabled="confirming">
              <span x-show="!confirming">📤 Xác nhận xuất kho</span>
              <span x-show="confirming" x-cloak>⏳ Đang xử lý...</span>
            </button>
          </form>
        @else
          <div style="display:flex;align-items:center;gap:.5rem;background:#fff5f5;border:1.5px solid #fca5a5;border-radius:10px;padding:.65rem 1.1rem">
            <span style="font-size:1.1rem">🚫</span>
            <span style="font-size:.88rem;color:#b91c1c;font-weight:600">Cần bổ sung hàng trước khi xuất kho</span>
          </div>
        @endif
      </div>

    </div>
  </main>
</div>
@endsection

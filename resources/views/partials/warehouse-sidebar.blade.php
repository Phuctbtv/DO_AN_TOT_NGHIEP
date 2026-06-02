{{-- ======================================================
     Warehouse Sidebar – dùng chung cho tất cả trang thủ kho
     Truyền biến $activeMenu để highlight menu đang active
     Ví dụ: @include('partials.warehouse-sidebar', ['activeMenu' => 'stock_ins'])
     ====================================================== --}}

<aside class="sidebar" :class="{ 'open': sidebarOpen }">
  <div class="sidebar-logo">🌊 ĐẠI <span>PHÚC</span></div>

  {{-- TỔNG QUAN --}}
  <div class="sidebar-section">KHO HÀNG</div>
  <nav class="sidebar-nav">
    <a href="{{ route('warehouse.overview') }}" @class(['active' => ($activeMenu ?? '') === 'overview'])>
      <span class="nav-icon">📊</span> Tổng quan
    </a>
  </nav>

  {{-- NHẬP XUẤT KHO --}}
  <div class="sidebar-section">QUẢN LÝ HÀNG HÓA</div>
  <nav class="sidebar-nav">
    <a href="{{ route('warehouse.stock_ins.create') }}" @class(['active' => ($activeMenu ?? '') === 'stock_ins-create'])>
      <span class="nav-icon">📥</span> Nhập kho
    </a>
    <a href="{{ route('warehouse.stock_ins.index') }}" @class(['active' => ($activeMenu ?? '') === 'stock_ins'])>
      <span class="nav-icon">📋</span> Lịch sử nhập kho
    </a>
    <a href="{{ route('warehouse.stock_outs.index') }}" @class(['active' => ($activeMenu ?? '') === 'stock_outs'])>
      <span class="nav-icon">📤</span> Xuất kho
    </a>
    <a href="{{ route('warehouse.inventory.index') }}" @class(['active' => ($activeMenu ?? '') === 'inventory'])>
      <span class="nav-icon">🗄️</span> Tồn kho hiện tại
    </a>
  </nav>

  {{-- BÁO CÁO --}}
  <div class="sidebar-section">BÁO CÁO</div>
  <nav class="sidebar-nav">
    <a href="{{ route('warehouse.statistics') }}" @class(['active' => ($activeMenu ?? '') === 'statistics'])>
      <span class="nav-icon">📈</span> Thống kê
    </a>
    <a href="{{ route('warehouse.alerts') }}" @class(['active' => ($activeMenu ?? '') === 'alerts'])>
      <span class="nav-icon">🔔</span> Cảnh báo tồn kho
    </a>
    <a href="#" onclick="document.getElementById('modal-stock-report').style.display='flex';return false;"
       @class(['active' => ($activeMenu ?? '') === 'stock-report'])>
      <span class="nav-icon">📊</span> Báo cáo nhập xuất
    </a>
  </nav>

  <div style="margin-top:auto;padding:1rem 1.25rem;border-top:1px solid rgba(255,255,255,.1)">
    <a href="/" style="display:flex;align-items:center;gap:.5rem;font-size:.82rem;color:rgba(255,255,255,.5);padding:.35rem 0">
      ← Về trang chủ
    </a>
  </div>
</aside>

{{-- ══════════════════════════════════════════════════════
     MODAL XUẤT BÁO CÁO NHẬP XUẤT KHO
     (render 1 lần, dùng chung toàn bộ trang kho)
     ══════════════════════════════════════════════════════ --}}
@php
  $modalWarehouses = \App\Models\Warehouse::where('manager_id', auth()->id())->orderBy('name')->get();
@endphp
<div id="modal-stock-report"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99999;align-items:center;justify-content:center"
     onclick="if(event.target===this)this.style.display='none'">
  <div style="background:#fff;border-radius:18px;padding:2rem;width:100%;max-width:460px;box-shadow:0 24px 64px rgba(0,0,0,.22);position:relative">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
      <div>
        <h3 style="font-size:1.1rem;font-weight:700;color:#0f172a;margin:0">📊 Báo cáo nhập xuất kho</h3>
        <p style="font-size:.8rem;color:#64748b;margin:.25rem 0 0">Chọn tháng và kho để xuất file Excel</p>
      </div>
      <button onclick="document.getElementById('modal-stock-report').style.display='none'"
              style="background:#f1f5f9;border:none;width:32px;height:32px;border-radius:8px;font-size:1.1rem;cursor:pointer;color:#64748b">✕</button>
    </div>

    <form method="GET" action="{{ route('warehouse.reports.stock.export') }}">

      {{-- Tháng / Năm --}}
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1rem">
        <div>
          <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem">Tháng</label>
          <select name="month"
                  style="width:100%;padding:.6rem .9rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.88rem;color:#0f172a;background:#fff">
            @for($m = 1; $m <= 12; $m++)
              <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                Tháng {{ $m }}
              </option>
            @endfor
          </select>
        </div>
        <div>
          <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem">Năm</label>
          <select name="year"
                  style="width:100%;padding:.6rem .9rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.88rem;color:#0f172a;background:#fff">
            @for($y = now()->year; $y >= now()->year - 4; $y--)
              <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
          </select>
        </div>
      </div>

      {{-- Kho (chỉ hiện nếu quản lý nhiều kho) --}}
      @if($modalWarehouses->count() > 1)
        <div style="margin-bottom:1rem">
          <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem">Kho</label>
          <select name="warehouse_id"
                  style="width:100%;padding:.6rem .9rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.88rem;color:#0f172a;background:#fff">
            <option value="">🏭 Tất cả kho</option>
            @foreach($modalWarehouses as $wh)
              <option value="{{ $wh->id }}">{{ $wh->name }}</option>
            @endforeach
          </select>
        </div>
      @else
        {{-- Auto-fill kho duy nhất --}}
        <input type="hidden" name="warehouse_id" value="{{ $modalWarehouses->first()?->id }}">
      @endif

      {{-- Nội dung file --}}
      <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.82rem;color:#166534">
        📄 File Excel gồm: <strong>STT, Mặt hàng, Đơn vị, Tồn đầu kỳ, Nhập trong kỳ, Xuất trong kỳ, Tồn cuối kỳ</strong> + dòng Tổng cộng
      </div>

      <div style="display:flex;gap:.75rem">
        <button type="button" onclick="document.getElementById('modal-stock-report').style.display='none'"
                style="flex:1;padding:.7rem;border:1.5px solid #e2e8f0;background:#fff;border-radius:8px;font-weight:600;color:#64748b;cursor:pointer">
          Huỷ
        </button>
        <button type="submit"
                style="flex:2;padding:.7rem;background:linear-gradient(135deg,#0d9488,#0891b2);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:.95rem;box-shadow:0 2px 8px rgba(13,148,136,.3)">
          📥 Tải xuống Excel
        </button>
      </div>
    </form>
  </div>
</div>

@extends('layouts.app')
@section('title', 'Chi tiết phiếu nhập - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  {{-- SIDEBAR --}}
  @include('partials.warehouse-sidebar', ['activeMenu' => 'stock_ins'])

  {{-- MAIN --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '📋 Chi tiết phiếu nhập'])

    <div style="padding:1.5rem">

      {{-- BREADCRUMB --}}
      <div style="display:flex;align-items:center;gap:.5rem;font-size:.83rem;color:#64748b;margin-bottom:1.5rem">
        <a href="{{ route('warehouse.dashboard') }}" style="color:#0d9488;text-decoration:none">Dashboard</a>
        <span>›</span>
        <a href="{{ route('warehouse.stock_ins.index') }}" style="color:#0d9488;text-decoration:none">Lịch sử nhập kho</a>
        <span>›</span>
        <span style="color:#1e293b;font-weight:600">Chi tiết #{{ $stockIn->id }}</span>
      </div>

      <div style="display:grid;grid-template-columns:1fr 380px;gap:1.5rem;align-items:start">

        {{-- LEFT: THÔNG TIN --}}
        <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;box-shadow:0 2px 12px rgba(0,0,0,.05);overflow:hidden">
          <div style="background:linear-gradient(135deg,#0d9488,#0891b2);padding:1.25rem 1.5rem;color:#fff">
            <div style="font-size:1rem;font-weight:700">📥 Phiếu nhập kho #{{ $stockIn->id }}</div>
            <div style="font-size:.82rem;opacity:.8;margin-top:.2rem">
              Nhập ngày {{ $stockIn->received_date->format('d/m/Y H:i') }}
            </div>
          </div>

          <div style="padding:1.5rem">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem">

              {{-- Kho --}}
              <div style="grid-column:1/-1;background:#f0fdfa;border:1px solid #a7f3d0;border-radius:10px;padding:1rem">
                <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#0d9488;margin-bottom:.3rem">🏭 Kho nhận hàng</div>
                <div style="font-size:1rem;font-weight:700;color:#0f172a">{{ $stockIn->warehouse->name ?? '—' }}</div>
                <div style="font-size:.82rem;color:#64748b;margin-top:.15rem">{{ $stockIn->warehouse->address ?? '' }}</div>
              </div>

              {{-- Mặt hàng --}}
              <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:1rem">
                <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#d97706;margin-bottom:.3rem">📦 Mặt hàng</div>
                <div style="font-size:1rem;font-weight:700;color:#0f172a">{{ $stockIn->supply->name ?? '—' }}</div>
                <div style="font-size:.82rem;color:#64748b;margin-top:.15rem">
                  Đơn vị: {{ $stockIn->supply->unit ?? '—' }}
                  @if($stockIn->supply->category)
                    · Danh mục: {{ $stockIn->supply->category->name }}
                  @endif
                </div>
              </div>

              {{-- Số lượng --}}
              <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:1rem">
                <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#16a34a;margin-bottom:.3rem">🔢 Số lượng</div>
                <div style="font-size:1.8rem;font-weight:800;color:#0d9488">+{{ number_format($stockIn->quantity) }}</div>
                <div style="font-size:.82rem;color:#64748b">{{ $stockIn->supply->unit ?? 'đơn vị' }}</div>
              </div>

              {{-- Ngày nhập --}}
              <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:1rem">
                <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#64748b;margin-bottom:.3rem">📅 Ngày nhập</div>
                <div style="font-size:1rem;font-weight:700;color:#0f172a">{{ $stockIn->received_date->format('d/m/Y') }}</div>
                <div style="font-size:.82rem;color:#64748b">Lúc {{ $stockIn->received_date->format('H:i') }}</div>
              </div>

              {{-- Nguồn tài trợ --}}
              <div style="grid-column:1/-1;background:#fdf4ff;border:1px solid #e9d5ff;border-radius:10px;padding:1rem">
                <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#7c3aed;margin-bottom:.3rem">🤝 Nguồn tài trợ</div>
                <div style="font-size:.95rem;font-weight:600;color:#0f172a">
                  {{ $stockIn->donor_info ?: '(Không có thông tin nguồn tài trợ)' }}
                </div>
              </div>

              {{-- Người nhập --}}
              <div style="grid-column:1/-1;display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0">
                <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0891b2);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:700;flex-shrink:0">
                  {{ strtoupper(substr($stockIn->creator->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                  <div style="font-size:.75rem;color:#64748b;text-transform:uppercase;letter-spacing:.4px;font-weight:700">Người lập phiếu</div>
                  <div style="font-weight:700;color:#0f172a">{{ $stockIn->creator->name ?? '—' }}</div>
                  <div style="font-size:.78rem;color:#94a3b8">{{ $stockIn->creator->email ?? '' }}</div>
                </div>
                <div style="margin-left:auto;font-size:.78rem;color:#94a3b8">
                  Tạo {{ $stockIn->created_at->format('d/m/Y H:i') }}
                </div>
              </div>

            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:.75rem;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid #f1f5f9">
              <a href="{{ route('warehouse.stock_ins.index') }}"
                 style="padding:.6rem 1.2rem;border:1.5px solid #d1d5db;border-radius:8px;font-size:.88rem;font-weight:700;color:#374151;text-decoration:none;display:flex;align-items:center;gap:.4rem">
                ← Quay lại danh sách
              </a>
              <form action="{{ route('warehouse.stock_ins.destroy', $stockIn) }}" method="POST"
                    onsubmit="return confirm('⚠️ Xác nhận xóa phiếu nhập này?')"
                    style="margin-left:auto">
                @csrf @method('DELETE')
                <button type="submit"
                        style="padding:.6rem 1.2rem;background:#fee2e2;color:#dc2626;border:1.5px solid #fca5a5;border-radius:8px;font-size:.88rem;font-weight:700;cursor:pointer;font-family:inherit">
                  🗑️ Xóa phiếu nhập
                </button>
              </form>
            </div>
          </div>
        </div>

        {{-- RIGHT: ẢNH --}}
        <div>
          <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;box-shadow:0 2px 12px rgba(0,0,0,.05);overflow:hidden">
            <div style="padding:.85rem 1.1rem;border-bottom:1px solid #f1f5f9;font-weight:700;color:#0f172a;font-size:.95rem">
              🖼️ Ảnh hàng hóa
            </div>
            <div style="padding:1rem">
              @if($stockIn->image_url)
                <img src="{{ $stockIn->image_url }}"
                     alt="Ảnh hàng hóa"
                     style="width:100%;border-radius:10px;border:1px solid #e2e8f0;object-fit:cover"
                     onerror="this.onerror=null;this.src='';this.parentElement.innerHTML='<div style=\'text-align:center;padding:2rem;color:#94a3b8\'>⚠️ Không tải được ảnh</div>'">
                <a href="{{ $stockIn->image_url }}" target="_blank" rel="noopener"
                   style="display:block;text-align:center;margin-top:.75rem;font-size:.82rem;color:#0d9488;text-decoration:none">
                  🔗 Xem ảnh đầy đủ ↗
                </a>
              @else
                <div style="text-align:center;padding:2.5rem 1rem;color:#94a3b8">
                  <div style="font-size:3rem;margin-bottom:.75rem">📷</div>
                  <div style="font-size:.88rem">Không có ảnh đính kèm</div>
                </div>
              @endif
            </div>
          </div>
        </div>

      </div>{{-- end two-col grid --}}

    </div>
  </main>
</div>
@endsection

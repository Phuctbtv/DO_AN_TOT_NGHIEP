@extends('layouts.app')
@section('title', 'Chờ phê duyệt - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  @include('partials.admin-sidebar', ['activeMenu' => 'approvals'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '⏳ Danh sách chờ phê duyệt'])

    <div style="padding:1.5rem">

      {{-- FLASH --}}
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

      {{-- BANNER SỐ LƯỢNG --}}
      <div style="background:linear-gradient(135deg,#f59e0b,#f97316);border-radius:16px;padding:1.5rem 2rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1.5rem;color:#fff">
        <div style="font-size:3rem;line-height:1">⏳</div>
        <div>
          <div style="font-size:2.5rem;font-weight:800;line-height:1">{{ $pendingCount }}</div>
          <div style="font-size:1rem;opacity:.9;margin-top:.25rem">đơn đang chờ xem xét và phê duyệt</div>
          <div style="font-size:.8rem;opacity:.7;margin-top:.25rem">Mỗi đơn cần được duyệt trước khi hộ dân nhận được hỗ trợ</div>
        </div>
      </div>

      {{-- DANH SÁCH --}}
      @forelse($households as $hh)
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:1.25rem;margin-bottom:1rem;display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:start">

          {{-- Thông tin --}}
          <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:.75rem">
            <div>
              <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.2rem">Họ tên</div>
              <div style="font-weight:700;color:#0f172a;font-size:1rem">{{ $hh->household_name }}</div>
              <div style="font-size:.8rem;color:#64748b;margin-top:.2rem">{{ $hh->member_count }} người / hộ</div>
            </div>
            <div>
              <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.2rem">CCCD</div>
              <div style="font-family:monospace;color:#0d9488;font-weight:600">{{ $hh->resident?->identity_card }}</div>
            </div>
            <div>
              <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.2rem">SĐT</div>
              <div style="color:#475569;font-weight:500">{{ $hh->phone ?? '—' }}</div>
            </div>
            <div>
              <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.2rem">Địa chỉ</div>
              <div style="color:#475569;font-size:.875rem">{{ Str::limit($hh->address, 60) }}</div>
            </div>
            <div>
              <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.2rem">GPS</div>
              @if($hh->lat && $hh->lng)
                <div style="color:#0d9488;font-size:.8rem">📍 {{ number_format($hh->lat,4) }}, {{ number_format($hh->lng,4) }}</div>
              @else
                <div style="color:#94a3b8;font-size:.8rem">Không có</div>
              @endif
            </div>
            <div>
              <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.2rem">Ngày đăng ký</div>
              <div style="color:#64748b;font-size:.875rem">{{ $hh->created_at->format('d/m/Y H:i') }}</div>
            </div>
            @if($hh->scene_image)
              <div>
                <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.4rem">Ảnh hiện trường</div>
                <img src="{{ $hh->scene_image }}" alt="Ảnh hiện trường"
                     style="width:80px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;cursor:pointer"
                     onclick="window.open('{{ $hh->scene_image }}','_blank')">
              </div>
            @endif
          </div>

          {{-- ACTIONS --}}
          <div style="display:flex;flex-direction:column;gap:.75rem;min-width:140px">
            <a href="{{ route('admin.households.show', $hh) }}?from=pending"
               style="display:block;text-align:center;background:#f1f5f9;color:#475569;padding:.5rem 1rem;border-radius:8px;font-size:.825rem;font-weight:600;text-decoration:none">
              👁️ Xem chi tiết
            </a>

            {{-- Nút duyệt nhanh --}}
            <form method="POST" action="{{ route('admin.households.approve', $hh) }}"
                  onsubmit="return confirm('Xác nhận PHÊ DUYỆT hộ dân {{ addslashes($hh->household_name) }}?')">
              @csrf
              <button type="submit"
                      style="width:100%;background:#10b981;color:#fff;border:none;padding:.5rem 1rem;border-radius:8px;font-size:.825rem;font-weight:700;cursor:pointer">
                ✅ Phê duyệt
              </button>
            </form>

            {{-- Nút từ chối nhanh --}}
            <button onclick="openRejectModal({{ $hh->id }}, '{{ addslashes($hh->household_name) }}')"
                    style="background:#ef4444;color:#fff;border:none;padding:.5rem 1rem;border-radius:8px;font-size:.825rem;font-weight:700;cursor:pointer;width:100%">
              ❌ Từ chối
            </button>
          </div>

        </div>
      @empty
        <div style="text-align:center;padding:4rem;background:#fff;border-radius:16px;border:2px dashed #e2e8f0">
          <div style="font-size:4rem;margin-bottom:1rem">🎉</div>
          <h3 style="color:#0f172a;font-weight:700;margin-bottom:.5rem">Không còn đơn chờ duyệt!</h3>
          <p style="color:#64748b">Tất cả đơn đăng ký đã được xử lý.</p>
          <a href="{{ route('admin.households.index') }}"
             style="display:inline-block;margin-top:1rem;background:#0d9488;color:#fff;padding:.6rem 1.5rem;border-radius:8px;font-weight:600;text-decoration:none">
            Xem tất cả hộ dân →
          </a>
        </div>
      @endforelse

      {{-- Pagination --}}
      @if($households->hasPages())
        <div style="margin-top:1.25rem">{{ $households->links() }}</div>
      @endif

    </div>
  </main>
</div>

{{-- MODAL TỪ CHỐI --}}
<div id="rejectModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:16px;padding:2rem;max-width:480px;width:90%;position:relative">
    <h3 style="font-weight:700;font-size:1.1rem;color:#0f172a;margin-bottom:.25rem">❌ Từ chối đơn đăng ký</h3>
    <p id="rejectHouseholdName" style="color:#64748b;font-size:.875rem;margin-bottom:1.25rem"></p>

    <form id="rejectForm" method="POST">
      @csrf
      <div style="margin-bottom:1rem">
        <label style="display:block;font-weight:600;font-size:.875rem;color:#374151;margin-bottom:.5rem">
          Lý do từ chối <span style="color:#ef4444">*</span>
        </label>
        <textarea name="rejection_reason" rows="4"
                  placeholder="Vui lòng nhập rõ lý do từ chối để thông báo cho hộ dân biết..."
                  required minlength="10"
                  style="width:100%;padding:.75rem;border:1px solid #e2e8f0;border-radius:8px;font-size:.875rem;resize:vertical;box-sizing:border-box"></textarea>
        <div style="font-size:.75rem;color:#94a3b8;margin-top:.25rem">Tối thiểu 10 ký tự</div>
      </div>
      <div style="display:flex;gap:.75rem">
        <button type="button" onclick="closeRejectModal()"
                style="flex:1;border:1px solid #e2e8f0;background:#fff;padding:.65rem;border-radius:8px;font-weight:600;cursor:pointer;color:#64748b">
          Huỷ
        </button>
        <button type="submit"
                style="flex:1;background:#ef4444;color:#fff;border:none;padding:.65rem;border-radius:8px;font-weight:700;cursor:pointer">
          Xác nhận từ chối
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function openRejectModal(id, name) {
  document.getElementById('rejectModal').style.display = 'flex';
  document.getElementById('rejectHouseholdName').textContent = 'Hộ dân: ' + name;
  document.getElementById('rejectForm').action = '/admin/households/' + id + '/reject';
}
function closeRejectModal() {
  document.getElementById('rejectModal').style.display = 'none';
}
document.getElementById('rejectModal').addEventListener('click', function(e) {
  if (e.target === this) closeRejectModal();
});
</script>
@endpush
@endsection

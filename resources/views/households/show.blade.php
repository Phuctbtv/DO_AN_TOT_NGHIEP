@extends('layouts.app')
@section('title', 'Chi tiết Hộ dân - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true, showReject: false }">

  @include('partials.admin-sidebar', ['activeMenu' => 'households'])

  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '🏠 Chi tiết Hộ dân'])

    <div style="padding:1.5rem">

      {{-- BACK --}}
      @php
        $backFrom = request('from', 'index');
        $backUrl  = $backFrom === 'pending'
          ? route('admin.households.pending')
          : route('admin.households.index');
        $backLabel = $backFrom === 'pending'
          ? '← Quay lại danh sách chờ duyệt'
          : '← Quay lại danh sách hộ dân';
      @endphp
      <div style="margin-bottom:1.25rem">
        <a href="{{ $backUrl }}"
           style="color:#64748b;font-size:.875rem;text-decoration:none;display:inline-flex;align-items:center;gap:.35rem">
          {{ $backLabel }}
        </a>
      </div>

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

      @php
        $statusColors = ['pending'=>['#fef3c7','#f59e0b'], 'active'=>['#d1fae5','#10b981'], 'rejected'=>['#fee2e2','#ef4444']];
        [$sbg, $sfg] = $statusColors[$household->status] ?? ['#f1f5f9','#64748b'];
      @endphp

      <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

        {{-- ============ CỘT TRÁI: Thông tin ============ --}}
        <div>

          {{-- Card thông tin cơ bản --}}
          <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem;margin-bottom:1.25rem">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem">
              <div>
                <h2 style="font-size:1.4rem;font-weight:800;color:#0f172a;margin:0">{{ $household->household_name }}</h2>
                <div style="color:#64748b;font-size:.875rem;margin-top:.25rem">{{ $household->member_count }} người / hộ</div>
              </div>
              <span style="background:{{ $sbg }};color:{{ $sfg }};padding:.4rem 1rem;border-radius:999px;font-size:.8rem;font-weight:700">
                {{ $household->status_label }}
              </span>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
              <div style="background:#f8fafc;padding:.875rem;border-radius:10px">
                <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.3rem">Số CCCD</div>
                <div style="font-family:monospace;font-weight:700;color:#0d9488;font-size:1rem">{{ $household->resident?->identity_card }}</div>
              </div>
              <div style="background:#f8fafc;padding:.875rem;border-radius:10px">
                <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.3rem">Số điện thoại</div>
                <div style="font-weight:600;color:#0f172a">{{ $household->phone ?? '—' }}</div>
              </div>
              <div style="background:#f8fafc;padding:.875rem;border-radius:10px;grid-column:span 2">
                <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.3rem">Địa chỉ</div>
                <div style="font-weight:500;color:#0f172a">{{ $household->address }}</div>
              </div>
              <div style="background:#f8fafc;padding:.875rem;border-radius:10px">
                <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.3rem">Ngày đăng ký</div>
                <div style="font-weight:600;color:#0f172a">{{ $household->created_at->format('d/m/Y H:i') }}</div>
              </div>
              <div style="background:#f8fafc;padding:.875rem;border-radius:10px">
                <div style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:.3rem">Mức ưu tiên</div>
                <div style="font-weight:600;color:#0f172a">Cấp {{ $household->priority_level }}</div>
              </div>
            </div>
          </div>

          {{-- Ảnh hiện trường --}}
          @if($household->scene_image)
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem;margin-bottom:1.25rem">
              <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:1rem">📸 Ảnh hiện trường lũ lụt</h3>
              <img src="{{ $household->scene_image }}" alt="Ảnh hiện trường"
                   style="width:100%;max-height:400px;object-fit:cover;border-radius:10px;cursor:pointer"
                   onclick="window.open('{{ $household->scene_image }}','_blank')"
                   title="Nhấn để xem ảnh gốc">
              <p style="font-size:.75rem;color:#94a3b8;margin-top:.5rem;text-align:center">Nhấn vào ảnh để xem kích thước đầy đủ</p>
            </div>
          @endif

          {{-- Bản đồ --}}
          @if($household->lat && $household->lng)
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem;margin-bottom:1.25rem">
              <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:1rem">
                📍 Vị trí GPS
                <span style="font-weight:400;color:#64748b;font-size:.8rem;margin-left:.5rem">
                  ({{ number_format($household->lat, 6) }}, {{ number_format($household->lng, 6) }})
                </span>
              </h3>
              <div id="householdMap" style="height:300px;border-radius:10px;border:1px solid #e2e8f0"></div>
            </div>
          @endif

          {{-- Lý do từ chối --}}
          @if($household->isRejected() && $household->rejection_reason)
            <div style="background:#fff5f5;border:1px solid #fca5a5;border-radius:16px;padding:1.5rem;margin-bottom:1.25rem">
              <h3 style="font-size:1rem;font-weight:700;color:#dc2626;margin-bottom:.75rem">❌ Lý do từ chối</h3>
              <p style="color:#991b1b;font-size:.9rem;line-height:1.6">{{ $household->rejection_reason }}</p>
            </div>
          @endif

          {{-- QR Code nếu đã active --}}
          @if($household->isActive() && $household->qr_code)
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:16px;padding:1.5rem;margin-bottom:1.25rem;text-align:center">
              <h3 style="font-size:1rem;font-weight:700;color:#15803d;margin-bottom:1rem">✅ QR Code hộ dân</h3>
              <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($household->qr_code) }}&margin=10"
                   alt="QR Code" style="width:200px;height:200px;border-radius:8px;border:4px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,.1)">
              <div style="margin-top:.75rem;font-family:monospace;font-weight:700;color:#15803d;font-size:1.1rem">
                {{ $household->qr_code }}
              </div>
            </div>
          @endif

        </div>

        {{-- ============ CỘT PHẢI: Actions ============ --}}
        <div style="position:sticky;top:1rem">

          @if($household->isPending())
            {{-- PHÊ DUYỆT --}}
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem;margin-bottom:1rem">
              <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:1rem">⚡ Hành động</h3>

              <form method="POST" action="{{ route('admin.households.approve', $household) }}"
                    onsubmit="return confirm('Xác nhận PHÊ DUYỆT hộ dân này?')">
                @csrf

                {{-- CHỌN MỨC ƯU TIÊN --}}
                <div style="margin-bottom:1rem">
                  <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.5rem">
                    🚨 Mức ưu tiên hỗ trợ
                  </label>
                  <select name="priority_level"
                          style="width:100%;padding:.6rem .875rem;border:1px solid #e2e8f0;border-radius:8px;font-size:.875rem;color:#0f172a;background:#fff;cursor:pointer">
                    <option value="1" {{ $household->priority_level == 1 ? 'selected' : '' }}
                            style="color:#dc2626;font-weight:600">
                      🔴 Cấp 1 – Khẩn cấp (gia đình đặc biệt khó khăn)
                    </option>
                    <option value="2" {{ $household->priority_level == 2 ? 'selected' : '' }}
                            style="color:#f59e0b;font-weight:600">
                      🟡 Cấp 2 – Cao (cần hỗ trợ sớm)
                    </option>
                    <option value="3" {{ $household->priority_level == 3 ? 'selected' : '' }}>
                      🟢 Cấp 3 – Bình thường
                    </option>
                    <option value="4" {{ $household->priority_level == 4 ? 'selected' : '' }}
                            style="color:#64748b">
                      ⚪ Cấp 4 – Thấp (tạm thời ổn định)
                    </option>
                  </select>
                  <div style="font-size:.72rem;color:#94a3b8;margin-top:.3rem">
                    Hiện tại: Cấp {{ $household->priority_level }} — có thể đổi trước khi duyệt
                  </div>
                </div>

                <button type="submit"
                        style="width:100%;background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;padding:1rem;border-radius:10px;font-size:1rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.5rem">
                  ✅ PHÊ DUYỆT NGAY
                </button>
              </form>

              <button @click="showReject = true"
                      style="width:100%;margin-top:.75rem;background:#fff;color:#ef4444;border:2px solid #ef4444;padding:.875rem;border-radius:10px;font-size:.9rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.5rem">
                ❌ TỪ CHỐI
              </button>
            </div>

            {{-- MODAL TỪ CHỐI (Alpine.js) --}}
            <div x-show="showReject" x-transition
                 style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center"
                 :style="showReject ? 'display:flex' : 'display:none'"
                 @click.self="showReject = false">
              <div style="background:#fff;border-radius:16px;padding:2rem;max-width:480px;width:90%">
                <h3 style="font-weight:700;font-size:1.1rem;color:#0f172a;margin-bottom:.5rem">❌ Từ chối đơn đăng ký</h3>
                <p style="color:#64748b;font-size:.875rem;margin-bottom:1.25rem">
                  Hộ dân: <strong>{{ $household->household_name }}</strong>
                </p>
                <form method="POST" action="{{ route('admin.households.reject', $household) }}">
                  @csrf
                  <div style="margin-bottom:1rem">
                    <label style="display:block;font-weight:600;font-size:.875rem;color:#374151;margin-bottom:.5rem">
                      Lý do từ chối <span style="color:#ef4444">*</span>
                    </label>
                    <textarea name="rejection_reason" rows="5"
                              placeholder="VD: Địa chỉ không rõ ràng, không thể xác minh thông tin, nằm ngoài vùng hỗ trợ..."
                              required minlength="10"
                              style="width:100%;padding:.75rem;border:1px solid #e2e8f0;border-radius:8px;font-size:.875rem;resize:vertical;box-sizing:border-box;outline:none"
                              onfocus="this.style.borderColor='#0d9488'"
                              onblur="this.style.borderColor='#e2e8f0'"></textarea>
                  </div>
                  <div style="display:flex;gap:.75rem">
                    <button type="button" @click="showReject = false"
                            style="flex:1;border:1px solid #e2e8f0;background:#fff;padding:.65rem;border-radius:8px;font-weight:600;cursor:pointer;color:#64748b">
                      Huỷ
                    </button>
                    <button type="submit"
                            style="flex:1;background:#ef4444;color:#fff;border:none;padding:.65rem;border-radius:8px;font-weight:700;cursor:pointer">
                      Xác nhận
                    </button>
                  </div>
                </form>
              </div>
            </div>

          @elseif($household->isActive())
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:16px;padding:1.25rem;text-align:center">
              <div style="font-size:2rem;margin-bottom:.5rem">✅</div>
              <div style="font-weight:700;color:#15803d;margin-bottom:.25rem">Đã phê duyệt</div>
              <div style="font-size:.8rem;color:#16a34a">Hộ dân đang hoạt động</div>
            </div>
          @else
            <div style="background:#fff5f5;border:1px solid #fca5a5;border-radius:16px;padding:1.25rem;text-align:center">
              <div style="font-size:2rem;margin-bottom:.5rem">❌</div>
              <div style="font-weight:700;color:#dc2626;margin-bottom:.25rem">Đã từ chối</div>
              <div style="font-size:.8rem;color:#ef4444">Đơn này đã bị từ chối</div>
            </div>
          @endif

          {{-- Thông tin tài khoản --}}
          <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.25rem;margin-top:1rem">
            <h4 style="font-size:.875rem;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:.875rem">Tài khoản hệ thống</h4>
            <div style="font-size:.85rem;color:#475569;line-height:1.8">
              <div>👤 Email: <span style="font-family:monospace;color:#0d9488">{{ $household->resident?->email }}</span></div>
              <div>🔑 Mật khẩu: <span style="color:#94a3b8;font-size:.8rem">Mặc định = số CCCD</span></div>
              @if($household->resident?->telegram_chat_id)
                <div>✈️ Telegram: <span style="color:#10b981">Đã liên kết</span></div>
              @else
                <div>✈️ Telegram: <span style="color:#94a3b8">Chưa liên kết</span></div>
              @endif
            </div>
          </div>

        </div>
      </div>

    </div>
  </main>
</div>
@endsection

@push('scripts')
@if($household->lat && $household->lng)
<script>
document.addEventListener('DOMContentLoaded', function() {
  if (typeof L !== 'undefined') {
    const lat = {{ $household->lat }};
    const lng = {{ $household->lng }};
    const map = L.map('householdMap').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap'
    }).addTo(map);
    L.marker([lat, lng])
      .addTo(map)
      .bindPopup(`<b>{{ addslashes($household->household_name) }}</b><br>{{ addslashes($household->address) }}`)
      .openPopup();
  }
});
</script>
@endif
@endpush

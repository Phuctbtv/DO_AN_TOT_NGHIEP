@extends('layouts.app')
@section('title', 'Hộ dân - ĐẠI PHÚC')

@section('content')
<div x-data="{
    showHelp: false,
    showEdit: false,
    feedbackSent: false,
    editLoading: false,
    feedbackLoading: false,
}" >

  @include('partials.dashboard-header', ['pageTitle' => '🏠 Dashboard Hộ dân'])

  {{-- Flash messages --}}
  @if(session('success'))
    <div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:.85rem 1.25rem;margin:.75rem 1rem;border-radius:10px;font-size:.875rem;display:flex;align-items:center;gap:.5rem">
      ✅ {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:.85rem 1.25rem;margin:.75rem 1rem;border-radius:10px;font-size:.875rem;display:flex;align-items:center;gap:.5rem">
      ❌ {{ session('error') }}
    </div>
  @endif

  <div class="resident-container">

    {{-- ==================== TRẠNG THÁI ĐĂNG KÝ ==================== --}}
    @if(!$household)
      <div style="background:linear-gradient(135deg,#667eea,#764ba2);border-radius:16px;padding:2rem;margin-bottom:1.5rem;text-align:center;color:#fff">
        <div style="font-size:3rem;margin-bottom:1rem">📝</div>
        <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:.5rem">Bạn chưa đăng ký cứu trợ</h2>
        <p style="font-size:.875rem;opacity:.9;margin-bottom:1.25rem">Vui lòng đăng ký để nhận hỗ trợ trong đợt bão lũ này</p>
        <a href="/" style="display:inline-block;background:#fff;color:#667eea;padding:.7rem 1.75rem;border-radius:10px;font-weight:700;text-decoration:none">
          📝 Đăng ký ngay →
        </a>
      </div>

    @elseif($household->isPending())
      {{-- CHỜ DUYỆT --}}
      <div style="background:linear-gradient(135deg,#f59e0b,#f97316);border-radius:16px;padding:2rem;margin-bottom:1.5rem;text-align:center;color:#fff">
        <div style="font-size:3rem;margin-bottom:.75rem">⏳</div>
        <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:.5rem">Đang chờ Admin phê duyệt</h2>
        <p style="font-size:.875rem;opacity:.95;line-height:1.6">
          Đơn đăng ký của bạn đã được tiếp nhận thành công.<br>
          Admin sẽ xem xét và thông báo kết quả sớm nhất có thể.
        </p>
        <div style="margin-top:1rem;background:rgba(255,255,255,.2);border-radius:10px;padding:.75rem;font-size:.8rem">
          📅 Ngày đăng ký: {{ $household->created_at->format('d/m/Y H:i') }}
        </div>
      </div>

      {{-- Timeline pending --}}
      <div class="qr-card" style="text-align:left;margin-bottom:1.5rem">
        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:.5rem">📍 Tiến trình đơn đăng ký</h3>
        <div class="timeline">
          @foreach($timelineSteps as $step)
          <div class="timeline-step {{ $step['status'] }}">
            <div class="step-title">{{ $step['icon'] }} {{ $step['title'] }}</div>
            <div class="step-desc">{{ $step['desc'] }}</div>
          </div>
          @endforeach
        </div>
      </div>

    @elseif($household->isRejected())
      {{-- BỊ TỪ CHỐI --}}
      <div style="background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:16px;padding:2rem;margin-bottom:1.5rem;text-align:center;color:#fff">
        <div style="font-size:3rem;margin-bottom:.75rem">❌</div>
        <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:.5rem">Đơn đăng ký bị từ chối</h2>
        @if($household->rejection_reason)
          <div style="background:rgba(255,255,255,.15);border-radius:10px;padding:1rem;margin-top:.75rem;text-align:left;font-size:.875rem;line-height:1.6">
            <strong>📝 Lý do:</strong><br>{{ $household->rejection_reason }}
          </div>
        @endif
        <p style="font-size:.8rem;opacity:.85;margin-top:1rem">
          Nếu cần hỗ trợ thêm, vui lòng liên hệ hotline: <strong>1900.636.838</strong>
        </p>
      </div>

    @else
      {{-- ĐÃ ĐƯỢC DUYỆT – ACTIVE --}}

      {{-- QR CODE --}}
      <div class="qr-card" style="margin-bottom:1.5rem">
        <span class="section-badge">📱 Mã QR của bạn</span>
        <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:.25rem">Mã nhận hàng cứu trợ</h2>
        <p style="font-size:.85rem;color:#64748b;margin-bottom:1rem">Đưa mã QR này cho tài xế khi nhận hàng</p>

        <div class="qr-placeholder" id="qrCodeDisplay">
          <div style="text-align:center">
            @if($qrImageUrl)
              <img src="{{ $qrImageUrl }}"
                   alt="QR Code nhận hàng"
                   style="width:200px;height:200px;border-radius:12px;border:4px solid #fff;box-shadow:0 8px 24px rgba(0,0,0,.12)">
            @else
              <div style="width:200px;height:200px;background:#f1f5f9;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:auto;color:#64748b;font-size:.85rem">Chưa có QR</div>
            @endif
          </div>
        </div>

        <p style="font-size:.8rem;color:#64748b;margin-top:.75rem">
          Mã hộ dân: <strong style="color:#0d9488">{{ $household->qr_code }}</strong>
        </p>
        <p style="font-size:.8rem;color:#64748b">
          CCCD: {{ substr($user->identity_card, 0, 4) . '****' . substr($user->identity_card, -4) }}
        </p>

        {{-- Nút tải QR --}}
        <div style="margin-top:1rem">
          <a href="{{ route('resident.qr.download') }}"
             target="_blank"
             class="btn btn-teal btn-sm"
             style="display:inline-flex;align-items:center;gap:.4rem">
            ⬇️ Tải QR Code
          </a>
        </div>
      </div>

      {{-- THÔNG TIN HỘ --}}
      <div class="qr-card" style="text-align:left;margin-bottom:1.5rem">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
          <h3 style="font-size:1.1rem;font-weight:700;margin:0">📋 Thông tin hộ dân</h3>
          <button @click="showEdit = true"
                  class="btn btn-outline-teal btn-sm"
                  style="font-size:.8rem">
            ✏️ Chỉnh sửa
          </button>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
          <div style="background:#f8fafc;padding:.75rem;border-radius:8px">
            <div style="font-size:.75rem;color:#64748b">Tên hộ dân</div>
            <div style="font-weight:700;color:#0d9488">{{ $household->household_name }}</div>
          </div>
          <div style="background:#f8fafc;padding:.75rem;border-radius:8px">
            <div style="font-size:.75rem;color:#64748b">Ngày duyệt</div>
            <div style="font-weight:600">{{ $household->updated_at->format('d/m/Y') }}</div>
          </div>
          <div style="background:#f8fafc;padding:.75rem;border-radius:8px">
            <div style="font-size:.75rem;color:#64748b">Số thành viên</div>
            <div style="font-weight:600">{{ $household->member_count }} người</div>
          </div>
          <div style="background:#f8fafc;padding:.75rem;border-radius:8px">
            <div style="font-size:.75rem;color:#64748b">Trạng thái</div>
            <div style="font-weight:600;color:{{ $household->status_color }}">
              {{ $household->status_label }}
            </div>
          </div>
        </div>
        <div style="background:#f8fafc;padding:.75rem;border-radius:8px;margin-top:.75rem">
          <div style="font-size:.75rem;color:#64748b">Địa chỉ</div>
          <div style="font-weight:600">{{ $household->address }}</div>
        </div>
        <div style="background:#f8fafc;padding:.75rem;border-radius:8px;margin-top:.75rem">
          <div style="font-size:.75rem;color:#64748b">Số điện thoại</div>
          <div style="font-weight:600">{{ $household->phone ?? 'Chưa cập nhật' }}</div>
        </div>
      </div>

      {{-- TIMELINE động --}}
      <div class="qr-card" style="text-align:left;margin-bottom:1.5rem">
        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:.5rem">📍 Theo dõi tiến trình</h3>
        <p style="font-size:.85rem;color:#64748b;margin-bottom:1rem">Cập nhật trạng thái nhận hàng của bạn</p>
        <div class="timeline">
          @foreach($timelineSteps as $step)
          <div class="timeline-step {{ $step['status'] }}">
            <div class="step-title" style="color:{{ $step['color'] }}">{{ $step['icon'] }} {{ $step['title'] }}</div>
            <div class="step-desc">{{ $step['desc'] }}</div>
          </div>
          @endforeach
        </div>
      </div>

      {{-- LỊCH SỬ NHẬN HÀNG --}}
      <div class="qr-card" style="text-align:left;margin-bottom:1.5rem">
        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem">📦 Lịch sử nhận hàng</h3>

        @if($deliveries->isEmpty())
          <div style="text-align:center;padding:2rem;color:#94a3b8">
            <div style="font-size:2.5rem;margin-bottom:.5rem">📭</div>
            <p style="font-size:.875rem">Chưa có lần nhận hàng nào</p>
          </div>
        @else
          @foreach($deliveries as $delivery)
          <div style="border:1px solid #e2e8f0;border-radius:12px;padding:1rem;margin-bottom:.75rem;background:#fff">
            {{-- Header --}}
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem">
              <div>
                <div style="font-weight:700;font-size:.9rem;color:#0d9488">
                  🚛 {{ $delivery->trip->trip_code ?? 'N/A' }}
                </div>
                <div style="font-size:.75rem;color:#64748b;margin-top:.15rem">
                  {{ $delivery->delivered_at ? $delivery->delivered_at->format('H:i d/m/Y') : 'N/A' }}
                </div>
              </div>
              <span style="font-size:.75rem;padding:.25rem .6rem;border-radius:6px;font-weight:600;
                background:{{ $delivery->status === 'success' ? '#d1fae5' : '#fef3c7' }};
                color:{{ $delivery->status === 'success' ? '#065f46' : '#92400e' }}">
                {{ $delivery->status === 'success' ? '✅ Đã nhận' : '⚠️ Cảnh báo GPS' }}
              </span>
            </div>

            {{-- Danh sách hàng --}}
            @if($delivery->trip && $delivery->trip->tripDetails->isNotEmpty())
            <div style="background:#f8fafc;border-radius:8px;padding:.6rem .75rem;margin-bottom:.6rem">
              <div style="font-size:.75rem;color:#64748b;margin-bottom:.4rem">📋 Hàng hóa nhận được:</div>
              @foreach($delivery->trip->tripDetails as $detail)
                @if($detail->supply)
                <div style="font-size:.8rem;color:#374151;padding:.15rem 0;display:flex;justify-content:space-between">
                  <span>• {{ $detail->supply->name }}</span>
                  <span style="color:#0d9488;font-weight:600">
                    ~{{ $delivery->trip->deliveries->count() > 0 ? floor($detail->quantity_loaded / $delivery->trip->deliveries->count()) : 0 }}
                    {{ $detail->supply->unit ?? 'phần' }}
                  </span>
                </div>
                @endif
              @endforeach
            </div>
            @endif

            {{-- Ảnh minh chứng --}}
            @if($delivery->proof_image_url)
            <div>
              <div style="font-size:.75rem;color:#64748b;margin-bottom:.35rem">📷 Ảnh minh chứng:</div>
              <img src="{{ $delivery->proof_image_url }}"
                   alt="Ảnh minh chứng giao hàng"
                   style="width:100%;max-height:180px;object-fit:cover;border-radius:8px;cursor:pointer"
                   onclick="window.open(this.src,'_blank')">
            </div>
            @endif
          </div>
          @endforeach

          {{-- PHÂN TRANG --}}
          @if($deliveries->hasPages())
            <div style="margin-top:1rem">
              @include('partials.pagination', ['paginator' => $deliveries])
            </div>
          @endif
        @endif
      </div>
    @endif

    {{-- LIÊN HỆ --}}
    <div class="qr-card" style="text-align:left;margin-bottom:1.5rem">
      <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem">📞 Cần hỗ trợ?</h3>
      <div style="display:flex;gap:.75rem;flex-wrap:wrap">
        <a href="tel:1900636838" class="btn btn-orange btn-sm" style="flex:1;justify-content:center;min-width:160px">📞 Hotline: 1900.636.838</a>
        <button class="btn btn-outline-teal btn-sm" @click="showHelp = true" style="flex:1;justify-content:center;min-width:160px">💬 Gửi phản hồi</button>
      </div>
    </div>

    <div style="text-align:center;margin-bottom:2rem">
      <a href="/" class="btn btn-outline btn-sm">← Về trang chủ</a>
    </div>

  </div>{{-- end .resident-container --}}

  {{-- ==================== MODAL: PHẢN HỒI ==================== --}}
  <template x-if="showHelp">
    <div class="modal-overlay" @click.self="showHelp = false">
      <div class="modal-box" @click.stop>
        <div class="modal-header">
          <h3>💬 Gửi phản hồi</h3>
          <button class="modal-close" @click="showHelp = false">✕</button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('resident.feedback') }}" @submit="feedbackLoading = true">
            @csrf
            <div class="form-group">
              <label class="form-label">Nội dung phản hồi <span style="color:#ef4444">*</span></label>
              <textarea name="content" class="form-control" rows="5"
                placeholder="Mô tả vấn đề hoặc phản hồi của bạn... (tối thiểu 10 ký tự)"
                required minlength="10"></textarea>
            </div>
            <button type="submit" class="btn btn-teal btn-lg" style="width:100%" :disabled="feedbackLoading">
              <span x-show="!feedbackLoading">📨 Gửi phản hồi</span>
              <span x-show="feedbackLoading">⏳ Đang gửi...</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </template>

  {{-- ==================== MODAL: CHỈNH SỬA THÔNG TIN ==================== --}}
  @if($household && $household->isActive())
  <template x-if="showEdit">
    <div class="modal-overlay" @click.self="showEdit = false">
      <div class="modal-box" @click.stop>
        <div class="modal-header">
          <h3>✏️ Chỉnh sửa thông tin</h3>
          <button class="modal-close" @click="showEdit = false">✕</button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('resident.update-info') }}" @submit="editLoading = true">
            @csrf
            @method('PATCH')

            <div class="form-group">
              <label class="form-label">Số điện thoại <span style="color:#ef4444">*</span></label>
              <input type="tel" name="phone" class="form-control"
                value="{{ old('phone', $household->phone ?? $user->phone) }}"
                placeholder="Nhập số điện thoại" required>
            </div>

            <div class="form-group">
              <label class="form-label">Số thành viên trong hộ <span style="color:#ef4444">*</span></label>
              <input type="number" name="member_count" class="form-control"
                value="{{ old('member_count', $household->member_count) }}"
                min="1" max="20" required>
              <small style="color:#64748b;font-size:.75rem">Tối thiểu 1, tối đa 20 người</small>
            </div>

            <div style="background:#fef3c7;border-radius:8px;padding:.75rem;margin-bottom:1rem;font-size:.8rem;color:#92400e">
              ⚠️ Địa chỉ không thể thay đổi vì liên quan đến hệ thống GPS giao hàng.
            </div>

            <button type="submit" class="btn btn-teal btn-lg" style="width:100%" :disabled="editLoading">
              <span x-show="!editLoading">💾 Lưu thay đổi</span>
              <span x-show="editLoading">⏳ Đang lưu...</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </template>
  @endif

</div>{{-- end x-data --}}
@endsection

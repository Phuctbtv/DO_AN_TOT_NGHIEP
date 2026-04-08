@extends('layouts.app')
@section('title', 'Hộ dân - ĐẠI PHÚC')

@section('content')
@php
  $user      = auth()->user();
  $household = $user->household; // Eager load quan hệ
@endphp

<div x-data="{ showHelp: false }">

  {{-- ==================== HEADER ==================== --}}
  @include('partials.dashboard-header', ['pageTitle' => '🏠 Dashboard Hộ dân'])

  <div class="resident-container">

    {{-- ==================== TRẠNG THÁI ĐĂNG KÝ ==================== --}}
    @if(!$household)
      {{-- CHƯA ĐĂNG KÝ --}}
      <div style="background:linear-gradient(135deg,#667eea,#764ba2);border-radius:16px;padding:2rem;margin-bottom:1.5rem;text-align:center;color:#fff">
        <div style="font-size:3rem;margin-bottom:1rem">📝</div>
        <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:.5rem">Bạn chưa đăng ký cứu trợ</h2>
        <p style="font-size:.875rem;opacity:.9;margin-bottom:1.25rem">
          Vui lòng đăng ký để nhận hỗ trợ trong đợt bão lũ này
        </p>
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

      {{-- Timeline - step 1 done, step 2 pending --}}
      <div class="qr-card" style="text-align:left;margin-bottom:1.5rem">
        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:.5rem">📍 Tiến trình đơn đăng ký</h3>
        <div class="timeline">
          <div class="timeline-step done">
            <div class="step-title">✅ Đã gửi đăng ký</div>
            <div class="step-desc">Đơn đăng ký đã gửi lúc {{ $household->created_at->format('H:i d/m/Y') }}</div>
          </div>
          <div class="timeline-step active">
            <div class="step-title">⏳ Chờ Admin phê duyệt</div>
            <div class="step-desc">Admin đang xem xét thông tin của bạn</div>
          </div>
          <div class="timeline-step">
            <div class="step-title">📱 Nhận QR Code</div>
            <div class="step-desc">QR code sẽ được cấp sau khi được duyệt</div>
          </div>
          <div class="timeline-step">
            <div class="step-title">📦 Nhận hàng cứu trợ</div>
            <div class="step-desc">Xuất trình QR code khi nhận hàng</div>
          </div>
        </div>
      </div>

    @elseif($household->isRejected())
      {{-- BỊ TỪ CHỐI --}}
      <div style="background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:16px;padding:2rem;margin-bottom:1.5rem;text-align:center;color:#fff">
        <div style="font-size:3rem;margin-bottom:.75rem">❌</div>
        <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:.5rem">Đơn đăng ký bị từ chối</h2>
        @if($household->rejection_reason)
          <div style="background:rgba(255,255,255,.15);border-radius:10px;padding:1rem;margin-top:.75rem;text-align:left;font-size:.875rem;line-height:1.6">
            <strong>📝 Lý do:</strong><br>
            {{ $household->rejection_reason }}
          </div>
        @endif
        <p style="font-size:.8rem;opacity:.85;margin-top:1rem">
          Nếu cần hỗ trợ thêm, vui lòng liên hệ hotline: <strong>1900.636.838</strong>
        </p>
      </div>

    @else
      {{-- ĐÃ ĐƯỢC DUYỆT – ACTIVE → HIỂN THỊ QR --}}

      {{-- QR CODE --}}
      <div class="qr-card" style="margin-bottom:1.5rem">
        <span class="section-badge">📱 Mã QR của bạn</span>
        <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:.25rem">Mã nhận hàng cứu trợ</h2>
        <p style="font-size:.85rem;color:#64748b;margin-bottom:1rem">Đưa mã QR này cho tài xế khi nhận hàng</p>

        <div class="qr-placeholder" id="qrCodeDisplay">
          <div style="text-align:center">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($household->qr_code) }}&margin=10"
                 alt="QR Code nhận hàng"
                 style="width:200px;height:200px;border-radius:12px;border:4px solid #fff;box-shadow:0 8px 24px rgba(0,0,0,.12)"
                 onerror="this.src='https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($household->qr_code) }}'">
          </div>
        </div>

        <p style="font-size:.8rem;color:#64748b;margin-top:.75rem">Mã hộ dân: <strong style="color:#0d9488">{{ $household->qr_code }}</strong></p>
        <p style="font-size:.8rem;color:#64748b">CCCD: {{ substr($user->identity_card, 0, 4) . '****' . substr($user->identity_card, -4) }}</p>
      </div>

      {{-- THÔNG TIN HỘ --}}
      <div class="qr-card" style="text-align:left;margin-bottom:1.5rem">
        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem">📋 Thông tin hộ dân</h3>
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
            <div style="font-weight:600;color:#10b981">✅ Đang hoạt động</div>
          </div>
        </div>
        <div style="background:#f8fafc;padding:.75rem;border-radius:8px;margin-top:.75rem">
          <div style="font-size:.75rem;color:#64748b">Địa chỉ</div>
          <div style="font-weight:600">{{ $household->address }}</div>
        </div>
      </div>

      {{-- TIMELINE --}}
      <div class="qr-card" style="text-align:left;margin-bottom:1.5rem">
        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:.5rem">📍 Theo dõi tiến trình</h3>
        <p style="font-size:.85rem;color:#64748b;margin-bottom:1rem">Cập nhật trạng thái</p>
        <div class="timeline">
          <div class="timeline-step done">
            <div class="step-title">✅ Đã đăng ký</div>
            <div class="step-desc">Đơn đăng ký đã gửi lúc {{ $household->created_at->format('H:i d/m/Y') }}</div>
          </div>
          <div class="timeline-step done">
            <div class="step-title">✅ Admin phê duyệt</div>
            <div class="step-desc">Đơn đã được duyệt — QR code đã cấp</div>
          </div>
          <div class="timeline-step">
            <div class="step-title">🚛 Chờ giao hàng</div>
            <div class="step-desc">Chờ chuyến xe cứu trợ đến khu vực của bạn</div>
          </div>
          <div class="timeline-step">
            <div class="step-title">📦 Nhận hàng</div>
            <div class="step-desc">Xuất trình QR code khi tài xế đến</div>
          </div>
        </div>
      </div>
    @endif

    {{-- ==================== LIÊN HỆ ==================== --}}
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

  </div>

  {{-- MODAL: PHẢN HỒI --}}
  <template x-if="showHelp">
    <div class="modal-overlay" @click.self="showHelp = false">
      <div class="modal-box" @click.stop>
        <div class="modal-header">
          <h3>💬 Gửi phản hồi</h3>
          <button class="modal-close" @click="showHelp = false">✕</button>
        </div>
        <div class="modal-body">
          <form onsubmit="handleFeedback(event)">
            <div class="form-group">
              <label class="form-label">Nội dung</label>
              <textarea class="form-control" rows="4" placeholder="Mô tả vấn đề hoặc phản hồi..." required></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Ảnh đính kèm</label>
              <label class="file-upload">
                <input type="file" accept="image/*" multiple style="display:none" onchange="handleFileUpload(this,'residentPhotoPreview')">
                📎 Chọn ảnh
              </label>
              <div id="residentPhotoPreview" style="display:flex;flex-wrap:wrap;gap:.25rem;margin-top:.5rem"></div>
            </div>
            <button type="submit" class="btn btn-teal btn-lg" style="width:100%">📨 Gửi phản hồi</button>
          </form>
        </div>
      </div>
    </div>
  </template>

</div>
@endsection

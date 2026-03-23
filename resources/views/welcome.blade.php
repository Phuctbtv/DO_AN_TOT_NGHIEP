@extends('layouts.app')

@section('title', 'ĐẠI PHÚC - Hỗ trợ bão lũ | Trang chủ')

@section('content')
<div x-data="{ showRegModal: false, showLookupModal: false, showFeedbackModal: false }">

{{-- ==================== HEADER ==================== --}}
<nav class="navbar">
  <div class="container navbar-inner">
    <a href="/" class="navbar-logo">🌊 ĐẠI <span>PHÚC</span></a>

    <div class="navbar-menu">
      <a href="#" class="active">Trang chủ</a>
      <a href="#lookup-section">Tra cứu</a>
      <a href="#feedback-section">Liên hệ</a>
    </div>

    <div class="navbar-actions">
      <button class="btn btn-outline-teal btn-sm" @click="showLookupModal = true">🔍 Tra cứu CCCD</button>
      <button class="btn btn-orange btn-sm" @click="showRegModal = true">📝 ĐĂNG KÝ CỨU TRỢ</button>
      @auth
        <a href="{{ route('dashboard') }}" class="btn btn-teal btn-sm">📊 Dashboard</a>
      @else
        <a href="{{ route('login') }}" class="btn btn-outline-teal btn-sm">Đăng nhập</a>
        <a href="{{ route('register') }}" class="btn btn-teal btn-sm">Đăng ký</a>
      @endauth
    </div>
  </div>
</nav>

{{-- ==================== HERO BANNER ==================== --}}
<section class="hero-banner">
  <div class="hero-banner-bg" style="background-image: url('{{ asset('images/flood_banner.png') }}')"></div>
  <div class="hero-banner-overlay"></div>
  <div class="hero-banner-content container">
    <div class="hero-banner-badge">🔴 ĐANG HOẠT ĐỘNG</div>
    <h1 class="hero-banner-title">Cứu Trợ Bão Lũ<br><span>Miền Trung Việt Nam</span></h1>
    <p class="hero-banner-sub">
      Hệ thống điều phối cứu trợ minh bạch — theo dõi từng chuyến hàng,<br>
      từng hộ dân được hỗ trợ theo thời gian thực
    </p>
    <div class="hero-banner-actions">
      <button class="btn btn-orange btn-lg" @click="showRegModal = true">
        📝 Đăng ký nhận hỗ trợ
      </button>
      <a href="#map-section" class="btn btn-banner-outline btn-lg">
        🗺️ Xem bản đồ cứu trợ
      </a>
    </div>
    <div class="hero-banner-stats">
      <div class="hb-stat"><span class="hb-num">12,567</span><span class="hb-lbl">Hộ dân</span></div>
      <div class="hb-divider"></div>
      <div class="hb-stat"><span class="hb-num">1,234</span><span class="hb-lbl">Chuyến xe</span></div>
      <div class="hb-divider"></div>
      <div class="hb-stat"><span class="hb-num">487T</span><span class="hb-lbl">Hàng hóa</span></div>
      <div class="hb-divider"></div>
      <div class="hb-stat"><span class="hb-num">340</span><span class="hb-lbl">Tình nguyện viên</span></div>
    </div>
  </div>
  <div class="hero-scroll-hint">
    <span>Cuộn xuống</span>
    <div class="scroll-arrow"></div>
  </div>
</section>

{{-- ==================== BẢN ĐỒ CỨU TRỢ ==================== --}}
<section class="map-section" id="map-section">
  <div class="container">
    <div class="map-section-header">
      <div>
        <span class="section-badge">🗺️ Trực tiếp</span>
        <h2 class="section-title">Bản đồ cứu trợ trực tiếp</h2>
        <p class="section-subtitle">Theo dõi tình hình hỗ trợ bão lũ miền Trung Việt Nam theo thời gian thực</p>
      </div>
      <div class="map-controls">
        <button class="map-ctrl-btn active" onclick="filterMap('all')">Tất cả</button>
        <button class="map-ctrl-btn" onclick="filterMap('urgent')">🔴 Cần gấp</button>
        <button class="map-ctrl-btn" onclick="filterMap('active')">🟡 Đang hỗ trợ</button>
        <button class="map-ctrl-btn" onclick="filterMap('done')">🟢 Đã ổn định</button>
      </div>
    </div>
    <div class="map-wrapper">
      <div class="hero-map" id="mainMap"></div>
      <div class="map-overlay-legend">
        <div class="mol-title">Chú thích</div>
        <div class="mol-item"><i class="legend-red"></i> Cần hỗ trợ gấp</div>
        <div class="mol-item"><i class="legend-yellow"></i> Đang được hỗ trợ</div>
        <div class="mol-item"><i class="legend-green"></i> Đã ổn định</div>
      </div>
    </div>
  </div>
</section>

{{-- ==================== THỐNG KÊ ==================== --}}
<section class="section" style="background:#fff;padding:2.5rem 0">
  <div class="container">
    <div class="stats-row">
      <div class="stat-card animate-in delay-1">
        <div class="icon">🏠</div>
        <div class="number" data-count="12567">0</div>
        <div class="label">Hộ dân đã hỗ trợ</div>
      </div>
      <div class="stat-card animate-in delay-2">
        <div class="icon">🚛</div>
        <div class="number" data-count="1234">0</div>
        <div class="label">Chuyến xe</div>
      </div>
      <div class="stat-card animate-in delay-3">
        <div class="icon">📦</div>
        <div class="number" data-count="487">0</div>
        <div class="label">Tấn hàng</div>
      </div>
      <div class="stat-card animate-in delay-4">
        <div class="icon">🤝</div>
        <div class="number" data-count="340">0</div>
        <div class="label">Tình nguyện viên</div>
      </div>
    </div>
  </div>
</section>

{{-- ==================== BẢNG TIN MINH BẠCH ==================== --}}
<section class="section" style="background:#fff">
  <div class="container">
    <span class="section-badge">🔔 Cập nhật liên tục</span>
    <h2 class="section-title">Bảng tin minh bạch</h2>
    <p class="section-subtitle">Hoạt động giao hàng & cứu trợ mới nhất — minh bạch từng phút</p>

    <div class="ticker-wrap" id="tickerWrap">
      {{-- Filled by JS or static --}}
    </div>
  </div>
</section>

{{-- ==================== TRA CỨU CCCD ==================== --}}
<section class="section" id="lookup-section">
  <div class="container" style="max-width:640px">
    <div class="text-center">
      <span class="section-badge">🔍 Tra cứu</span>
      <h2 class="section-title">Tra cứu bằng số CCCD</h2>
      <p class="section-subtitle">Nhập số CCCD để kiểm tra trạng thái hỗ trợ cứu trợ</p>
    </div>

    <div style="display:flex;gap:.5rem;margin-top:1rem">
      <input type="text" id="cccdInput" class="form-control" placeholder="Nhập số CCCD (VD: 012345678901)" maxlength="12">
      <button class="btn btn-teal" onclick="lookupCCCD()" style="flex-shrink:0">🔍 Tra cứu</button>
    </div>
    <div id="lookupResult"></div>
  </div>
</section>

{{-- ==================== PHẢN HỒI ==================== --}}
<section class="section" id="feedback-section" style="background:#fff">
  <div class="container" style="max-width:640px">
    <div class="text-center">
      <span class="section-badge">💬 Phản hồi</span>
      <h2 class="section-title">Gửi phản hồi / Liên hệ</h2>
      <p class="section-subtitle">Ý kiến của bạn giúp chúng tôi cải thiện công tác cứu trợ</p>
    </div>

    <form onsubmit="handleFeedback(event)" style="margin-top:1rem">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div class="form-group">
          <label class="form-label">Họ tên</label>
          <input type="text" class="form-control" placeholder="Nguyễn Văn A" required>
        </div>
        <div class="form-group">
          <label class="form-label">Số điện thoại</label>
          <input type="tel" class="form-control" placeholder="0901 234 567">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Số CCCD</label>
        <input type="text" class="form-control" placeholder="012345678901" maxlength="12">
      </div>
      <div class="form-group">
        <label class="form-label">Loại phản hồi</label>
        <select class="form-control">
          <option value="">-- Chọn loại phản hồi --</option>
          <option>Góp ý cải thiện</option>
          <option>Phản ánh chậm trễ</option>
          <option>Cảm ơn / Khen ngợi</option>
          <option>Tố cáo sai phạm</option>
          <option>Khác</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Nội dung</label>
        <textarea class="form-control" rows="4" placeholder="Mô tả chi tiết phản hồi của bạn..." required></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Ảnh đính kèm (nếu có)</label>
        <label class="file-upload">
          <input type="file" accept="image/*" multiple style="display:none" onchange="handleFileUpload(this, 'feedbackPreview')">
          📎 Nhấn để chọn ảnh hoặc kéo thả
        </label>
        <div id="feedbackPreview" style="display:flex;flex-wrap:wrap;gap:.25rem;margin-top:.5rem"></div>
      </div>
      <button type="submit" class="btn btn-teal btn-lg" style="width:100%">📨 Gửi phản hồi</button>
    </form>
  </div>
</section>

{{-- ==================== FOOTER ==================== --}}
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      {{-- Cột 1 --}}
      <div>
        <div class="footer-logo">🌊 ĐẠI <span>PHÚC</span></div>
        <p>Hệ thống hỗ trợ bão lũ minh bạch,<br>hiệu quả & nhân văn</p>
        <div class="footer-social">
          <a href="#" title="Facebook">📘</a>
          <a href="#" title="Zalo">💬</a>
          <a href="#" title="Telegram">✈️</a>
        </div>
      </div>
      {{-- Cột 2 --}}
      <div>
        <h4>Về Đại Phúc</h4>
        <p><a href="#">Giới thiệu</a></p>
        <p><a href="#">Cách hoạt động</a></p>
        <p><a href="#">Minh bạch tài chính</a></p>
        <p><a href="#">Đối tác & Tài trợ</a></p>
      </div>
      {{-- Cột 3 --}}
      <div>
        <h4>Hỗ trợ</h4>
        <p><a href="#">Hướng dẫn đăng ký</a></p>
        <p><a href="#">Câu hỏi thường gặp</a></p>
        <p><a href="#">Theo dõi đơn hàng</a></p>
        <p><a href="#">Tình nguyện viên</a></p>
      </div>
      {{-- Cột 4 --}}
      <div>
        <div class="hotline-box">📞 1900.636.838</div>
        <p>📧 hotro@daiphuc.vn</p>
        <p>🏢 123 Trần Phú, TP. Đà Nẵng</p>
        <p style="margin-top:.5rem;font-size:.8rem;color:rgba(255,255,255,.5)">Hoạt động 24/7</p>
      </div>
    </div>
    <div class="footer-bottom">
      &copy; 2026 ĐẠI PHÚC. Tất cả vì đồng bào vùng lũ. ❤️
    </div>
  </div>
</footer>

{{-- ==================== MODAL: ĐĂNG KÝ CỨU TRỢ ==================== --}}
<template x-if="showRegModal">
  <div class="modal-overlay" @click.self="showRegModal = false" x-transition>
    <div class="modal-box" @click.stop>
      <div class="modal-header">
        <h3>📝 Đăng ký nhận hỗ trợ cứu trợ</h3>
        <button class="modal-close" @click="showRegModal = false">✕</button>
      </div>
      <div class="modal-body">
        <form onsubmit="handleRegistration(event)">
          <div class="form-group">
            <label class="form-label">Họ tên <span class="required">*</span></label>
            <input type="text" class="form-control" placeholder="Nguyễn Văn A" required>
          </div>
          <div class="form-group">
            <label class="form-label">Số CCCD <span class="required">*</span></label>
            <input type="text" class="form-control" placeholder="012345678901" maxlength="12" required>
          </div>
          <div class="form-group">
            <label class="form-label">Số điện thoại</label>
            <input type="tel" class="form-control" placeholder="0901 234 567">
          </div>
          <div class="form-group">
            <label class="form-label">Địa chỉ <span class="required">*</span></label>
            <textarea class="form-control" rows="2" placeholder="Số nhà, thôn/xóm, xã/phường, huyện/quận, tỉnh..." required></textarea>
          </div>

          {{-- GPS --}}
          <div class="form-group">
            <label class="form-label">Vị trí GPS</label>
            <div style="display:flex;gap:.5rem;align-items:center">
              <input type="text" id="regLat" class="form-control" placeholder="Vĩ độ" readonly style="flex:1">
              <input type="text" id="regLng" class="form-control" placeholder="Kinh độ" readonly style="flex:1">
              <button type="button" class="btn btn-outline-teal btn-sm" style="flex-shrink:0"
                      onclick="getLocation('regLat','regLng',this)">
                📍 Lấy vị trí hiện tại
              </button>
            </div>
          </div>

          {{-- Upload ảnh --}}
          <div class="form-group">
            <label class="form-label">Ảnh hiện trường</label>
            <label class="file-upload">
              <input type="file" accept="image/*" multiple style="display:none" onchange="handleFileUpload(this, 'regPreview')">
              📸 Nhấn để chọn ảnh tình hình lũ lụt tại nhà
            </label>
            <div id="regPreview" style="display:flex;flex-wrap:wrap;gap:.25rem;margin-top:.5rem"></div>
          </div>

          <button type="submit" class="btn btn-teal btn-lg" style="width:100%">🚀 Gửi đăng ký</button>
          <p style="text-align:center;font-size:.8rem;color:#64748b;margin-top:.75rem">
            Sau khi gửi, admin sẽ xem xét và phê duyệt yêu cầu trong thời gian sớm nhất
          </p>
        </form>
      </div>
    </div>
  </div>
</template>

{{-- ==================== MODAL: TRA CỨU CCCD ==================== --}}
<template x-if="showLookupModal">
  <div class="modal-overlay" @click.self="showLookupModal = false" x-transition>
    <div class="modal-box" @click.stop>
      <div class="modal-header">
        <h3>🔍 Tra cứu bằng số CCCD</h3>
        <button class="modal-close" @click="showLookupModal = false">✕</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Số CCCD</label>
          <div style="display:flex;gap:.5rem">
            <input type="text" id="modalCccdInput" class="form-control" placeholder="Nhập 12 số CCCD" maxlength="12">
            <button class="btn btn-teal" style="flex-shrink:0"
                    onclick="document.getElementById('cccdInput').value=document.getElementById('modalCccdInput').value; lookupCCCD(); document.getElementById('modalLookupResult').innerHTML=document.getElementById('lookupResult').innerHTML;">
              Tra cứu
            </button>
          </div>
        </div>
        <div id="modalLookupResult"></div>
        <div style="margin-top:1rem;padding:1rem;background:#f8fafc;border-radius:8px;font-size:.8rem;color:#64748b">
          <strong>CCCD mẫu để thử:</strong><br>
          012345678901 · 098765432109 · 111222333444
        </div>
      </div>
    </div>
  </div>
</template>

</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Init map
    initMap('mainMap');

    // Fill ticker
    const tickerWrap = document.getElementById('tickerWrap');
    if (tickerWrap) {
      tickerWrap.innerHTML = MOCK.activities.map(a => `
        <div class="ticker-item">
          <span class="ticker-time"><span class="pulse-dot"></span>${a.time}</span>
          <span class="ticker-text">${a.text} <span class="ticker-badge badge-${a.type}">${a.badge}</span></span>
        </div>
      `).join('');
    }
  });
</script>
@endpush

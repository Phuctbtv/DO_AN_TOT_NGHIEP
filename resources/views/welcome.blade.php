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
      <div class="hb-stat"><span class="hb-num">{{ number_format($stats['households']) }}</span><span class="hb-lbl">Hộ dân</span></div>
      <div class="hb-divider"></div>
      <div class="hb-stat"><span class="hb-num">{{ $stats['active_trips'] }}</span><span class="hb-lbl">Xe đang chạy</span></div>
      <div class="hb-divider"></div>
      <div class="hb-stat"><span class="hb-num">{{ $stats['total_kg'] }}T</span><span class="hb-lbl">Hàng hóa</span></div>
      <div class="hb-divider"></div>
      <div class="hb-stat"><span class="hb-num">{{ $stats['drivers'] }}</span><span class="hb-lbl">Tình nguyện viên</span></div>
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
        <button class="map-ctrl-btn" onclick="filterMap('1')">🔴 Cần gấp</button>
        <button class="map-ctrl-btn" onclick="filterMap('2')">🟡 Đang hỗ trợ</button>
        <button class="map-ctrl-btn" onclick="filterMap('3')">🟢 Đã ổn định</button>
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
{{-- Inject markers bản đồ thật từ DB --}}
<script>window.MAP_HOUSEHOLDS = @json($mapHouseholds);</script>

{{-- ==================== THỐNG KÊ ==================== --}}
<section class="section" style="background:#fff;padding:2.5rem 0">
  <div class="container">
    <div class="stats-row">
      <div class="stat-card animate-in delay-1">
        <div class="icon">🏠</div>
        <div class="number" data-count="{{ $stats['households'] }}">0</div>
        <div class="label">Hộ dân đã hỗ trợ</div>
      </div>
      <div class="stat-card animate-in delay-2">
        <div class="icon">🚛</div>
        <div class="number" data-count="{{ $stats['active_trips'] }}">0</div>
        <div class="label">Xe đang giao hàng</div>
      </div>
      <div class="stat-card animate-in delay-3">
        <div class="icon">📦</div>
        <div class="number" data-count="{{ $stats['total_kg'] }}">0</div>
        <div class="label">Tấn hàng đã phát</div>
      </div>
      <div class="stat-card animate-in delay-4">
        <div class="icon">🤝</div>
        <div class="number" data-count="{{ $stats['drivers'] }}">0</div>
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

    <div class="ticker-wrap"
         x-data="activityFeed()"
         x-init="load(); setInterval(load, 30000)">
      <template x-if="items.length === 0">
        <div style="text-align:center;padding:2rem;color:#94a3b8">⏳ Đang tải dữ liệu...</div>
      </template>
      <template x-for="a in items" :key="a.trip_code + a.time">
        <div class="ticker-item">
          <span class="ticker-time"><span class="pulse-dot"></span><span x-text="a.time"></span></span>
          <span class="ticker-text">
            Chuyến <strong x-text="a.trip_code"></strong>
            đã giao <span x-text="a.supply_text"></span>
            tại <em x-text="a.address"></em>
            <span class="ticker-badge badge-success">Thành công</span>
          </span>
        </div>
      </template>
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

    <div x-data="cccdLookup()" style="margin-top:1rem">
      <div style="display:flex;gap:.5rem">
        <input type="text" x-model="cccd" class="form-control"
               placeholder="Nhập 12 số CCCD" maxlength="12"
               @keydown.enter="lookup()">
        <button class="btn btn-teal" @click="lookup()" :disabled="loading" style="flex-shrink:0">
          <span x-show="!loading">🔍 Tra cứu</span>
          <span x-show="loading">⏳...</span>
        </button>
      </div>

      {{-- Kết quả --}}
      <div x-show="result" x-cloak style="margin-top:1rem;background:#f8fafc;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0">
        <template x-if="result && result.found">
          <div>
            <div style="font-size:1.1rem;font-weight:700;color:#0d9488;margin-bottom:.75rem">🏠 Đã tìm thấy thông tin</div>
            <div style="display:grid;gap:.5rem;font-size:.875rem">
              <div><span style="color:#64748b">Họ tên:</span> <strong x-text="result.name"></strong></div>
              <div><span style="color:#64748b">CCCD:</span> <span x-text="result.cccd_masked"></span></div>
              <div><span style="color:#64748b">Địa chỉ:</span> <span x-text="result.address"></span></div>
              <div><span style="color:#64748b">Số thành viên:</span> <span x-text="result.member_count"></span> người</div>
              <div>
                <span style="color:#64748b">Trạng thái:</span>
                <span :style="'color:'+result.status_color" style="font-weight:600" x-text="result.status_label"></span>
              </div>
              <div x-show="result.last_delivery">
                <span style="color:#64748b">Lần nhận gần nhất:</span> <span x-text="result.last_delivery"></span>
              </div>
              <div x-show="!result.last_delivery" style="color:#94a3b8">— Chưa có lần nhận hàng nào</div>
            </div>
          </div>
        </template>
        <template x-if="result && !result.found">
          <div style="text-align:center;color:#ef4444;padding:1rem">
            <div style="font-size:2rem">❌</div>
            <div style="margin-top:.5rem" x-text="result.message"></div>
          </div>
        </template>
      </div>
      <div x-show="error" x-cloak style="margin-top:.75rem;color:#ef4444;font-size:.85rem" x-text="error"></div>
    </div>
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

    <div x-data="publicFeedback()" style="margin-top:1rem">
      <div x-show="sent" x-cloak style="text-align:center;padding:2rem;background:#d1fae5;border-radius:12px;color:#065f46">
        <div style="font-size:2.5rem">✅</div>
        <div style="font-size:1rem;font-weight:700;margin-top:.5rem">Cảm ơn! Phản hồi đã được gửi.</div>
        <p style="font-size:.85rem;margin-top:.25rem">Chúng tôi sẽ xem xét sớm nhất có thể.</p>
      </div>

      <form x-show="!sent" @submit.prevent="submit" enctype="multipart/form-data">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div class="form-group">
            <label class="form-label">Họ tên <span style="color:#ef4444">*</span></label>
            <input type="text" name="name" x-model="form.name" class="form-control" placeholder="Nguyễn Văn A" required>
          </div>
          <div class="form-group">
            <label class="form-label">Số điện thoại</label>
            <input type="tel" name="phone" x-model="form.phone" class="form-control" placeholder="0901 234 567">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Số CCCD <span style="color:#ef4444">*</span></label>
          <input type="text" name="identity_card" x-model="form.identity_card" class="form-control" placeholder="012345678901" maxlength="12" required>
        </div>
        <div class="form-group">
          <label class="form-label">Loại phản hồi <span style="color:#ef4444">*</span></label>
          <select name="type" x-model="form.type" class="form-control" required>
            <option value="">-- Chọn loại phản hồi --</option>
            <option value="suggestion">Góp ý cải thiện</option>
            <option value="complaint">Phản ánh chậm trễ</option>
            <option value="praise">Cảm ơn / Khen ngợi</option>
            <option value="report">Tố cáo sai phạm</option>
            <option value="other">Khác</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Nội dung <span style="color:#ef4444">*</span></label>
          <textarea name="content" x-model="form.content" class="form-control" rows="4"
            placeholder="Mô tả chi tiết phản hồi của bạn..." required></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Ảnh đính kèm (nếu có)</label>
          <label class="file-upload">
            <input type="file" name="image" accept="image/*" style="display:none"
                   @change="form.image = $event.target.files[0]">
            📎 Chọn ảnh minh chứng
          </label>
          <div x-show="form.image" style="font-size:.75rem;color:#0d9488;margin-top:.25rem">
            ✅ Đã chọn: <span x-text="form.image ? form.image.name : ''"></span>
          </div>
        </div>
        <div x-show="errMsg" x-cloak style="color:#ef4444;font-size:.85rem;margin-bottom:.75rem" x-text="errMsg"></div>
        <button type="submit" class="btn btn-teal btn-lg" style="width:100%" :disabled="loading">
          <span x-show="!loading">📨 Gửi phản hồi</span>
          <span x-show="loading">⏳ Đang gửi...</span>
        </button>
      </form>
    </div>
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

        {{-- Thông báo kết quả --}}
        <div id="regAlert" style="display:none;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem;font-weight:500"></div>

        <form id="regForm" onsubmit="handleRegistrationAjax(event)">
          @csrf
          <div class="form-group">
            <label class="form-label">Họ tên <span class="required">*</span></label>
            <input type="text" id="regName" name="name" class="form-control" placeholder="Nguyễn Văn A" required autocomplete="name">
          </div>
          <div class="form-group">
            <label class="form-label">Số CCCD <span class="required">*</span></label>
            <input type="text" id="regCCCD" name="identity_card" class="form-control" placeholder="012345678901" maxlength="12" pattern="[0-9]{12}" required>
            <div style="font-size:.75rem;color:#94a3b8;margin-top:.25rem">Đúng 12 chữ số</div>
          </div>
          <div class="form-group">
            <label class="form-label">Số điện thoại <span class="required">*</span></label>
            <input type="tel" id="regPhone" name="phone" class="form-control" placeholder="0901 234 567" required>
          </div>
          <div class="form-group">
            <label class="form-label">Địa chỉ <span class="required">*</span></label>
            <textarea id="regAddress" name="address" class="form-control" rows="2" placeholder="Số nhà, thôn/xóm, xã/phường, huyện/quận, tỉnh..." required></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Số thành viên trong hộ</label>
            <input type="number" id="regMembers" name="member_count" class="form-control" placeholder="VD: 4" min="1" max="50" value="1">
          </div>

          {{-- GPS --}}
          <div class="form-group">
            <label class="form-label">Vị trí GPS <span style="color:#94a3b8;font-size:.75rem">(tuỳ chọn, giúp định vị chính xác hơn)</span></label>
            <div style="display:flex;gap:.5rem;align-items:center">
              <input type="text" id="regLat" name="lat" class="form-control" placeholder="Vĩ độ" readonly style="flex:1">
              <input type="text" id="regLng" name="lng" class="form-control" placeholder="Kinh độ" readonly style="flex:1">
              <button type="button" class="btn btn-outline-teal btn-sm" style="flex-shrink:0" id="regGpsBtn"
                      onclick="getLocation('regLat','regLng',this)">
                📍 Lấy vị trí
              </button>
            </div>
          </div>

          {{-- Upload ảnh --}}
          <div class="form-group">
            <label class="form-label">Ảnh hiện trường <span style="color:#94a3b8;font-size:.75rem">(tuỳ chọn, tối đa 5MB)</span></label>
            <label class="file-upload" for="regSceneFile">
              <input type="file" id="regSceneFile" name="scene_image" accept="image/*" style="display:none" onchange="handleRegImagePreview(this)">
              📸 Nhấn để chọn ảnh tình hình lũ lụt tại nhà
            </label>
            <div id="regPreview" style="display:flex;flex-wrap:wrap;gap:.25rem;margin-top:.5rem"></div>
          </div>

          <button type="submit" id="regSubmitBtn" class="btn btn-teal btn-lg" style="width:100%">
            🚀 Gửi đăng ký
          </button>
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
        <div x-data="cccdLookup()">
          <div class="form-group">
            <label class="form-label">Số CCCD</label>
            <div style="display:flex;gap:.5rem">
              <input type="text" x-model="cccd" class="form-control"
                     placeholder="Nhập 12 số CCCD" maxlength="12"
                     @keydown.enter="lookup()">
              <button class="btn btn-teal" style="flex-shrink:0"
                      @click="lookup()" :disabled="loading">
                <span x-show="!loading">🔍 Tra cứu</span>
                <span x-show="loading">⏳...</span>
              </button>
            </div>
          </div>

          {{-- Thông báo lỗi validate --}}
          <div x-show="error" x-cloak style="margin-top:.5rem;color:#ef4444;font-size:.85rem" x-text="error"></div>

          {{-- Kết quả --}}
          <div x-show="result" x-cloak style="margin-top:1rem;background:#f8fafc;border-radius:12px;padding:1.25rem;border:1px solid #e2e8f0">
            <template x-if="result && result.found">
              <div>
                <div style="font-size:1.05rem;font-weight:700;color:#0d9488;margin-bottom:.75rem">🏠 Đã tìm thấy thông tin</div>
                <div style="display:grid;gap:.5rem;font-size:.875rem">
                  <div><span style="color:#64748b">Họ tên:</span> <strong x-text="result.name"></strong></div>
                  <div><span style="color:#64748b">CCCD:</span> <span x-text="result.cccd_masked"></span></div>
                  <div><span style="color:#64748b">Địa chỉ:</span> <span x-text="result.address"></span></div>
                  <div><span style="color:#64748b">Số thành viên:</span> <span x-text="result.member_count"></span> người</div>
                  <div>
                    <span style="color:#64748b">Trạng thái:</span>
                    <span :style="'color:'+result.status_color" style="font-weight:600" x-text="result.status_label"></span>
                  </div>
                  <div x-show="result.last_delivery">
                    <span style="color:#64748b">Lần nhận gần nhất:</span> <span x-text="result.last_delivery"></span>
                  </div>
                  <div x-show="!result.last_delivery" style="color:#94a3b8">— Chưa có lần nhận hàng nào</div>
                </div>
              </div>
            </template>
            <template x-if="result && !result.found">
              <div style="text-align:center;color:#ef4444;padding:1rem">
                <div style="font-size:2rem">❌</div>
                <div style="margin-top:.5rem" x-text="result.message"></div>
              </div>
            </template>
          </div>
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
    // Init map với dữ liệu thật từ DB
    initRealMap('mainMap');
  });

  // ============================================================
  //  BẢN ĐỒ THẬT – dùng window.MAP_HOUSEHOLDS
  // ============================================================
  let _allMarkers = [];
  let _map = null;

  function initRealMap(id) {
    _map = L.map(id).setView([16.47, 107.59], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap'
    }).addTo(_map);

    const households = window.MAP_HOUSEHOLDS || [];
    const colorMap = { 1: '#ef4444', 2: '#f59e0b', 3: '#10b981' };

    households.forEach(h => {
      const color = colorMap[h.priority] || '#10b981';
      const icon = L.divIcon({
        className: '',
        html: `<div style="width:14px;height:14px;background:${color};border-radius:50%;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.3)"></div>`,
        iconSize: [14, 14], iconAnchor: [7, 7]
      });
      const marker = L.marker([h.lat, h.lng], { icon })
        .bindPopup(`<b>${h.name}</b><br>${h.address}${h.phone ? '<br>📞 '+h.phone : ''}`)
        .addTo(_map);
      marker._priority = String(h.priority);
      _allMarkers.push(marker);
    });
  }

  function filterMap(priority) {
    document.querySelectorAll('.map-ctrl-btn').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
    _allMarkers.forEach(m => {
      if (priority === 'all' || m._priority === priority) {
        if (!_map.hasLayer(m)) m.addTo(_map);
      } else {
        _map.removeLayer(m);
      }
    });
  }

  // ============================================================
  //  XỬ LÝ ĐĂNG KÝ CỨU TRỢ – AJAX THẬT
  // ============================================================
  async function handleRegistrationAjax(e) {
    e.preventDefault();
    const form     = document.getElementById('regForm');
    const alertEl  = document.getElementById('regAlert');
    const submitBtn = document.getElementById('regSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = '⏳ Đang gửi...';
    alertEl.style.display = 'none';
    const formData = new FormData(form);
    try {
      const res = await fetch('{{ route("household.register") }}', {
        method : 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body   : formData,
      });
      const json = await res.json();
      if (json.success) {
        form.style.display = 'none';
        alertEl.style.cssText = 'display:block;padding:1.25rem;border-radius:12px;background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;font-weight:500;font-size:.9rem;text-align:center';
        alertEl.innerHTML = `<div style="font-size:2.5rem;margin-bottom:.75rem">✅</div><div style="font-size:1rem;font-weight:700">${json.message}</div>`;
      } else {
        alertEl.style.cssText = 'display:block;padding:.875rem 1rem;border-radius:10px;background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;font-size:.875rem';
        let msg = json.message || 'Có lỗi xảy ra.';
        if (json.errors) msg = '⚠️ ' + Object.values(json.errors).flat().join('<br>• ');
        alertEl.innerHTML = msg;
        submitBtn.disabled = false;
        submitBtn.textContent = '🚀 Gửi đăng ký';
      }
    } catch {
      alertEl.style.cssText = 'display:block;padding:.875rem 1rem;border-radius:10px;background:#fee2e2;color:#991b1b;font-size:.875rem';
      alertEl.textContent = '❌ Lỗi kết nối. Vui lòng thử lại.';
      submitBtn.disabled = false;
      submitBtn.textContent = '🚀 Gửi đăng ký';
    }
  }

  function handleRegImagePreview(input) {
    const preview = document.getElementById('regPreview');
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
      const file = input.files[0];
      if (file.size > 5 * 1024 * 1024) { alert('Ảnh quá lớn (tối đa 5MB)'); input.value = ''; return; }
      const reader = new FileReader();
      reader.onload = e => {
        preview.innerHTML = `<div style="position:relative;display:inline-block"><img src="${e.target.result}" style="width:120px;height:90px;object-fit:cover;border-radius:8px;border:2px solid #0d9488"><button type="button" onclick="clearRegImage()" style="position:absolute;top:-6px;right:-6px;background:#ef4444;color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:12px;cursor:pointer">✕</button></div>`;
      };
      reader.readAsDataURL(file);
    }
  }
  function clearRegImage() {
    document.getElementById('regSceneFile').value = '';
    document.getElementById('regPreview').innerHTML = '';
  }

  // ============================================================
  //  ALPINE.JS COMPONENTS
  // ============================================================

  // 1) Bảng tin polling
  function activityFeed() {
    return {
      items: [],
      async load() {
        try {
          const r = await fetch('{{ route("api.activity-feed") }}');
          if (r.ok) this.items = await r.json();
        } catch {}
      }
    };
  }

  // 2) Tra cứu CCCD
  function cccdLookup() {
    return {
      cccd: '', loading: false, result: null, error: '',
      async lookup() {
        if (this.cccd.length !== 12) { this.error = 'CCCD phải đúng 12 số.'; return; }
        this.loading = true; this.result = null; this.error = '';
        try {
          const r = await fetch('{{ route("api.cccd-lookup") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ cccd: this.cccd })
          });
          const data = await r.json();
          this.result = r.ok ? data : { found: false, message: data.message || 'Không tìm thấy.' };
        } catch { this.error = 'Lỗi kết nối. Vui lòng thử lại.'; }
        finally { this.loading = false; }
      }
    };
  }

  // 3) Form phản hồi công khai
  function publicFeedback() {
    return {
      form: { name:'', phone:'', identity_card:'', type:'', content:'', image: null },
      loading: false, sent: false, errMsg: '',
      async submit() {
        this.loading = true; this.errMsg = '';
        const fd = new FormData();
        Object.entries(this.form).forEach(([k, v]) => { if (v) fd.append(k, v); });
        fd.append('_token', '{{ csrf_token() }}');
        try {
          const r = await fetch('{{ route("api.public-feedback") }}', { method:'POST', body: fd });
          const data = await r.json();
          if (data.success) { this.sent = true; }
          else { this.errMsg = data.message || 'Gửi thất bại. Vui lòng thử lại.'; }
        } catch { this.errMsg = 'Lỗi kết nối. Vui lòng thử lại.'; }
        finally { this.loading = false; }
      }
    };
  }
</script>
@endpush


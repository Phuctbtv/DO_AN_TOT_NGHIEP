<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ĐẠI PHÚC - Hỗ trợ bão lũ</title>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    {{-- CSS trang chủ --}}
    <link rel="stylesheet" href="{{ asset('css/daiphuc.css') }}">
</head>
<body>

    {{-- ============ HEADER ============ --}}
    <header class="header">
        <div class="header-content">
            <a href="#" class="logo">
                <i class="fas fa-shield-heart"></i>
                <span style="font-size: 18px;">ĐẠI PHÚC</span>
            </a>

            <nav class="nav-menu">
                <a href="#home"   class="nav-link active">Trang chủ</a>
                <a href="#search" class="nav-link">Tra cứu</a>
                <a href="#contact" class="nav-link">Liên hệ</a>
            </nav>

            <div class="auth-buttons">
                @auth
                    <span class="welcome-user">
                        👋 Xin chào, <strong>{{ auth()->user()->name }}</strong>
                    </span>
                    <a href="{{ route('dashboard') }}" class="btn-solid">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn-outline">Đăng xuất</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-outline">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn-solid">Đăng ký</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- ============ HERO BANNER ============ --}}
    <section id="home" class="hero-banner">
        <div class="hero-bg"
             style="background-image: url('https://images.unsplash.com/photo-1660066522109-82af50d99488?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1400');">
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-badge">
                🌊 Đang hỗ trợ vùng lũ Yên Bái, Lào Cai, Hà Giang
            </div>
            <h1>
                ĐẠI PHÚC
                <span class="orange-text" style="font-size: 40px;">Kết nối yêu thương</span>
            </h1>
            <p>Hệ thống quản lý cứu trợ bão lũ minh bạch, kịp thời và chính xác.
               Mỗi sự giúp đỡ đều được ghi lại và xác minh.</p>
            <div class="hero-buttons">
                <button class="btn-primary">🚀 Bắt đầu ngay</button>
                <button class="btn-secondary">🔍 Tra cứu CCCD</button>
            </div>
        </div>
    </section>

    {{-- ============ STATS SECTION ============ --}}
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-icon">🏠</span>
                    <div class="stat-number">12.567</div>
                    <div class="stat-unit">hộ</div>
                    <div class="stat-label">Hộ dân đã nhận hỗ trợ</div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">🚚</span>
                    <div class="stat-number">1.234</div>
                    <div class="stat-unit">chuyến</div>
                    <div class="stat-label">Chuyến xe vận chuyển</div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">📦</span>
                    <div class="stat-number">487</div>
                    <div class="stat-unit">tấn</div>
                    <div class="stat-label">Tấn hàng hóa phân phát</div>
                </div>
                <div class="stat-card">
                    <span class="stat-icon">🤝</span>
                    <div class="stat-number">340+</div>
                    <div class="stat-unit">người</div>
                    <div class="stat-label">Tình nguyện viên tham gia</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ SEARCH SECTION ============ --}}
    <section id="search" class="search-section">
        <div class="search-content">
            <div class="search-header">
                <h2>🔍 Tra cứu tình trạng nhận hỗ trợ</h2>
                <p>Nhập số CCCD của bạn để kiểm tra thông tin hỗ trợ</p>
            </div>
            <div class="search-box">
                <input type="text"
                       id="cccdInput"
                       placeholder="Nhập số CCCD (12 số)"
                       maxlength="12"
                       class="search-input">
                <button class="btn-search" onclick="searchCCCD()">🔍 Tra cứu</button>
            </div>
            <div id="searchResult" class="search-result"></div>
        </div>
    </section>

    {{-- ============ IMAGE CARDS SECTION ============ --}}
    <section class="image-cards-section">
        <div class="container">
            <div class="image-cards-header">
                <h2>Hoạt động cứu trợ</h2>
            </div>
            <div class="image-cards-grid">
                <div class="image-card">
                    <img src="https://images.unsplash.com/photo-1741081288260-877057e3fa27?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=800"
                         alt="Cứu hộ khẩn cấp" loading="lazy">
                    <div class="image-card-overlay">
                        <div class="image-card-text">🚤 Cứu hộ khẩn cấp</div>
                    </div>
                </div>
                <div class="image-card">
                    <img src="https://images.unsplash.com/photo-1752010284872-76526682bfee?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=800"
                         alt="Hàng cứu trợ" loading="lazy">
                    <div class="image-card-overlay">
                        <div class="image-card-text">📦 Hàng hóa cứu trợ</div>
                    </div>
                </div>
                <div class="image-card">
                    <img src="https://images.unsplash.com/photo-1758003653085-4ebfbd7f76b4?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=800"
                         alt="Vùng hỗ trợ" loading="lazy">
                    <div class="image-card-overlay">
                        <div class="image-card-text">🗺️ Vùng hỗ trợ</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FEATURES SECTION ============ --}}
    <section class="features-section">
        <div class="container">
            <div class="features-header">
                <h2>Tính năng nổi bật</h2>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon-bg">🔍</div>
                    <h3>Tra cứu nhanh</h3>
                    <p>Tìm kiếm thông tin cứu trợ của bạn chỉ trong vài giây</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon-bg">🛡️</div>
                    <h3>Minh bạch 100%</h3>
                    <p>Toàn bộ thông tin cứu trợ công khai, dễ kiểm chứng</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon-bg">🚚</div>
                    <h3>Theo dõi thời gian thực</h3>
                    <p>Cập nhật liên tục tình trạng vận chuyển hàng cứu trợ</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon-bg">📊</div>
                    <h3>Báo cáo tự động</h3>
                    <p>Thống kê chi tiết và báo cáo công khai hàng ngày</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ ACTIVITIES SECTION ============ --}}
    <section class="activities-section">
        <div class="container">
            <div class="activities-header">
                <h2>Hoạt động gần đây (hôm nay)</h2>
            </div>
            <div class="activities-container">

                <div class="activity-item success">
                    <div class="activity-time">08:30</div>
                    <div class="activity-content">
                        <div class="activity-message">
                            <span class="activity-badge"></span>
                            Chuyến xe TX-089 giao thành công 120 gói quà
                        </div>
                        <div class="activity-location">📍 Xã Hòa Bình</div>
                    </div>
                </div>

                <div class="activity-item info">
                    <div class="activity-time">09:15</div>
                    <div class="activity-content">
                        <div class="activity-message">
                            <span class="activity-badge"></span>
                            Kho Văn Yên nhận thêm 2 tấn gạo
                        </div>
                        <div class="activity-location">📍 Huyện Văn Yên</div>
                    </div>
                </div>

                <div class="activity-item success">
                    <div class="activity-time">10:00</div>
                    <div class="activity-content">
                        <div class="activity-message">
                            <span class="activity-badge"></span>
                            85 hộ dân xã Tân Tiến đã xác nhận
                        </div>
                        <div class="activity-location">📍 Xã Tân Tiến</div>
                    </div>
                </div>

                <div class="activity-item warning">
                    <div class="activity-time">11:20</div>
                    <div class="activity-content">
                        <div class="activity-message">
                            <span class="activity-badge"></span>
                            Cảnh báo: Kho Trấn Yên sắp hết nước uống
                        </div>
                        <div class="activity-location">📍 Huyện Trấn Yên</div>
                    </div>
                </div>

                <div class="activity-item info">
                    <div class="activity-time">13:45</div>
                    <div class="activity-content">
                        <div class="activity-message">
                            <span class="activity-badge"></span>
                            Tình nguyện viên mở rộng vùng hỗ trợ
                        </div>
                        <div class="activity-location">📍 Xã Minh Quang</div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============ CTA SECTION ============ --}}
    <section class="cta-section">
        <div class="cta-content">
            <h2>Cùng nhau vượt qua bão lũ ☀️</h2>
            <p>Mỗi sự đóng góp, mỗi chuyến xe, mỗi gói hàng — tất cả đều được ghi lại,
               xác minh và công khai để cộng đồng tin tưởng.</p>
            <div class="cta-buttons">
                <button class="cta-btn cta-btn-primary">Tham gia ngay</button>
                <button class="cta-btn cta-btn-secondary">Hotline: 1900.636.838</button>
            </div>
        </div>
    </section>

    {{-- ============ FEEDBACK SECTION ============ --}}
    <section id="contact" class="feedback-section">
        <div class="container">
            <div class="feedback-header">
                <div class="feedback-badge">💬 Góp ý &amp; Phản ánh</div>
                <h2>Gửi phản ánh đến chúng tôi</h2>
                <p>Mọi ý kiến của bạn đều được tiếp nhận và xử lý trong vòng 24 giờ</p>
            </div>

            <div class="feedback-wrapper">
                {{-- Form gửi phản ánh --}}
                <div class="feedback-form-card">
                    <form id="feedbackForm" novalidate>
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="fb_name">
                                    <i class="fas fa-user"></i> Họ và tên <span class="required">*</span>
                                </label>
                                <input type="text" id="fb_name" name="name"
                                       class="form-control" placeholder="Nguyễn Văn A">
                                <span class="form-error" id="err_name"></span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="fb_phone">
                                    <i class="fas fa-phone"></i> Số điện thoại <span class="required">*</span>
                                </label>
                                <input type="tel" id="fb_phone" name="phone"
                                       class="form-control" placeholder="0912 345 678">
                                <span class="form-error" id="err_phone"></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="fb_cccd">
                                    <i class="fas fa-id-card"></i> Số CCCD
                                </label>
                                <input type="text" id="fb_cccd" name="cccd"
                                       class="form-control" placeholder="12 chữ số (không bắt buộc)"
                                       maxlength="12">
                                <span class="form-error" id="err_cccd"></span>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="fb_type">
                                    <i class="fas fa-tag"></i> Loại phản ánh <span class="required">*</span>
                                </label>
                                <select id="fb_type" name="type" class="form-control form-select">
                                    <option value="">-- Chọn loại phản ánh --</option>
                                    <option value="chua_nhan">Chưa nhận được hỗ trợ</option>
                                    <option value="sai_thong_tin">Thông tin hỗ trợ sai sót</option>
                                    <option value="khan_cap">Yêu cầu hỗ trợ khẩn cấp</option>
                                    <option value="de_xuat">Đề xuất cải tiến</option>
                                    <option value="khac">Khác</option>
                                </select>
                                <span class="form-error" id="err_type"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="fb_address">
                                <i class="fas fa-map-marker-alt"></i> Địa chỉ
                            </label>
                            <input type="text" id="fb_address" name="address"
                                   class="form-control" placeholder="Số nhà, thôn/xóm, xã, huyện...">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="fb_content">
                                <i class="fas fa-comment-dots"></i> Nội dung phản ánh <span class="required">*</span>
                            </label>
                            <textarea id="fb_content" name="content" rows="5"
                                      class="form-control form-textarea"
                                      placeholder="Mô tả chi tiết vấn đề bạn gặp phải hoặc đề xuất của bạn..."></textarea>
                            <div class="char-count"><span id="charCount">0</span>/500 ký tự</div>
                            <span class="form-error" id="err_content"></span>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-reset" onclick="resetFeedbackForm()">
                                <i class="fas fa-undo"></i> Nhập lại
                            </button>
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Gửi phản ánh
                            </button>
                        </div>
                    </form>

                    {{-- Toast thông báo --}}
                    <div id="feedbackToast" class="feedback-toast" style="display:none;"></div>
                </div>

                {{-- Bảng phản ánh đã gửi --}}
                <div class="feedback-table-card">
                    <div class="table-header">
                        <h3><i class="fas fa-list-alt"></i> Phản ánh đã gửi</h3>
                        <span class="table-badge" id="totalCount">0 phản ánh</span>
                    </div>
                    <div class="table-responsive">
                        <table class="feedback-table" id="feedbackTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Họ tên</th>
                                    <th>Loại phản ánh</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody id="feedbackTableBody">
                                <tr class="empty-row">
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <p>Chưa có phản ánh nào</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FOOTER ============ --}}
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-logo">
                        <i class="fas fa-shield-heart"></i>
                        <span>ĐẠI PHÚC</span>
                    </div>
                    <p>Hệ thống quản lý cứu trợ bão lũ minh bạch, kịp thời và chính xác</p>
                    <div class="social-links">
                        <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div class="footer-column">
                    <h4>Về Đại Phúc</h4>
                    <ul>
                        <li><a href="#">Giới thiệu</a></li>
                        <li><a href="#">Đội ngũ</a></li>
                        <li><a href="#">Đối tác</a></li>
                        <li><a href="#">Báo cáo</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4>Hỗ trợ</h4>
                    <ul>
                        <li><a href="#">Hướng dẫn sử dụng</a></li>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                        <li><a href="#">Liên hệ</a></li>
                        <li><a href="#">Bảo mật</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4>Liên hệ ngay</h4>
                    <div class="hotline">
                        <p>Hotline cứu trợ</p>
                        <a href="tel:1900636838" class="hotline-number">1900.636.838</a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>© 2026 ĐẠI PHÚC. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    {{-- JS trang chủ --}}
    <script src="{{ asset('js/daiphuc.js') }}"></script>
</body>
</html>

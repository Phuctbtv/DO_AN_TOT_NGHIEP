<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ĐẠI PHÚC – {{ config('app.name', 'Laravel') }}</title>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    {{-- Auth CSS --}}
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
<div class="auth-wrapper">

    {{-- ===== LEFT PANEL – Branding ===== --}}
    <div class="auth-panel-left">
        <div class="auth-brand">
            <span class="auth-brand-logo">
                <i class="fas fa-shield-heart"></i>
            </span>
            <div class="auth-brand-name">ĐẠI PHÚC</div>
            <div class="auth-brand-sub">Hệ thống quản lý cứu trợ bão lũ</div>
        </div>

        <div class="auth-features">
            <div class="auth-feature-item">
                <span class="auth-feature-icon">🔍</span>
                <div class="auth-feature-text">
                    <strong>Tra cứu nhanh</strong>
                    <span>Kiểm tra tình trạng hỗ trợ theo CCCD</span>
                </div>
            </div>
            <div class="auth-feature-item">
                <span class="auth-feature-icon">🛡️</span>
                <div class="auth-feature-text">
                    <strong>Minh bạch 100%</strong>
                    <span>Toàn bộ thông tin được công khai</span>
                </div>
            </div>
            <div class="auth-feature-item">
                <span class="auth-feature-icon">🚚</span>
                <div class="auth-feature-text">
                    <strong>Theo dõi thời gian thực</strong>
                    <span>Cập nhật liên tục tình trạng vận chuyển</span>
                </div>
            </div>
            <div class="auth-feature-item">
                <span class="auth-feature-icon">📊</span>
                <div class="auth-feature-text">
                    <strong>Báo cáo tự động</strong>
                    <span>Thống kê chi tiết hàng ngày</span>
                </div>
            </div>
        </div>

        <div class="auth-footer-note">© 2024 ĐẠI PHÚC. Tất cả quyền được bảo lưu.</div>
    </div>

    {{-- ===== RIGHT PANEL – Form ===== --}}
    <div class="auth-panel-right">
        <div class="auth-form-container">
            {{ $slot }}
        </div>
    </div>

</div>
</body>
</html>

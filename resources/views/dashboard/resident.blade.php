<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra cứu hỗ trợ – ĐẠI PHÚC</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        /* Override cho resident - nhẹ nhàng hơn */
        .resident-hero {
            background: linear-gradient(135deg, #0d9488 0%, #0a7e6f 100%);
            border-radius: 16px;
            padding: 36px;
            color: white;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .resident-hero::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
            right: -40px;
            top: -60px;
        }

        .resident-hero h2 {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .resident-hero p {
            font-size: 15px;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .support-status-card {
            background: white;
            border-radius: 14px;
            padding: 28px;
            border: 2px solid #ccfbf1;
            margin-bottom: 24px;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .support-status-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #ccfbf1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
        }

        .support-status-text h3 {
            font-size: 18px;
            font-weight: 700;
            color: #0d9488;
            margin-bottom: 4px;
        }

        .support-status-text p {
            font-size: 14px;
            color: #64748b;
        }
    </style>
</head>
<body>

{{-- TOP NAV --}}
<nav class="dash-topnav">
    <a href="/" class="dash-brand">
        <i class="fas fa-shield-heart"></i> ĐẠI PHÚC
    </a>
    <div class="dash-nav-right">
        <span class="dash-welcome">
            Xin chào, <strong>{{ auth()->user()->name }}</strong>
        </span>
        <span class="dash-role-badge badge-resident">
            <i class="fas fa-user"></i> Dân cư
        </span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dash-logout-btn">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </button>
        </form>
    </div>
</nav>

<div class="dash-layout">
    {{-- SIDEBAR --}}
    <aside class="dash-sidebar">
        <div class="sidebar-section-label">Cá nhân</div>
        <a href="{{ route('resident.dashboard') }}" class="sidebar-link active">
            <i class="fas fa-house"></i> Trang của tôi
        </a>

        <div class="sidebar-section-label">Hỗ trợ</div>
        <a href="#" class="sidebar-link">
            <i class="fas fa-search"></i> Tra cứu CCCD
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-gift"></i> Lịch sử nhận hỗ trợ
        </a>

        <div class="sidebar-section-label">Phản ánh</div>
        <a href="/#contact" class="sidebar-link">
            <i class="fas fa-comment-dots"></i> Gửi phản ánh
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-clock-rotate-left"></i> Phản ánh của tôi
        </a>

        <div class="sidebar-section-label">Tài khoản</div>
        <a href="{{ route('profile.edit') }}" class="sidebar-link">
            <i class="fas fa-user-cog"></i> Hồ sơ cá nhân
        </a>
    </aside>

    {{-- MAIN --}}
    <main class="dash-main">
        {{-- HERO --}}
        <div class="resident-hero">
            <h2>👋 Chào mừng, {{ auth()->user()->name }}!</h2>
            <p>Theo dõi tình trạng hỗ trợ cứu trợ bão lũ của gia đình bạn</p>
            @if(auth()->user()->identity_card)
                <span style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.15);padding:8px 16px;border-radius:50px;font-size:13px;font-weight:600">
                    <i class="fas fa-id-card"></i>
                    CCCD: {{ auth()->user()->identity_card }}
                </span>
            @else
                <a href="{{ route('profile.edit') }}" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.2);padding:8px 16px;border-radius:50px;font-size:13px;font-weight:600;color:white;text-decoration:none">
                    <i class="fas fa-plus"></i> Cập nhật số CCCD
                </a>
            @endif
        </div>

        {{-- SUPPORT STATUS --}}
        <div class="support-status-card">
            <div class="support-status-icon">🎁</div>
            <div class="support-status-text">
                <h3>Tình trạng: Đã nhận hỗ trợ đợt 1</h3>
                <p>Ngày cập nhật: {{ date('d/m/Y') }} • 5 gói quà • Xã Hòa Bình</p>
            </div>
            <span class="badge badge-success" style="margin-left:auto;white-space:nowrap">✅ Xác nhận</span>
        </div>

        {{-- STATS --}}
        <div class="dash-stats" style="grid-template-columns:repeat(3,1fr)">
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-green"><i class="fas fa-gift" style="color:#22c55e"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">5</div>
                    <div class="dash-stat-label">Gói hỗ trợ đã nhận</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-teal"><i class="fas fa-comment-dots" style="color:#0d9488"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">1</div>
                    <div class="dash-stat-label">Phản ánh đã gửi</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-orange"><i class="fas fa-bell" style="color:#f97316"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">2</div>
                    <div class="dash-stat-label">Thông báo mới</div>
                </div>
            </div>
        </div>

        {{-- HISTORY + NOTIFICATIONS --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">

            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title"><i class="fas fa-gift"></i> Lịch sử nhận hỗ trợ</div>
                </div>
                <div class="dash-card-body" style="padding:0">
                    <table class="dash-table">
                        <thead>
                            <tr><th>Đợt</th><th>Hàng hóa</th><th>SL</th><th>Ngày</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Đợt 1</td>
                                <td>Gạo + Nước</td>
                                <td>5 gói</td>
                                <td>15/09</td>
                            </tr>
                            <tr>
                                <td>Đợt 2</td>
                                <td>Mì tôm</td>
                                <td>3 gói</td>
                                <td>20/09</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title"><i class="fas fa-bell"></i> Thông báo mới nhất</div>
                </div>
                <div class="dash-card-body">
                    <div class="activity-list">
                        <div class="activity-row">
                            <div class="activity-dot dot-green"></div>
                            <div>
                                <div class="activity-text">Đợt hỗ trợ thứ 3 sắp đến</div>
                                <div class="activity-meta">Dự kiến ngày 25/09</div>
                            </div>
                        </div>
                        <div class="activity-row">
                            <div class="activity-dot dot-blue"></div>
                            <div>
                                <div class="activity-text">Phản ánh của bạn đã được tiếp nhận</div>
                                <div class="activity-meta">2 giờ trước</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>

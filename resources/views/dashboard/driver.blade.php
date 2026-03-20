<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài xế Dashboard – ĐẠI PHÚC</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
        <span class="dash-role-badge badge-driver">
            <i class="fas fa-truck"></i> Tài xế
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
        <div class="sidebar-section-label">Tổng quan</div>
        <a href="{{ route('driver.dashboard') }}" class="sidebar-link active">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>

        <div class="sidebar-section-label">Chuyến xe</div>
        <a href="#" class="sidebar-link">
            <i class="fas fa-truck-moving"></i> Chuyến hôm nay
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-route"></i> Lộ trình
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-clock-rotate-left"></i> Lịch sử chuyến
        </a>

        <div class="sidebar-section-label">Giao hàng</div>
        <a href="#" class="sidebar-link">
            <i class="fas fa-hand-holding-heart"></i> Danh sách giao
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-check-circle"></i> Xác nhận giao
        </a>

        <div class="sidebar-section-label">Tài khoản</div>
        <a href="{{ route('profile.edit') }}" class="sidebar-link">
            <i class="fas fa-user-cog"></i> Hồ sơ
        </a>
    </aside>

    {{-- MAIN --}}
    <main class="dash-main">
        <div class="dash-page-header">
            <h1>🚚 Dashboard Tài xế</h1>
            <p>Theo dõi chuyến xe và quản lý giao hàng cứu trợ</p>
        </div>

        {{-- STATS --}}
        <div class="dash-stats">
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-orange"><i class="fas fa-truck-moving" style="color:#f97316"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">2</div>
                    <div class="dash-stat-label">Chuyến hôm nay</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-green"><i class="fas fa-circle-check" style="color:#22c55e"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">1</div>
                    <div class="dash-stat-label">Chuyến hoàn thành</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-blue"><i class="fas fa-boxes-stacked" style="color:#3b82f6"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">240</div>
                    <div class="dash-stat-label">Gói hàng đã giao</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-teal"><i class="fas fa-road" style="color:#0d9488"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">87km</div>
                    <div class="dash-stat-label">Quãng đường hôm nay</div>
                </div>
            </div>
        </div>

        {{-- TRIP DETAIL + DELIVERY LIST --}}
        <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:24px">

            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title"><i class="fas fa-truck-moving"></i> Chuyến hiện tại</div>
                    <span class="badge badge-info">Đang chạy</span>
                </div>
                <div class="dash-card-body">
                    <div class="activity-list">
                        <div class="activity-row">
                            <div class="activity-dot dot-orange"></div>
                            <div>
                                <div class="activity-text">Mã chuyến: TX-091</div>
                                <div class="activity-meta">Bắt đầu: 13:00</div>
                            </div>
                        </div>
                        <div class="activity-row">
                            <div class="activity-dot dot-blue"></div>
                            <div>
                                <div class="activity-text">Xuất phát: Kho Văn Yên</div>
                                <div class="activity-meta">120 gói hàng</div>
                            </div>
                        </div>
                        <div class="activity-row">
                            <div class="activity-dot dot-green"></div>
                            <div>
                                <div class="activity-text">Điểm đến: Xã Hòa Bình</div>
                                <div class="activity-meta">Còn ~18km</div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:16px">
                        <a href="#" class="dash-btn dash-btn-primary" style="width:100%;justify-content:center">
                            <i class="fas fa-check"></i> Xác nhận giao xong
                        </a>
                    </div>
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title"><i class="fas fa-list-check"></i> Danh sách giao hôm nay</div>
                </div>
                <div class="dash-card-body" style="padding:0">
                    <table class="dash-table">
                        <thead>
                            <tr><th>Hộ dân</th><th>Địa chỉ</th><th>Hàng hóa</th><th>TT</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nguyễn Văn A</td>
                                <td>Xóm 3, Hòa Bình</td>
                                <td>2 gói</td>
                                <td><span class="badge badge-success">Đã giao</span></td>
                            </tr>
                            <tr>
                                <td>Trần Thị B</td>
                                <td>Thôn 5, Hòa Bình</td>
                                <td>1 gói</td>
                                <td><span class="badge badge-warning">Chờ giao</span></td>
                            </tr>
                            <tr>
                                <td>Lê Văn C</td>
                                <td>Xóm 1, Tân Tiến</td>
                                <td>3 gói</td>
                                <td><span class="badge badge-warning">Chờ giao</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>

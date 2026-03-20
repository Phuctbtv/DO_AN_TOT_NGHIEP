<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thủ kho Dashboard – ĐẠI PHÚC</title>
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
        <span class="dash-role-badge badge-warehouse">
            <i class="fas fa-warehouse"></i> Thủ kho
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
        <a href="{{ route('warehouse.dashboard') }}" class="sidebar-link active">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>

        <div class="sidebar-section-label">Kho hàng</div>
        <a href="#" class="sidebar-link">
            <i class="fas fa-warehouse"></i> Kho của tôi
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-boxes-stacked"></i> Tồn kho
        </a>

        <div class="sidebar-section-label">Giao dịch</div>
        <a href="#" class="sidebar-link">
            <i class="fas fa-arrow-down-to-bracket"></i> Nhập kho
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-arrow-up-from-bracket"></i> Xuất kho
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-clock-rotate-left"></i> Lịch sử giao dịch
        </a>

        <div class="sidebar-section-label">Vận chuyển</div>
        <a href="#" class="sidebar-link">
            <i class="fas fa-truck"></i> Theo dõi chuyến xe
        </a>

        <div class="sidebar-section-label">Tài khoản</div>
        <a href="{{ route('profile.edit') }}" class="sidebar-link">
            <i class="fas fa-user-cog"></i> Hồ sơ
        </a>
    </aside>

    {{-- MAIN --}}
    <main class="dash-main">
        <div class="dash-page-header">
            <h1>🏭 Dashboard Thủ kho</h1>
            <p>Quản lý kho hàng và theo dõi xuất nhập hàng hóa</p>
        </div>

        {{-- STATS --}}
        <div class="dash-stats">
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-teal"><i class="fas fa-boxes-stacked" style="color:#0d9488"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">2.480</div>
                    <div class="dash-stat-label">Tổng hàng tồn kho (kg)</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-green"><i class="fas fa-arrow-down-to-bracket" style="color:#22c55e"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">34</div>
                    <div class="dash-stat-label">Phiếu nhập hôm nay</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-orange"><i class="fas fa-arrow-up-from-bracket" style="color:#f97316"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">28</div>
                    <div class="dash-stat-label">Phiếu xuất hôm nay</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-red"><i class="fas fa-triangle-exclamation" style="color:#ef4444"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">3</div>
                    <div class="dash-stat-label">Mặt hàng sắp hết</div>
                </div>
            </div>
        </div>

        {{-- TABLES --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">

            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title"><i class="fas fa-arrow-down-to-bracket"></i> Nhập kho gần đây</div>
                    <a href="#" class="dash-btn dash-btn-primary" style="font-size:12px;padding:7px 14px">
                        <i class="fas fa-plus"></i> Nhập mới
                    </a>
                </div>
                <div class="dash-card-body" style="padding:0">
                    <table class="dash-table">
                        <thead>
                            <tr><th>Hàng hóa</th><th>SL</th><th>Thời gian</th><th>TT</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Gạo 5kg</td>
                                <td>200 thùng</td>
                                <td>08:30</td>
                                <td><span class="badge badge-success">Đã nhập</span></td>
                            </tr>
                            <tr>
                                <td>Nước uống</td>
                                <td>500 chai</td>
                                <td>09:15</td>
                                <td><span class="badge badge-success">Đã nhập</span></td>
                            </tr>
                            <tr>
                                <td>Mì tôm</td>
                                <td>150 thùng</td>
                                <td>10:00</td>
                                <td><span class="badge badge-warning">Chờ xác nhận</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title"><i class="fas fa-arrow-up-from-bracket"></i> Xuất kho gần đây</div>
                    <a href="#" class="dash-btn dash-btn-outline" style="font-size:12px;padding:7px 14px">
                        <i class="fas fa-list"></i> Xem tất cả
                    </a>
                </div>
                <div class="dash-card-body" style="padding:0">
                    <table class="dash-table">
                        <thead>
                            <tr><th>Chuyến xe</th><th>Hàng hóa</th><th>SL</th><th>TT</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>TX-089</td>
                                <td>Gạo + Nước</td>
                                <td>120 gói</td>
                                <td><span class="badge badge-success">Đã giao</span></td>
                            </tr>
                            <tr>
                                <td>TX-090</td>
                                <td>Mì tôm</td>
                                <td>80 thùng</td>
                                <td><span class="badge badge-info">Đang đi</span></td>
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

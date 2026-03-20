<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – ĐẠI PHÚC</title>
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
        <span class="dash-role-badge badge-admin">
            <i class="fas fa-crown"></i> Quản trị viên
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
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link active">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>

        <div class="sidebar-section-label">Quản lý người dùng</div>
        <a href="#" class="sidebar-link">
            <i class="fas fa-users"></i> Tài khoản
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-user-shield"></i> Phân quyền
        </a>

        <div class="sidebar-section-label">Kho & Vật tư</div>
        <a href="#" class="sidebar-link">
            <i class="fas fa-warehouse"></i> Kho hàng
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-boxes-stacked"></i> Vật tư
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-arrow-down-to-bracket"></i> Nhập kho
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-arrow-up-from-bracket"></i> Xuất kho
        </a>

        <div class="sidebar-section-label">Vận chuyển</div>
        <a href="#" class="sidebar-link">
            <i class="fas fa-truck"></i> Chuyến xe
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-route"></i> Lộ trình
        </a>

        <div class="sidebar-section-label">Cứu trợ</div>
        <a href="#" class="sidebar-link">
            <i class="fas fa-house-chimney-user"></i> Hộ dân
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-hand-holding-heart"></i> Phân phát
        </a>
        <a href="#" class="sidebar-link">
            <i class="fas fa-comment-dots"></i> Phản ánh
        </a>

        <div class="sidebar-section-label">Hệ thống</div>
        <a href="#" class="sidebar-link">
            <i class="fas fa-chart-bar"></i> Báo cáo
        </a>
        <a href="{{ route('profile.edit') }}" class="sidebar-link">
            <i class="fas fa-user-cog"></i> Hồ sơ
        </a>
    </aside>

    {{-- MAIN --}}
    <main class="dash-main">
        <div class="dash-page-header">
            <h1>🛡️ Bảng điều khiển Admin</h1>
            <p>Quản lý toàn bộ hệ thống cứu trợ bão lũ ĐẠI PHÚC</p>
        </div>

        {{-- STATS --}}
        <div class="dash-stats">
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-blue"><i class="fas fa-users" style="color:#3b82f6"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">128</div>
                    <div class="dash-stat-label">Tài khoản hệ thống</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-teal"><i class="fas fa-house-chimney-user" style="color:#0d9488"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">12.567</div>
                    <div class="dash-stat-label">Hộ dân đã hỗ trợ</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-orange"><i class="fas fa-truck" style="color:#f97316"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">1.234</div>
                    <div class="dash-stat-label">Chuyến xe hoàn thành</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-green"><i class="fas fa-boxes-stacked" style="color:#22c55e"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">487t</div>
                    <div class="dash-stat-label">Hàng hóa phân phát</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-red"><i class="fas fa-comment-dots" style="color:#ef4444"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">23</div>
                    <div class="dash-stat-label">Phản ánh chờ xử lý</div>
                </div>
            </div>
            <div class="dash-stat-card">
                <div class="dash-stat-icon icon-purple"><i class="fas fa-warehouse" style="color:#8b5cf6"></i></div>
                <div class="dash-stat-info">
                    <div class="dash-stat-value">8</div>
                    <div class="dash-stat-label">Kho hàng hoạt động</div>
                </div>
            </div>
        </div>

        {{-- ACTIVITY + ACCOUNTS --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">

            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title"><i class="fas fa-bolt"></i> Hoạt động gần đây</div>
                </div>
                <div class="dash-card-body">
                    <div class="activity-list">
                        <div class="activity-row">
                            <div class="activity-dot dot-green"></div>
                            <div>
                                <div class="activity-text">Kho Yên Bái nhập 2 tấn gạo</div>
                                <div class="activity-meta">5 phút trước • Thủ kho Minh</div>
                            </div>
                        </div>
                        <div class="activity-row">
                            <div class="activity-dot dot-blue"></div>
                            <div>
                                <div class="activity-text">Chuyến TX-099 xuất phát</div>
                                <div class="activity-meta">20 phút trước • Tài xế Hùng</div>
                            </div>
                        </div>
                        <div class="activity-row">
                            <div class="activity-dot dot-orange"></div>
                            <div>
                                <div class="activity-text">85 hộ dân xác nhận nhận hàng</div>
                                <div class="activity-meta">1 giờ trước • Xã Tân Tiến</div>
                            </div>
                        </div>
                        <div class="activity-row">
                            <div class="activity-dot dot-red"></div>
                            <div>
                                <div class="activity-text">Cảnh báo hết nước uống kho Trấn Yên</div>
                                <div class="activity-meta">2 giờ trước • Hệ thống</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title"><i class="fas fa-users"></i> Tài khoản mới nhất</div>
                    <a href="#" class="dash-btn dash-btn-primary" style="font-size:12px;padding:7px 14px">
                        <i class="fas fa-plus"></i> Thêm
                    </a>
                </div>
                <div class="dash-card-body" style="padding:0">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Họ tên</th>
                                <th>Role</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nguyễn Văn A</td>
                                <td><span class="badge badge-info">Thủ kho</span></td>
                                <td><span class="badge badge-success">Hoạt động</span></td>
                            </tr>
                            <tr>
                                <td>Trần Thị B</td>
                                <td><span class="badge badge-warning">Tài xế</span></td>
                                <td><span class="badge badge-success">Hoạt động</span></td>
                            </tr>
                            <tr>
                                <td>Lê Văn C</td>
                                <td><span class="badge badge-gray">Dân cư</span></td>
                                <td><span class="badge badge-success">Hoạt động</span></td>
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

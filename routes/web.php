<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\WarehouseDashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ResidentDashboardController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GpsMonitorController;
use Illuminate\Support\Facades\Route;

// ============ PUBLIC ============
Route::get('/', [WelcomeController::class, 'index'])->name('home');

// API công khai (không cần đăng nhập)
Route::get('/api/activity-feed',  [WelcomeController::class, 'activityFeed'])->name('api.activity-feed');
Route::post('/api/cccd-lookup',   [WelcomeController::class, 'lookupCccd'])->name('api.cccd-lookup');
Route::post('/api/public-feedback', [WelcomeController::class, 'submitFeedback'])->name('api.public-feedback');

// Đăng ký cứu trợ (public, không cần đăng nhập)
Route::post('/register-household', [HouseholdController::class, 'publicRegister'])
    ->name('household.register');

// ============ DASHBOARD (redirect theo role) ============
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ============ ADMIN ============
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Supplies (nhu yếu phẩm)
    Route::resource('supplies', SupplyController::class);

    // Warehouses (kho hàng)
    Route::resource('warehouses', WarehouseController::class);

    // Users (quản lý tài khoản)
    Route::resource('users', UserController::class);

    // Drivers - alias vào users/index lọc sẵn role=driver
    Route::get('/drivers', function () {
        return redirect()->route('admin.users.index', ['role' => 'driver']);
    })->name('drivers.index');

    // Households (hộ dân – đăng ký cứu trợ)
    Route::get('/households', [HouseholdController::class, 'index'])->name('households.index');
    Route::get('/households/pending', [HouseholdController::class, 'pending'])->name('households.pending');
    Route::get('/households/{household}', [HouseholdController::class, 'show'])->name('households.show');
    Route::post('/households/{household}/approve', [HouseholdController::class, 'approve'])->name('households.approve');
    Route::post('/households/{household}/reject', [HouseholdController::class, 'reject'])->name('households.reject');

    // Trips (quản lý chuyến xe)
    // QUAN TRỌNG: Route cụ thể phải khai báo TRƯỚC resource
    Route::get('/trips/stock/{warehouseId}', [TripController::class, 'stockByWarehouse'])->name('trips.stock');
    Route::post('/trips/{trip}/status', [TripController::class, 'updateStatus'])->name('trips.updateStatus');
    // API: Lấy danh sách trip đang active để admin subscribe WebSocket
    Route::get('/trips/active-ids', [TripController::class, 'activeIds'])->name('trips.activeIds');
    Route::resource('trips', TripController::class)->except(['edit', 'update']);

    // Feedbacks (phản hồi từ người dân)
    Route::get('/feedbacks',             [FeedbackController::class, 'index'])  ->name('feedbacks.index');
    Route::patch('/feedbacks/{feedback}',[FeedbackController::class, 'update']) ->name('feedbacks.update');

    // Báo cáo (Xuất Excel / PDF)
    Route::get('/reports/households/export',  [ReportController::class, 'exportHouseholds'])->name('reports.households.export');
    Route::get('/reports/trips/export',       [ReportController::class, 'exportTrips'])     ->name('reports.trips.export');
    Route::get('/reports/trips/{trip}/pdf',   [ReportController::class, 'tripPdf'])         ->name('reports.trips.pdf');

    // GPS Monitor
    Route::get('/gps',                [GpsMonitorController::class, 'index'])         ->name('gps.index');
    Route::get('/gps/live-positions', [GpsMonitorController::class, 'livePositions'])->name('gps.live');
});

// ============ WAREHOUSE MANAGER ============
Route::middleware(['auth', 'warehouse'])->prefix('warehouse')->name('warehouse.')->group(function () {
    Route::get('/dashboard', fn() => redirect()->route('warehouse.overview'))->name('dashboard');

    // Nhập kho (Stock In)
    Route::resource('stock_ins', StockInController::class)->except(['edit', 'update']);

    // Xuất kho (Stock Out)
    Route::get('/stock_outs',                 [StockOutController::class,  'index'])  ->name('stock_outs.index');
    Route::get('/stock_outs/{trip}',          [StockOutController::class,  'show'])   ->name('stock_outs.show');
    Route::post('/stock_outs/{trip}/confirm', [StockOutController::class,  'confirm'])->name('stock_outs.confirm');

    // Tồn kho hiện tại (Inventory)
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

    // Tổng quan dashboard (dữ liệu thực)
    Route::get('/overview',    [WarehouseDashboardController::class, 'overview'])   ->name('overview');

    // Thống kê biểu đồ
    Route::get('/statistics',  [WarehouseDashboardController::class, 'statistics']) ->name('statistics');

    // Cảnh báo tồn kho
    Route::get('/alerts',      [WarehouseDashboardController::class, 'alerts'])           ->name('alerts');
    Route::post('/alerts/request', [WarehouseDashboardController::class, 'sendStockRequest'])->name('alerts.request');

    // Báo cáo nhập xuất kho (Excel)
    Route::get('/reports/stock-export', [WarehouseDashboardController::class, 'exportStockReport'])->name('reports.stock.export');
});

// ============ DRIVER ============
Route::middleware(['auth', 'driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DeliveryController::class, 'driverDashboard'])->name('dashboard');

    // Lấy thông tin delivery (AJAX)
    Route::get('/deliveries/{delivery}/info', [DeliveryController::class, 'getDeliveryInfo'])->name('deliveries.info');

    // Xác nhận giao hàng (GPS + Cloudinary)
    Route::post('/deliveries/{delivery}/confirm', [DeliveryController::class, 'confirm'])->name('deliveries.confirm');

    // Tra cứu delivery bằng QR code
    Route::post('/deliveries/qr-lookup', [DeliveryController::class, 'qrLookup'])->name('deliveries.qrLookup');

    // Tra cứu delivery bằng CCCD (nhập tay)
    Route::post('/deliveries/cccd-lookup', [DeliveryController::class, 'cccdLookup'])->name('deliveries.cccdLookup');

    // API: Lấy stats realtime (WebSocket)
    Route::get('/trips/{trip}/stats', [DeliveryController::class, 'tripStats'])->name('trips.stats');

    // API: Lấy danh sách deliveries realtime (WebSocket)
    Route::get('/trips/{trip}/deliveries', [DeliveryController::class, 'tripDeliveries'])->name('trips.deliveries');

    // Bắt đầu giao hàng: driver tự chuyển exporting → shipping
    Route::post('/trips/{trip}/start', [DeliveryController::class, 'startTrip'])->name('trips.start');
});

// ============ RESIDENT ============
Route::middleware(['auth'])->prefix('resident')->name('resident.')->group(function () {
    Route::get('/dashboard',   [ResidentDashboardController::class, 'index'])          ->name('dashboard');
    Route::patch('/info',      [ResidentDashboardController::class, 'updateInfo'])      ->name('update-info');
    Route::post('/feedback',   [ResidentDashboardController::class, 'submitFeedback'])  ->name('feedback');
    Route::get('/qr/download', [ResidentDashboardController::class, 'downloadQr'])      ->name('qr.download');
});

// ============ PROFILE (Breeze) ============
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

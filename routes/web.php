<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\StockInController;
use Illuminate\Support\Facades\Route;

// ============ PUBLIC ============
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Đăng ký cứu trợ (public, không cần đăng nhập)
Route::post('/register-household', [HouseholdController::class, 'publicRegister'])
    ->name('household.register');

// ============ DASHBOARD (redirect theo role) ============
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ============ ADMIN ============
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.admin');
    })->name('dashboard');

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
    Route::resource('trips', TripController::class)->except(['edit', 'update']);
});

// ============ WAREHOUSE MANAGER ============
Route::middleware(['auth', 'warehouse'])->prefix('warehouse')->name('warehouse.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.warehouse');
    })->name('dashboard');

    // Nhập kho (Stock In)
    Route::resource('stock_ins', StockInController::class)->except(['edit', 'update']);
});

// ============ DRIVER ============
Route::middleware(['auth', 'driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.driver');
    })->name('dashboard');
});

// ============ RESIDENT ============
Route::middleware(['auth'])->prefix('resident')->name('resident.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.resident');
    })->name('dashboard');
});

// ============ PROFILE (Breeze) ============
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

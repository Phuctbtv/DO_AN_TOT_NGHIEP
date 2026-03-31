<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ============ PUBLIC ============
Route::get('/', function () {
    return view('welcome');
})->name('home');

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
});

// ============ WAREHOUSE MANAGER ============
Route::middleware(['auth', 'warehouse'])->prefix('warehouse')->name('warehouse.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.warehouse');
    })->name('dashboard');
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

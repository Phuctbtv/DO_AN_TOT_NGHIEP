<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Supply;
use App\Models\Warehouse;
use App\Models\Household;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo users
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@relief.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0123456789',
            'identity_card' => '001200012345'
        ]);

        $warehouseManager = User::create([
            'name' => 'Phạm Đại Phúc',
            'email' => 'phamdaiphuc20003@gmail.com',
            'password' => Hash::make('Phuc02032003@'),
            'role' => 'warehouse_manager',
            'phone' => '0123456792',
            'identity_card' => '001200012348'
        ]);

        $driver = User::create([
            'name' => 'Tài xế A',
            'email' => 'driver@relief.com',
            'password' => Hash::make('password'),
            'role' => 'driver',
            'phone' => '0123456790',
            'identity_card' => '001200012346'
        ]);

        $resident = User::create([
            'name' => 'Nguyễn Văn Dân',
            'email' => 'resident@relief.com',
            'password' => Hash::make('password'),
            'role' => 'resident',
            'phone' => '0123456791',
            'identity_card' => '001200012347',
            'address' => 'Thôn A, Xã B, Huyện C',
            'latitude' => 21.0285,
            'longitude' => 105.8542
        ]);

        // Tạo categories
        $category = Category::create(['name' => 'Thực phẩm', 'priority' => 1]);
        Category::create(['name' => 'Y tế', 'priority' => 1]);
        Category::create(['name' => 'Đồ gia dụng', 'priority' => 3]);

        // Tạo supplies
        Supply::create([
            'category_id' => $category->id,
            'name' => 'Gạo',
            'unit' => 'kg',
            'min_stock_alert' => 100
        ]);

        // Tạo warehouse
        $warehouse = Warehouse::create([
            'name' => 'Kho trung tâm',
            'address' => 'Số 1, Đường ABC, Quận 1',
            'lat' => 21.0285,
            'lng' => 105.8542,
            'manager_id' => $warehouseManager->id
        ]);

        // Tạo household
        Household::create([
            'resident_id' => $resident->id,
            'household_name' => 'Hộ ông Nguyễn Văn Dân',
            'address' => $resident->address,
            'lat' => $resident->latitude,
            'lng' => $resident->longitude,
            'qr_code' => 'QR-' . $resident->identity_card,
            'priority_level' => 1,
            'status' => 'active'
        ]);
    }
}
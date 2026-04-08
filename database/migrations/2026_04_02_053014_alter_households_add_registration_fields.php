<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {
            // Đổi qr_code thành nullable (chưa có khi mới đăng ký)
            $table->string('qr_code')->nullable()->change();

            // Đổi default status thành 'pending' (chờ duyệt)
            $table->string('status')->default('pending')->change();

            // Thêm các cột mới cho quy trình đăng ký
            $table->string('scene_image')->nullable()->after('lng');          // URL ảnh Cloudinary
            $table->integer('member_count')->default(1)->after('scene_image'); // Số người trong hộ
            $table->text('rejection_reason')->nullable()->after('member_count'); // Lý do từ chối
            $table->string('phone', 20)->nullable()->after('rejection_reason'); // SĐT hộ dân
        });
    }

    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropColumn(['scene_image', 'member_count', 'rejection_reason', 'phone']);
            $table->string('qr_code')->unique()->nullable(false)->change();
            $table->string('status')->default('active')->change();
        });
    }
};

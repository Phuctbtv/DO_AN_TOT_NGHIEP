<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->foreignId('supply_id')->constrained()->onDelete('restrict');
            $table->integer('quantity_loaded');
            $table->integer('quantity_delivered')->default(0);
            $table->timestamps();
            
            // Mỗi chuyến chỉ có 1 loại hàng duy nhất (tránh trùng lặp)
            $table->unique(['trip_id', 'supply_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_details');
    }
};
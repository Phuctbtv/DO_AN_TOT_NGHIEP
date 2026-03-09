<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('trip_code')->unique();
            $table->foreignId('driver_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('warehouse_id')->constrained()->onDelete('restrict');
            $table->string('vehicle_info');
            $table->string('status')->default('preparing'); // preparing, shipping, completed, cancelled
            
            // Weather data
            $table->float('weather_temp')->nullable();
            $table->string('weather_desc')->nullable();
            $table->boolean('weather_alert')->default(false);
            
            $table->text('notes')->nullable();
            $table->timestamp('exported_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'created_at']);
            $table->index('driver_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_code')->unique();
            $table->foreignId('trip_id')->constrained()->onDelete('restrict');
            $table->foreignId('household_id')->constrained()->onDelete('restrict');
            
            // Cloudinary
            $table->string('proof_image_url', 500)->nullable();
            
            // GPS data
            $table->decimal('actual_lat', 10, 8)->nullable();
            $table->decimal('actual_lng', 11, 8)->nullable();
            $table->float('distance_deviation')->nullable();
            
            // Recipient info
            $table->string('recipient_name');
            $table->string('recipient_cccd', 20);
            
            $table->string('status')->default('pending'); // success, warning, failed
            $table->text('notes')->nullable();
            $table->string('sync_status')->default('pending'); // pending, synced
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            
            $table->index(['trip_id', 'status']);
            $table->index('household_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
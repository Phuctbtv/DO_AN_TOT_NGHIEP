<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->onDelete('restrict');
            $table->foreignId('supply_id')->constrained()->onDelete('restrict');
            $table->integer('quantity');
            $table->string('donor_info')->nullable();
            $table->timestamp('received_date');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            
            // Index để tìm kiếm
            $table->index(['warehouse_id', 'received_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ins');
    }
};
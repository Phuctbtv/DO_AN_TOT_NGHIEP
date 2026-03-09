<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('restrict');
            $table->foreignId('supply_id')->constrained()->onDelete('restrict');
            $table->integer('quantity');
            $table->timestamp('exported_date');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
            
            $table->index(['warehouse_id', 'exported_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_outs');
    }
};
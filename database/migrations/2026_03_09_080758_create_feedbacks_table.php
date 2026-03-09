<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('identity_card', 20);
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('type'); // complaint, report, suggestion
            $table->text('content');
            $table->string('image_url', 500)->nullable();
            $table->string('status')->default('pending'); // pending, processing, resolved
            $table->text('admin_note')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('identity_card');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
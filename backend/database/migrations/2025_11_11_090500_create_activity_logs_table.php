<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable()->index();
                $table->string('user_agent', 1024)->nullable();
                $table->string('method', 10)->nullable();
                // Limit indexed path length to fit MySQL utf8mb4 index size constraints
                $table->string('path', 512)->nullable()->index();
                $table->string('route_name', 255)->nullable()->index();
                $table->json('query')->nullable();
                $table->json('body')->nullable();
                $table->unsignedSmallInteger('status_code')->nullable()->index();
                $table->unsignedInteger('duration_ms')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};



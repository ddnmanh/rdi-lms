<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefreshTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('token', 500)->nullable()->unique();
            $table->dateTime('expires_at')->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_revoked')->nullable()->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_revoked'], 'idx_rt_user');
            $table->index('token', 'idx_rt_token');
            $table->index('expires_at', 'idx_rt_expires');
            // Không tạo ràng buộc khóa ngoại ở DB

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_0900_ai_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refresh_tokens');
    }
}

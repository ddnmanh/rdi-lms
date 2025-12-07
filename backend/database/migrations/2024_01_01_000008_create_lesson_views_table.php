<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->unsignedInteger('watched_duration')->nullable()->default(0);
            $table->unsignedInteger('last_position')->nullable()->default(0)->comment('giây để resume');
            $table->unsignedInteger('completion_percentage')->nullable()->default(0)->comment('phần trăm hoàn thành');
            $table->timestamp('last_watched_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'lesson_id'], 'uq_lv');
            $table->index('user_id', 'idx_lv_user');
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
        Schema::dropIfExists('lesson_views');
    }
}


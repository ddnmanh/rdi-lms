<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('title', 255)->nullable()->default('Bài học');
            $table->string('description', 255)->nullable();
            $table->unsignedInteger('duration')->nullable()->comment('Giây');
            $table->text('video_url')->nullable();
            $table->unsignedSmallInteger('display_order')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['course_id', 'display_order'], 'idx_lessons_course_order');
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
        Schema::dropIfExists('lessons');
    }
}


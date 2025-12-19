<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('score', 5, 2)->default(0); // Điểm đạt được (%)
            $table->unsignedInteger('points_earned')->default(0); // Tổng điểm đạt được
            $table->unsignedInteger('max_points')->default(0); // Điểm tối đa có thể đạt
            $table->boolean('passed')->default(false); // Đã pass chưa
            $table->timestamp('started_at')->nullable(); // Thời gian bắt đầu làm bài
            $table->timestamp('completed_at')->nullable(); // Thời gian hoàn thành
            $table->timestamps();

            $table->index('quiz_id');
            $table->index('user_id');
            $table->index('passed');
            $table->index(['quiz_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_quiz_attempts');
    }
};


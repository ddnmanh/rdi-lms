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
        Schema::create('lesson_quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attempt_id');
            $table->unsignedBigInteger('question_id');
            $table->json('selected_option_ids')->nullable(); // JSON array của option_ids đã chọn
            $table->boolean('is_correct')->default(false); // Câu trả lời đúng hay sai
            $table->unsignedInteger('score_earned')->default(0); // Điểm đạt được cho câu này
            $table->timestamps();

            $table->index('attempt_id');
            $table->index('question_id');
            $table->index(['attempt_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_quiz_attempt_answers');
    }
};


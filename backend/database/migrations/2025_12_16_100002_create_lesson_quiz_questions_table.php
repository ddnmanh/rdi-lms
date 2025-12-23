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
        Schema::create('lesson_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->text('question_text'); // Nội dung câu hỏi
            $table->enum('question_type', ['single_choice', 'multiple_choice'])->default('single_choice'); // Loại câu hỏi
            $table->unsignedInteger('points')->default(1); // Điểm của câu hỏi
            $table->unsignedInteger('display_order')->default(0); // Thứ tự hiển thị
            $table->text('explanation')->nullable(); // Giải thích đáp án (hiển thị sau khi trả lời)
            $table->timestamps();
            $table->softDeletes();

            $table->index('quiz_id');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_quiz_questions');
    }
};


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
        Schema::create('lesson_quiz_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->text('option_text'); // Nội dung đáp án
            $table->boolean('is_correct')->default(false); // Đáp án đúng hay sai
            $table->unsignedInteger('display_order')->default(0); // Thứ tự hiển thị
            $table->timestamps();
            $table->softDeletes();

            $table->index('question_id');
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
        Schema::dropIfExists('lesson_quiz_options');
    }
};


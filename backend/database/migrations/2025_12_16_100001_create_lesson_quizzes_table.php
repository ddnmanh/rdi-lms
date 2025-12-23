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
        Schema::create('lesson_quizzes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->string('title')->nullable(); // Tiêu đề quiz (có thể null)
            $table->text('description')->nullable(); // Mô tả quiz
            $table->unsignedInteger('passing_percent_score')->default(70); // Điểm tối thiểu để pass (%)
            $table->boolean('is_required')->default(true); // Bắt buộc pass để xem tiếp
            $table->unsignedInteger('start_at_seconds')->nullable(); // Thời điểm dừng video để hiển thị quiz (giây)
            $table->unsignedInteger('max_questions')->default(4); // Số câu hỏi tối đa (1-4)
            $table->boolean('is_active')->default(true); // Trạng thái hoạt động
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('lesson_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_quizzes');
    }
};


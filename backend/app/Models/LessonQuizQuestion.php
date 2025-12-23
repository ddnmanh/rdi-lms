<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonQuizQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lesson_quiz_questions';

    protected $fillable = [
        'quiz_id',
        'question_text',
        'question_type',
        'points',
        'display_order',
        'explanation',
    ];

    protected $casts = [
        'points' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * Câu hỏi thuộc về quiz nào
     */
    public function quiz()
    {
        return $this->belongsTo(LessonQuiz::class, 'quiz_id');
    }

    /**
     * Các lựa chọn đáp án của câu hỏi
     */
    public function options()
    {
        return $this->hasMany(LessonQuizOption::class, 'question_id')->orderBy('display_order');
    }

    /**
     * Lấy các đáp án đúng
     */
    public function correctOptions()
    {
        return $this->options()->where('is_correct', true);
    }

    /**
     * Các câu trả lời cho câu hỏi này
     */
    public function attemptAnswers()
    {
        return $this->hasMany(LessonQuizAttemptAnswer::class, 'question_id');
    }

    /**
     * Kiểm tra câu hỏi là loại chọn nhiều đáp án
     */
    public function isMultipleChoice()
    {
        return $this->question_type === 'multiple_choice';
    }

    /**
     * Kiểm tra câu hỏi là loại chọn một đáp án
     */
    public function isSingleChoice()
    {
        return $this->question_type === 'single_choice';
    }
}


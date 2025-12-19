<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonQuizAttemptAnswer extends Model
{
    use HasFactory;

    protected $table = 'lesson_quiz_attempt_answers';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_ids',
        'is_correct',
        'points_earned',
    ];

    protected $casts = [
        'selected_option_ids' => 'array',
        'is_correct' => 'boolean',
        'points_earned' => 'integer',
    ];

    /**
     * Câu trả lời thuộc về lần làm bài nào
     */
    public function attempt()
    {
        return $this->belongsTo(LessonQuizAttempt::class, 'attempt_id');
    }

    /**
     * Câu trả lời cho câu hỏi nào
     */
    public function question()
    {
        return $this->belongsTo(LessonQuizQuestion::class, 'question_id');
    }

    /**
     * Lấy các option đã chọn
     */
    public function selectedOptions()
    {
        $optionIds = $this->selected_option_ids ?? [];
        return LessonQuizOption::whereIn('id', $optionIds)->get();
    }
}


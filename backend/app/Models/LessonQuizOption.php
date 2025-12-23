<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonQuizOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lesson_quiz_options';

    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'display_order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Đáp án thuộc về câu hỏi nào
     */
    public function question()
    {
        return $this->belongsTo(LessonQuizQuestion::class, 'question_id');
    }
}


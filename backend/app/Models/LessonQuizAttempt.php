<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonQuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'lesson_quiz_attempts';

    protected $fillable = [
        'quiz_id',
        'user_id',
        'percent_score_earned',
        'score_earned',
        'max_possible_score',
        'passed',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'percent_score_earned' => 'decimal:2',
        'score_earned' => 'integer',
        'max_possible_score' => 'integer',
        'passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Lần làm bài thuộc về quiz nào
     */
    public function quiz()
    {
        return $this->belongsTo(LessonQuiz::class, 'quiz_id');
    }

    /**
     * User đã làm bài
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Chi tiết các câu trả lời
     */
    public function answers()
    {
        return $this->hasMany(LessonQuizAttemptAnswer::class, 'attempt_id');
    }

    /**
     * Tính thời gian làm bài (giây)
     */
    public function getDurationAttribute()
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }
        return $this->completed_at->diffInSeconds($this->started_at);
    }

    /**
     * Kiểm tra đã hoàn thành chưa
     */
    public function isCompleted()
    {
        return $this->completed_at !== null;
    }
}


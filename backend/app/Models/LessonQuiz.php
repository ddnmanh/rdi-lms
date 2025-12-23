<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonQuiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lesson_quizzes';

    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'passing_percent_score',
        'is_required',
        'start_at_seconds',
        'max_questions',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'passing_percent_score' => 'integer',
        'is_required' => 'boolean',
        'start_at_seconds' => 'integer',
        'max_questions' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Quiz thuộc về lesson nào
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Các câu hỏi trong quiz
     */
    public function questions()
    {
        return $this->hasMany(LessonQuizQuestion::class, 'quiz_id')->orderBy('display_order');
    }

    /**
     * Các lần làm bài của quiz này
     */
    public function attempts()
    {
        return $this->hasMany(LessonQuizAttempt::class, 'quiz_id');
    }

    /**
     * Người tạo quiz
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Người cập nhật quiz
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Tính tổng điểm tối đa của quiz
     */
    public function getMaxPointsAttribute()
    {
        return $this->questions()->sum('points');
    }

    /**
     * Lấy số câu hỏi hiện tại
     */
    public function getQuestionsCountAttribute()
    {
        return $this->questions()->count();
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'thumbnail_path',
        'duration',
        'video_path',
        'hls_path',
        'display_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $attributes = [
        'course_id' => null,
    ];

    protected $casts = [
        'duration' => 'integer',
        'display_order' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function views()
    {
        return $this->hasMany(LessonView::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Các quiz của bài học
     */
    public function quizzes()
    {
        return $this->hasMany(LessonQuiz::class)->orderBy('start_at_seconds');
    }

    /**
     * Lấy quiz đang active
     */
    public function activeQuizzes()
    {
        return $this->quizzes()->where('is_active', true);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}


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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}


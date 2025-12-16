<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'user_id',
        'duration_at',
        'content',
    ];

    protected $casts = [
        'duration_at' => 'integer',
    ];

    /**
     * Bài học mà ghi chú thuộc về
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Người dùng tạo ghi chú
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


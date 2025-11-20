<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonVideoUpload extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_UPLOADING = 'uploading';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'lesson_id',
        'user_id',
        'original_name',
        'mime_type',
        'size_bytes',
        'chunk_size',
        'total_chunks',
        'uploaded_chunks',
        'status',
        'storage_disk',
        'storage_path',
        'temp_directory',
        'error_message',
        'meta',
        'processing_started_at',
        'processing_finished_at',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'chunk_size' => 'integer',
        'total_chunks' => 'integer',
        'uploaded_chunks' => 'integer',
        'meta' => 'array',
        'processing_started_at' => 'datetime',
        'processing_finished_at' => 'datetime',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

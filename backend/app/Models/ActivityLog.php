<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'method',
        'path',
        'route_name',
        'query',
        'body',
        'status_code',
        'duration_ms',
    ];

    protected $casts = [
        'query' => 'array',
        'body' => 'array',
    ];
}



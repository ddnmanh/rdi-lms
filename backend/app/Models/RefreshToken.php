<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'ip_address',
        'user_agent',
        'is_revoked',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_revoked' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kiểm tra token có còn hiệu lực không
     */
    public function isValid()
    {
        return !$this->is_revoked && $this->expires_at->isFuture();
    }

    /**
     * Revoke token
     */
    public function revoke()
    {
        $this->update(['is_revoked' => true]);
    }
}

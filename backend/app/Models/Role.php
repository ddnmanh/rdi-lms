<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'level', // Level của role: quyền cao nhất là 1, quyền thấp nhất là 255 (tỉ lệ nghịch)
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    /**
     * Kiểm tra role này có quyền cao hơn role khác không
     * Level thấp hơn = quyền cao hơn
     * 
     * @param Role|int $otherRole Role hoặc level cần so sánh
     * @return bool
     */
    public function hasHigherLevelThan($otherRole)
    {
        $otherLevel = $otherRole instanceof Role ? $otherRole->level : $otherRole;
        return $this->level < $otherLevel;
    }

    /**
     * Kiểm tra role này có quyền thấp hơn role khác không
     * Level cao hơn = quyền thấp hơn
     * 
     * @param Role|int $otherRole Role hoặc level cần so sánh
     * @return bool
     */
    public function hasLowerLevelThan($otherRole)
    {
        $otherLevel = $otherRole instanceof Role ? $otherRole->level : $otherRole;
        return $this->level > $otherLevel;
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


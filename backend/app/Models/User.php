<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'email',
        'password',
        'fullname',
        'birthday',
        'path_avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    /** Chỉ hash khi chưa phải hash */
    public function setPasswordAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['password'] = $value;
            return;
        }

        $info = password_get_info((string) $value);
        if (($info['algo'] ?? 0) !== 0) {
            $this->attributes['password'] = $value;
            return;
        }

        $this->attributes['password'] = Hash::make($value);
    }

    /** Relationships */
    public function roles()
    {
        // chỉnh lại 'user_role' nếu pivot tên khác
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user', 'user_id', 'course_id');
    }

    public function lessonViews()
    {
        return $this->hasMany(LessonView::class);
    }

    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

    /** JWT */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        $this->loadMissing('roles');

        $roles = $this->roles->map(fn($r) => [
            'id'    => $r->id,
            'name'  => $r->name,
            'level' => $r->level,
        ])->toArray();

        return [
            'email'       => $this->email,
            'fullname'    => $this->fullname,
            'path_avatar' => $this->path_avatar,
            'roles'       => $roles,
        ];
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function isRoot(): bool
    {
        return $this->hasRole('ROOT');
    }

    /**
     * Kiểm tra quyền theo method + path.
     * Hỗ trợ pattern: /api/users/{id} khớp /api/users/1
     */
    public function hasPermission(string $method, string $path): bool
    {
        if ($this->isRoot()) return true;

        $this->loadMissing('roles.permissions');

        $method = strtoupper($method);

        $permissions = $this->roles
            ->flatMap(fn($role) => $role->permissions)
            ->unique('id');

        foreach ($permissions as $permission) {
            if (strtoupper((string)$permission->method) !== $method) continue;

            $permPath = (string)$permission->path;

            // exact
            if ($permPath === $path) return true;

            // pattern {param}
            if ($this->matchPathBySegments($permPath, $path)) return true;
        }

        return false;
    }

    /**
     * So khớp path theo từng segment, {param} = wildcard cho đúng 1 segment.
     * Không dùng regex nên không thể phát sinh lỗi Unknown modifier.
     *
     * Ví dụ:
     *  - /api/users/{id}  ~ /api/users/1         => true
     *  - /api/courses/{c}/lessons/{l} ~ /api/courses/10/lessons/2 => true
     *  - /api/users       ~ /api/users/1         => false (khác số segment)
     */
    private function matchPathBySegments(string $pattern, string $path): bool
    {
        // Chuẩn hóa: bỏ slash đầu/cuối để split gọn
        $norm = fn(string $p) => trim($p, " \t\n\r\0\x0B/");

        $p1 = $norm($pattern);
        $p2 = $norm($path);

        // Trường hợp đặc biệt: root "/"
        if ($p1 === '' || $p2 === '') {
            return $p1 === $p2;
        }

        $segPattern = explode('/', $p1);
        $segPath    = explode('/', $p2);

        if (count($segPattern) !== count($segPath)) {
            return false;
        }

        foreach ($segPattern as $i => $seg) {
            $isParam = strlen($seg) >= 2 && $seg[0] === '{' && substr($seg, -1) === '}';

            if ($isParam) {
                // wildcard 1 segment: yêu cầu non-empty
                if ($segPath[$i] === '') return false;
                continue;
            }

            if ($seg !== $segPath[$i]) {
                return false;
            }
        }

        return true;
    }

    // Cần kiểm tra lại khi sử dụng
    // public function creator()
    // {
    //     return $this->belongsTo(User::class, 'created_by');
    // }

    // public function deleter()
    // {
    //     return $this->belongsTo(User::class, 'deleted_by');
    // }
}

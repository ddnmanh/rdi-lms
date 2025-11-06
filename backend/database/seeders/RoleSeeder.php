<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Đang tạo các roles...');

        $roles = [
            [
                'name' => 'ROOT',
                'description' => 'Quyền cao nhất, có thể truy cập tất cả tính năng trong hệ thống',
                'level' => 1,
            ],
            [
                'name' => 'ADMIN',
                'description' => 'Quản trị viên hệ thống, có quyền quản lý users, courses, và các tài nguyên khác',
                'level' => 10,
            ],
            [
                'name' => 'TEACHER',
                'description' => 'Giáo viên, có quyền tạo và quản lý khóa học, bài học',
                'level' => 50,
            ],
            [
                'name' => 'STUDENT',
                'description' => 'Học viên, có quyền xem khóa học và bài học đã đăng ký',
                'level' => 100,
            ],
        ];

        $permissions = Permission::all();

        foreach ($roles as $roleData) {
            $role = Role::withTrashed()->firstOrNew(['name' => $roleData['name']]);
            $role->description = $roleData['description'];
            $role->level = $roleData['level'];
            $role->deleted_at = null;
            $role->save();

            $this->command->info("✓ Đã tạo/cập nhật role {$role->name} (ID: {$role->id})");

            // Gán permissions cho từng role
            if ($role->name === 'ROOT' && !$permissions->isEmpty()) {
                // ROOT có tất cả permissions
                $role->permissions()->sync($permissions->pluck('id')->toArray());
                $this->command->info("  → Đã gán {$permissions->count()} permissions cho role ROOT");
            } elseif ($role->name === 'ADMIN' && !$permissions->isEmpty()) {
                // ADMIN có hầu hết permissions (trừ một số permissions đặc biệt của ROOT)
                $adminPermissions = $permissions->filter(function ($permission) {
                    // Có thể filter một số permissions đặc biệt nếu cần
                    return true;
                });
                $role->permissions()->sync($adminPermissions->pluck('id')->toArray());
                $this->command->info("  → Đã gán {$adminPermissions->count()} permissions cho role ADMIN");
            } elseif ($role->name === 'TEACHER' && !$permissions->isEmpty()) {
                // TEACHER có permissions liên quan đến courses và lessons
                $teacherPermissions = $permissions->filter(function ($permission) {
                    $path = strtolower($permission->path);
                    return strpos($path, 'course') !== false || 
                           strpos($path, 'lesson') !== false ||
                           strpos($path, 'profile') !== false;
                });
                if ($teacherPermissions->isNotEmpty()) {
                    $role->permissions()->sync($teacherPermissions->pluck('id')->toArray());
                    $this->command->info("  → Đã gán {$teacherPermissions->count()} permissions cho role TEACHER");
                }
            } elseif ($role->name === 'STUDENT' && !$permissions->isEmpty()) {
                // STUDENT có permissions xem courses và lessons
                $studentPermissions = $permissions->filter(function ($permission) {
                    $path = strtolower($permission->path);
                    $method = strtolower($permission->method);
                    return ($method === 'get' && (
                        strpos($path, 'course') !== false || 
                        strpos($path, 'lesson') !== false ||
                        strpos($path, 'profile') !== false
                    ));
                });
                if ($studentPermissions->isNotEmpty()) {
                    $role->permissions()->sync($studentPermissions->pluck('id')->toArray());
                    $this->command->info("  → Đã gán {$studentPermissions->count()} permissions cho role STUDENT");
                }
            }
        }

        if ($permissions->isEmpty()) {
            $this->command->warn('⚠ Chưa có permissions nào. Vui lòng chạy PermissionSeeder trước.');
        }

        $this->command->info('✓ Hoàn thành tạo các roles!');
    }
}

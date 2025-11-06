<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeederProduction extends Seeder
{
    /**
     * Seed the application's database for production.
     * Chỉ tạo user root cho production.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Đang tạo user root cho production...');

        $userData = [
            'email' => 'root@rdi.tvu.vn',
            'password' => '123123123',
            'fullname' => 'Root Administrator',
            'birthday' => '1990-01-01',
            'roles' => ['ROOT'],
        ];

        $roles = $userData['roles'];
        unset($userData['roles']);

        // Tìm hoặc tạo user
        $user = User::withTrashed()->firstOrNew(['email' => $userData['email']]);

        // Chỉ cập nhật nếu user chưa tồn tại hoặc đã bị soft delete
        if (!$user->exists || $user->trashed()) {
            $user->fill($userData);
            $user->deleted_at = null;
            $user->save();
            $this->command->info("✓ Đã tạo/cập nhật user: {$user->fullname} ({$user->email})");
        } else {
            $this->command->info("✓ User đã tồn tại: {$user->fullname} ({$user->email})");
        }

        // Gán roles cho user
        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                if (!$user->roles()->where('roles.id', $role->id)->exists()) {
                    $user->roles()->attach($role->id);
                }
            } else {
                $this->command->warn("  ⚠ Không tìm thấy role {$roleName}");
            }
        }

        $this->command->info("✓ Hoàn thành! Đã tạo user root cho production.");
    }
}


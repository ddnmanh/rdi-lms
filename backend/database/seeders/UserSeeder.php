<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Đang tạo user root mặc định...');

        // Tìm hoặc tạo user root
        $rootUser = User::withTrashed()->firstOrNew(['email' => 'root@rdi.tvu.vn']);
        
        // Chỉ cập nhật nếu user chưa tồn tại hoặc đã bị soft delete
        if (!$rootUser->exists || $rootUser->trashed()) {
            $rootUser->email = 'root@rdi.tvu.vn';
            $rootUser->password = '123123123'; // Sẽ được hash tự động bởi setPasswordAttribute
            $rootUser->fullname = 'Root Administrator';
            $rootUser->deleted_at = null; // Đảm bảo không bị soft delete
            $rootUser->save();

            $this->command->info("✓ Đã tạo/cập nhật user root (ID: {$rootUser->id})");
        } else {
            // Nếu user đã tồn tại, chỉ cập nhật password nếu cần
            $this->command->info("✓ User root đã tồn tại (ID: {$rootUser->id})");
        }

        // Gán role ROOT cho user
        $rootRole = Role::where('name', 'ROOT')->first();
        
        if ($rootRole) {
            // Sử dụng syncWithoutDetaching để không xóa các role khác nếu có
            if (!$rootUser->roles()->where('roles.id', $rootRole->id)->exists()) {
                $rootUser->roles()->attach($rootRole->id);
                $this->command->info("✓ Đã gán role ROOT cho user root");
            } else {
                $this->command->info("✓ User root đã có role ROOT");
            }
        } else {
            $this->command->warn("⚠ Không tìm thấy role ROOT. Vui lòng chạy RoleSeeder trước.");
        }
    }
}


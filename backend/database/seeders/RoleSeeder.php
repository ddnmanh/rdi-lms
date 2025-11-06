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
        $this->command->info('Đang tạo role ROOT...');

        // Tạo hoặc cập nhật role ROOT
        $rootRole = Role::withTrashed()->firstOrNew(['name' => 'ROOT']);
        $rootRole->description = 'Quyền cao nhất, có thể truy cập tất cả tính năng trong hệ thống';
        $rootRole->level = 1; // Level cao nhất (level thấp = quyền cao)
        $rootRole->deleted_at = null; // Đảm bảo không bị soft delete
        $rootRole->save();

        $this->command->info("✓ Đã tạo/cập nhật role ROOT (ID: {$rootRole->id})");

        // Gán tất cả permissions cho role ROOT
        $permissions = Permission::all();

        if ($permissions->isEmpty()) {
            $this->command->warn('⚠ Chưa có permissions nào. Vui lòng chạy PermissionSeeder trước.');
            return;
        }

        // Sync tất cả permissions với role ROOT
        $rootRole->permissions()->sync($permissions->pluck('id')->toArray());

        $this->command->info("✓ Đã gán {$permissions->count()} permissions cho role ROOT");
        $this->command->info('✓ Hoàn thành tạo role ROOT!');
    }
}

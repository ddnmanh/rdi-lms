<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        
        // Quét routes và thêm vào bảng permissions
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class, // Tạo role ROOT sau khi có permissions
            UserSeeder::class, // Tạo user root mặc định sau khi có role ROOT
        ]);
    }
}

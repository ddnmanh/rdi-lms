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
        $this->command->info("=== BẮT ĐẦU SEEDING ===");

        // Seeder chung cho mọi môi trường
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        // Seeder dành riêng cho môi trường production
        if (app()->environment('production')) {
            $this->command->warn('== Chạy SEED PRODUCTION ==');
            $this->call([
                UserSeederProduction::class, // Chỉ tạo root user cho production
            ]);
        }

        // Seeder dành riêng cho môi trường local và development
        if (app()->environment('local', 'dev')) {
            $this->command->warn('== Chạy SEED DEV ==');
            $this->call([
                UserSeederDev::class,       // 1. Tạo 150 users cho dev
                CourseSeeder::class,        // 2. Tạo courses
                LessonSeeder::class,        // 3. Tạo lessons (cần courses)
                CourseUserSeeder::class,    // 4. Gán users vào courses
                LessonViewSeeder::class,    // 5. Tạo dữ liệu xem bài học
            ]);
        }

        $this->command->info("=== HOÀN TẤT SEEDING ===");
    }

}

<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Đang tạo các khóa học mẫu...');

        // Tạo 10 khóa học ngẫu nhiên
        Course::factory()->count(10)->create();

        $this->command->info("✓ Hoàn thành! Đã tạo 10 khóa học.");
    }
}


<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Đang gán users vào các khóa học...');

        $users = User::all();
        $courses = Course::all();

        if ($users->isEmpty()) {
            $this->command->warn('⚠ Chưa có users nào. Vui lòng chạy UserSeederDev hoặc UserSeederProduction trước.');
            return;
        }

        if ($courses->isEmpty()) {
            $this->command->warn('⚠ Chưa có khóa học nào. Vui lòng chạy CourseSeeder trước.');
            return;
        }

        $assignedCount = 0;

        // Gán tất cả users vào một số khóa học ngẫu nhiên
        foreach ($users as $user) {
            // Mỗi user sẽ được gán vào 2-4 khóa học ngẫu nhiên
            $randomCourses = $courses->random(rand(2, min(4, $courses->count())));
            
            foreach ($randomCourses as $course) {
                // Kiểm tra xem đã gán chưa
                if (!$user->courses()->where('courses.id', $course->id)->exists()) {
                    $user->courses()->attach($course->id);
                    $assignedCount++;
                }
            }
        }

        $this->command->info("✓ Hoàn thành! Đã gán {$assignedCount} users vào các khóa học.");
    }
}


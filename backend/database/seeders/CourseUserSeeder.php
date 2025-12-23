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

        $users = User::orderBy('id')->get();
        $courses = Course::orderBy('id')->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠ Chưa có users nào. Vui lòng chạy UserSeederDev hoặc UserSeederProduction trước.');
            return;
        }

        if ($courses->isEmpty()) {
            $this->command->warn('⚠ Chưa có khóa học nào. Vui lòng chạy CourseSeeder trước.');
            return;
        }

        $assignedCount = 0;
        $coursesCount = $courses->count();

        // Gán users vào khóa học với logic cố định dựa trên index
        foreach ($users as $userIndex => $user) {
            // Mỗi user sẽ được gán vào 3 khóa học (cố định)
            // Sử dụng modulo để đảm bảo phân bổ đều
            $numCourses = 3;
            
            for ($i = 0; $i < $numCourses; $i++) {
                // Tính index của course dựa trên user index và offset
                $courseIndex = ($userIndex * $numCourses + $i) % $coursesCount;
                $course = $courses[$courseIndex];
                
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


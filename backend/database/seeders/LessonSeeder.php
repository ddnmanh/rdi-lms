<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Đang tạo các bài học mẫu...');

        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->command->warn('⚠ Chưa có khóa học nào. Vui lòng chạy CourseSeeder trước.');
            return;
        }

        $totalLessons = 0;

        foreach ($courses as $course) {
            // Tạo 5-10 bài học cho mỗi khóa học
            $lessons = Lesson::factory()->count(rand(5, 10))->create([
                'course_id' => $course->id,
            ]);

            // Cập nhật display_order
            foreach ($lessons as $index => $lesson) {
                $lesson->update(['display_order' => $index + 1]);
            }

            $count = $lessons->count();
            $totalLessons += $count;
            $this->command->info("✓ Đã tạo {$count} bài học cho khóa học: {$course->title}");
        }

        $this->command->info("✓ Hoàn thành! Đã tạo tổng cộng {$totalLessons} bài học.");
    }
}


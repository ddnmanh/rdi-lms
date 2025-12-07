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

        $lessonTemplates = [
            'Lập trình Web với PHP và Laravel' => [
                ['title' => 'Giới thiệu về PHP', 'description' => 'Tổng quan về PHP và môi trường phát triển', 'duration' => 1800, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/php-intro.mp4', 'created_by' => 1],
                ['title' => 'Cú pháp PHP cơ bản', 'description' => 'Biến, kiểu dữ liệu, và các toán tử trong PHP', 'duration' => 2400, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/php-syntax.mp4', 'created_by' => 1],
                ['title' => 'Làm việc với Arrays và Functions', 'description' => 'Mảng và hàm trong PHP', 'duration' => 2700, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/php-arrays-functions.mp4', 'created_by' => 1],
                ['title' => 'Giới thiệu Laravel Framework', 'description' => 'Tổng quan về Laravel và cài đặt', 'duration' => 2100, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/laravel-intro.mp4', 'created_by' => 1],
                ['title' => 'Routing và Controllers', 'description' => 'Cách tạo routes và controllers trong Laravel', 'duration' => 3000, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/laravel-routing.mp4', 'created_by' => 1],
                ['title' => 'Database và Eloquent ORM', 'description' => 'Làm việc với database và Eloquent', 'duration' => 3600, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/laravel-eloquent.mp4', 'created_by' => 1],
            ],
            'JavaScript và React.js từ Zero đến Hero' => [
                ['title' => 'JavaScript cơ bản', 'description' => 'Biến, hàm, và cấu trúc điều khiển', 'duration' => 2400, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/js-basics.mp4', 'created_by' => 1],
                ['title' => 'ES6+ Features', 'description' => 'Arrow functions, destructuring, spread operator', 'duration' => 2700, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/js-es6.mp4', 'created_by' => 1],
                ['title' => 'Giới thiệu React', 'description' => 'Tổng quan về React và JSX', 'duration' => 2100, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/react-intro.mp4', 'created_by' => 1],
                ['title' => 'Components và Props', 'description' => 'Tạo và sử dụng React Components', 'duration' => 3000, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/react-components.mp4', 'created_by' => 1],
                ['title' => 'State và Hooks', 'description' => 'useState, useEffect và các Hooks khác', 'duration' => 3600, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/react-hooks.mp4', 'created_by' => 1],
                ['title' => 'React Router', 'description' => 'Điều hướng trong React ứng dụng', 'duration' => 2400, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/react-router.mp4', 'created_by' => 1],
            ],
            'Python cho Data Science và Machine Learning' => [
                ['title' => 'Python cơ bản', 'description' => 'Cú pháp Python và các kiểu dữ liệu', 'duration' => 2700, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/python-basics.mp4', 'created_by' => 1],
                ['title' => 'NumPy - Mảng đa chiều', 'description' => 'Làm việc với NumPy arrays', 'duration' => 3000, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/numpy.mp4', 'created_by' => 1],
                ['title' => 'Pandas - Data Manipulation', 'description' => 'Xử lý dữ liệu với Pandas', 'duration' => 3600, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/pandas.mp4', 'created_by' => 1],
                ['title' => 'Data Visualization', 'description' => 'Vẽ biểu đồ với Matplotlib và Seaborn', 'duration' => 2700, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/visualization.mp4', 'created_by' => 1],
                ['title' => 'Machine Learning cơ bản', 'description' => 'Giới thiệu về ML và Scikit-learn', 'duration' => 4200, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/ml-basics.mp4', 'created_by' => 1],
            ],
        ];

        $defaultLessons = [
            ['title' => 'Bài 1: Giới thiệu', 'description' => 'Tổng quan về khóa học', 'duration' => 1800, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/intro.mp4', 'created_by' => 1],
            ['title' => 'Bài 2: Cài đặt môi trường', 'description' => 'Hướng dẫn cài đặt công cụ cần thiết', 'duration' => 2400, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/setup.mp4', 'created_by' => 1],
            ['title' => 'Bài 3: Kiến thức nền tảng', 'description' => 'Các khái niệm cơ bản', 'duration' => 2700, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/fundamentals.mp4', 'created_by' => 1],
            ['title' => 'Bài 4: Thực hành', 'description' => 'Bài tập thực hành', 'duration' => 3000, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/practice.mp4', 'created_by' => 1],
            ['title' => 'Bài 5: Nâng cao', 'description' => 'Các chủ đề nâng cao', 'duration' => 3600, 'thumbnail_path' => '/static/defaults/video-not-available.jpg', 'video_path' => 'https://example.com/videos/advanced.mp4', 'created_by' => 1],
        ];

        $totalLessons = 0;

        foreach ($courses as $course) {
            $lessons = $lessonTemplates[$course->title] ?? $defaultLessons;
            $displayOrder = 1;

            foreach ($lessons as $lessonData) {
                $lesson = Lesson::withTrashed()->firstOrNew([
                    'course_id' => $course->id,
                    'title' => $lessonData['title'],
                ]);

                $lesson->fill($lessonData);
                $lesson->display_order = $displayOrder;
                $lesson->deleted_at = null;
                $lesson->save();

                $displayOrder++;
                $totalLessons++;
            }

            $this->command->info("✓ Đã tạo " . count($lessons) . " bài học cho khóa học: {$course->title}");
        }

        $this->command->info("✓ Hoàn thành! Đã tạo tổng cộng {$totalLessons} bài học.");
    }
}


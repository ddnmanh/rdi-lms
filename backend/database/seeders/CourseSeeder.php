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

        $courses = [
            [
                'title' => 'Lập trình Web với PHP và Laravel',
                'description' => 'Khóa học toàn diện về lập trình web sử dụng PHP và framework Laravel. Học viên sẽ được hướng dẫn từ cơ bản đến nâng cao.',
                'thumbnail_path' => '/static/defaults/image-not-available.jpg',
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->addMonths(4),
                'created_by' => 1,
            ],
            [
                'title' => 'JavaScript và React.js từ Zero đến Hero',
                'description' => 'Khóa học JavaScript và React.js giúp bạn xây dựng các ứng dụng web hiện đại. Bao gồm ES6+, Hooks, Redux, và nhiều hơn nữa.',
                'thumbnail_path' => '/static/defaults/image-not-available.jpg',
                'start_date' => Carbon::now()->subMonth(),
                'end_date' => Carbon::now()->addMonths(5),
                'created_by' => 1,
            ],
            [
                'title' => 'Python cho Data Science và Machine Learning',
                'description' => 'Học Python từ cơ bản và áp dụng vào Data Science, Machine Learning với các thư viện như Pandas, NumPy, Scikit-learn.',
                'thumbnail_path' => '/static/defaults/image-not-available.jpg',
                'start_date' => Carbon::now()->subWeeks(2),
                'end_date' => Carbon::now()->addMonths(6),
                'created_by' => 1,
            ],
            [
                'title' => 'Node.js và Express.js - Backend Development',
                'description' => 'Xây dựng RESTful APIs và ứng dụng backend với Node.js và Express.js. Học về authentication, database, và deployment.',
                'thumbnail_path' => '/static/defaults/image-not-available.jpg',
                'start_date' => Carbon::now()->subWeeks(3),
                'end_date' => Carbon::now()->addMonths(3),
                'created_by' => 1,
            ],
            [
                'title' => 'Vue.js - Framework JavaScript hiện đại',
                'description' => 'Khóa học Vue.js từ cơ bản đến nâng cao. Học về Components, Vuex, Vue Router, và xây dựng SPA hoàn chỉnh.',
                'thumbnail_path' => '/static/defaults/image-not-available.jpg',
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addMonths(4),
                'created_by' => 1,
            ],
            [
                'title' => 'Flutter - Phát triển ứng dụng di động',
                'description' => 'Học Flutter để xây dựng ứng dụng di động đa nền tảng cho iOS và Android. Sử dụng Dart và Flutter SDK.',
                'thumbnail_path' => '/static/defaults/image-not-available.jpg',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addMonths(5),
                'created_by' => 1,
            ],
            [
                'title' => 'Docker và Kubernetes - Containerization',
                'description' => 'Học về Docker, containerization, và Kubernetes để deploy và quản lý ứng dụng một cách hiệu quả.',
                'thumbnail_path' => '/static/defaults/image-not-available.jpg',
                'start_date' => Carbon::now()->subWeek(),
                'end_date' => Carbon::now()->addMonths(3),
                'created_by' => 1,
            ],
            [
                'title' => 'MySQL và Database Design',
                'description' => 'Khóa học về thiết kế database, SQL queries, indexing, optimization và best practices cho MySQL.',
                'thumbnail_path' => '/static/defaults/image-not-available.jpg',
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->addMonths(2),
                'created_by' => 1,
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::withTrashed()->firstOrNew(['title' => $courseData['title']]);
            $course->fill($courseData);
            $course->deleted_at = null;
            $course->save();

            $this->command->info("✓ Đã tạo/cập nhật khóa học: {$course->title} (ID: {$course->id})");
        }

        $this->command->info("✓ Hoàn thành! Đã tạo " . count($courses) . " khóa học.");
    }
}


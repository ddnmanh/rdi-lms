<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
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

        $courses = Course::orderBy('id')->get();

        if ($courses->isEmpty()) {
            $this->command->warn('⚠ Chưa có khóa học nào. Vui lòng chạy CourseSeeder trước.');
            return;
        }

        // Lấy user đầu tiên làm created_by và updated_by
        $creator = User::orderBy('id')->first();
        if (!$creator) {
            $this->command->warn('⚠ Chưa có user nào. Vui lòng chạy UserSeeder trước.');
            return;
        }

        // ============================================
        // CẤU TRÚC DỮ LIỆU BÀI HỌC
        // Mỗi khóa học có danh sách bài học riêng
        // Key là title của khóa học (phải khớp với CourseSeeder)
        // Bạn có thể thay thế nội dung trong mảng này
        // ============================================
        $lessonsByCourse = [
            'Khóa học Laravel Cơ bản' => [
                [
                    'title' => 'Giới thiệu Laravel Framework',
                    'description' => 'Trong bài học này, chúng ta sẽ tìm hiểu về Laravel Framework - một PHP framework mạnh mẽ và phổ biến.',
                    'duration' => 1200, // giây (20 phút)
                ],
                [
                    'title' => 'Cài đặt và Cấu hình Laravel',
                    'description' => 'Hướng dẫn chi tiết cách cài đặt Laravel và cấu hình môi trường phát triển.',
                    'duration' => 1800, // giây (30 phút)
                ],
                [
                    'title' => 'Routing và Controller trong Laravel',
                    'description' => 'Tìm hiểu về routing và controller - những thành phần cốt lõi của Laravel.',
                    'duration' => 2400, // giây (40 phút)
                ],
                [
                    'title' => 'Database Migration và Eloquent ORM',
                    'description' => 'Học cách sử dụng Migration và Eloquent ORM để làm việc với database.',
                    'duration' => 2100, // giây (35 phút)
                ],
                [
                    'title' => 'Authentication và Authorization',
                    'description' => 'Xây dựng hệ thống xác thực và phân quyền trong Laravel.',
                    'duration' => 2700, // giây (45 phút)
                ],
            ],
            'Lập trình ReactJS Nâng cao' => [
                [
                    'title' => 'Giới thiệu ReactJS và Hooks',
                    'description' => 'Tìm hiểu về React Hooks và cách sử dụng chúng trong ứng dụng React.',
                    'duration' => 1500,
                ],
                [
                    'title' => 'State Management với Redux',
                    'description' => 'Học cách quản lý state phức tạp với Redux trong React.',
                    'duration' => 3000,
                ],
                [
                    'title' => 'Performance Optimization',
                    'description' => 'Các kỹ thuật tối ưu hiệu năng cho ứng dụng React.',
                    'duration' => 2400,
                ],
            ],
            'Thực chiến NodeJS Toàn tập' => [
                [
                    'title' => 'Node.js Fundamentals',
                    'description' => 'Nắm vững các khái niệm cơ bản về Node.js và Event Loop.',
                    'duration' => 1800,
                ],
                [
                    'title' => 'Express.js Framework',
                    'description' => 'Xây dựng RESTful API với Express.js.',
                    'duration' => 2700,
                ],
                [
                    'title' => 'Database Integration',
                    'description' => 'Kết nối và làm việc với MongoDB và MySQL trong Node.js.',
                    'duration' => 2400,
                ],
            ],
            'Master Python Cho người mới bắt đầu' => [
                [
                    'title' => 'Python Basics',
                    'description' => 'Học các kiến thức cơ bản về Python: biến, kiểu dữ liệu, cấu trúc điều khiển.',
                    'duration' => 1200,
                ],
                [
                    'title' => 'Functions và Modules',
                    'description' => 'Tìm hiểu về functions, modules và cách tổ chức code trong Python.',
                    'duration' => 1800,
                ],
                [
                    'title' => 'Object-Oriented Programming',
                    'description' => 'Lập trình hướng đối tượng trong Python.',
                    'duration' => 2400,
                ],
            ],
            'Bootcamp DevOps Trong 30 ngày' => [
                [
                    'title' => 'Giới thiệu DevOps',
                    'description' => 'Tổng quan về DevOps và các công cụ phổ biến.',
                    'duration' => 1500,
                ],
                [
                    'title' => 'CI/CD với Jenkins',
                    'description' => 'Thiết lập pipeline CI/CD với Jenkins.',
                    'duration' => 3000,
                ],
                [
                    'title' => 'Containerization với Docker',
                    'description' => 'Học cách containerize ứng dụng với Docker.',
                    'duration' => 2700,
                ],
            ],
            'Khóa học AWS Cơ bản' => [
                [
                    'title' => 'AWS Fundamentals',
                    'description' => 'Giới thiệu về AWS và các dịch vụ cơ bản.',
                    'duration' => 1800,
                ],
                [
                    'title' => 'EC2 và VPC',
                    'description' => 'Làm việc với EC2 instances và Virtual Private Cloud.',
                    'duration' => 2400,
                ],
                [
                    'title' => 'S3 và CloudFront',
                    'description' => 'Sử dụng S3 để lưu trữ và CloudFront cho CDN.',
                    'duration' => 2100,
                ],
            ],
            'Lập trình Docker Nâng cao' => [
                [
                    'title' => 'Docker Advanced Concepts',
                    'description' => 'Các khái niệm nâng cao về Docker: networks, volumes, compose.',
                    'duration' => 2400,
                ],
                [
                    'title' => 'Docker Swarm và Orchestration',
                    'description' => 'Quản lý cluster với Docker Swarm.',
                    'duration' => 3000,
                ],
            ],
            'Thực chiến Machine Learning Toàn tập' => [
                [
                    'title' => 'Machine Learning Basics',
                    'description' => 'Giới thiệu về Machine Learning và các thuật toán cơ bản.',
                    'duration' => 2700,
                ],
                [
                    'title' => 'Neural Networks',
                    'description' => 'Xây dựng và train neural networks.',
                    'duration' => 3600,
                ],
                [
                    'title' => 'Deep Learning với TensorFlow',
                    'description' => 'Thực hành Deep Learning với TensorFlow.',
                    'duration' => 3300,
                ],
            ],
            'Master Laravel Cho người mới bắt đầu' => [
                [
                    'title' => 'Laravel Overview',
                    'description' => 'Tổng quan về Laravel và kiến trúc MVC.',
                    'duration' => 1200,
                ],
                [
                    'title' => 'Blade Templates',
                    'description' => 'Sử dụng Blade template engine trong Laravel.',
                    'duration' => 1800,
                ],
                [
                    'title' => 'Form Validation',
                    'description' => 'Xử lý và validate form trong Laravel.',
                    'duration' => 1500,
                ],
            ],
            'Cấp tốc ReactJS Trong 30 ngày' => [
                [
                    'title' => 'Tổng quan về React',
                    'description' => 'Học các khái niệm cơ bản về React: components, props, state.',
                    'duration' => 0,
                ],
                [
                    'title' => 'React Router',
                    'description' => 'Thiết lập routing trong ứng dụng React.',
                    'duration' => 0,
                ],
                [
                    'title' => 'Tích hợp API',
                    'description' => 'Kết nối React với RESTful API.',
                    'duration' => 0,
                ],
            ],
        ];

        $totalLessons = 0;

        foreach ($courses as $course) {
            $lessonsCount = 0;

            // Kiểm tra xem khóa học có danh sách bài học riêng không
            if (isset($lessonsByCourse[$course->title])) {
                $lessons = $lessonsByCourse[$course->title];

                foreach ($lessons as $lessonIndex => $lessonData) {
                    $lesson = Lesson::firstOrCreate(
                        [
                            'course_id' => $course->id,
                            'title' => $lessonData['title'],
                        ],
                        [
                            'description' => $lessonData['description'],
                            'thumbnail_path' => '/static/defaults/video-not-available.jpg',
                            'duration' => $lessonData['duration'],
                            'video_path' => null,
                            'hls_path' => null,
                            'display_order' => $lessonIndex + 1,
                            'created_by' => $creator->id,
                            'updated_by' => $creator->id,
                        ]
                    );
                    $lessonsCount++;
                }
            } else {
                // Nếu khóa học không có trong danh sách, bỏ qua hoặc tạo bài học mặc định
                $this->command->warn("⚠ Khóa học '{$course->title}' chưa có danh sách bài học. Vui lòng thêm vào mảng \$lessonsByCourse.");
            }

            $totalLessons += $lessonsCount;
            $this->command->info("✓ Đã tạo {$lessonsCount} bài học cho khóa học: {$course->title}");
        }

        $this->command->info("✓ Hoàn thành! Đã tạo tổng cộng {$totalLessons} bài học.");
    }
}


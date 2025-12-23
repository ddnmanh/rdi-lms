<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
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

        // Lấy user đầu tiên làm created_by (thường là root hoặc admin)
        $creator = User::orderBy('id')->first();
        if (!$creator) {
            $this->command->warn('⚠ Chưa có user nào. Vui lòng chạy UserSeeder trước.');
            return;
        }

        // ============================================
        // CẤU TRÚC DỮ LIỆU KHÓA HỌC
        // Bạn có thể thay thế nội dung trong mảng này
        // ============================================
        $coursesData = [
            [
                'title' => 'Khóa học Laravel Cơ bản',
                'description' => 'Khóa học Laravel Cơ bản cung cấp kiến thức toàn diện và các dự án thực tế. Giúp bạn tự tin ứng tuyển vào các công ty công nghệ hàng đầu.',
                'start_date_offset' => 0, // số ngày từ hôm nay
                'end_date_offset' => 180, // số ngày từ hôm nay
            ],
            [
                'title' => 'Lập trình ReactJS Nâng cao',
                'description' => 'Lập trình ReactJS Nâng cao cung cấp kiến thức toàn diện và các dự án thực tế. Giúp bạn tự tin ứng tuyển vào các công ty công nghệ hàng đầu.',
                'start_date_offset' => 2,
                'end_date_offset' => 185,
            ],
            [
                'title' => 'Thực chiến NodeJS Toàn tập',
                'description' => 'Thực chiến NodeJS Toàn tập cung cấp kiến thức toàn diện và các dự án thực tế. Giúp bạn tự tin ứng tuyển vào các công ty công nghệ hàng đầu.',
                'start_date_offset' => 4,
                'end_date_offset' => 190,
            ],
            [
                'title' => 'Master Python Cho người mới bắt đầu',
                'description' => 'Master Python Cho người mới bắt đầu cung cấp kiến thức toàn diện và các dự án thực tế. Giúp bạn tự tin ứng tuyển vào các công ty công nghệ hàng đầu.',
                'start_date_offset' => 6,
                'end_date_offset' => 195,
            ],
            [
                'title' => 'Bootcamp DevOps Trong 30 ngày',
                'description' => 'Bootcamp DevOps Trong 30 ngày cung cấp kiến thức toàn diện và các dự án thực tế. Giúp bạn tự tin ứng tuyển vào các công ty công nghệ hàng đầu.',
                'start_date_offset' => 8,
                'end_date_offset' => 200,
            ],
            [
                'title' => 'Khóa học AWS Cơ bản',
                'description' => 'Khóa học AWS Cơ bản cung cấp kiến thức toàn diện và các dự án thực tế. Giúp bạn tự tin ứng tuyển vào các công ty công nghệ hàng đầu.',
                'start_date_offset' => 10,
                'end_date_offset' => 205,
            ],
            [
                'title' => 'Lập trình Docker Nâng cao',
                'description' => 'Lập trình Docker Nâng cao cung cấp kiến thức toàn diện và các dự án thực tế. Giúp bạn tự tin ứng tuyển vào các công ty công nghệ hàng đầu.',
                'start_date_offset' => 12,
                'end_date_offset' => 210,
            ],
            [
                'title' => 'Thực chiến Machine Learning Toàn tập',
                'description' => 'Thực chiến Machine Learning Toàn tập cung cấp kiến thức toàn diện và các dự án thực tế. Giúp bạn tự tin ứng tuyển vào các công ty công nghệ hàng đầu.',
                'start_date_offset' => 14,
                'end_date_offset' => 215,
            ],
            [
                'title' => 'Master Laravel Cho người mới bắt đầu',
                'description' => 'Master Laravel Cho người mới bắt đầu cung cấp kiến thức toàn diện và các dự án thực tế. Giúp bạn tự tin ứng tuyển vào các công ty công nghệ hàng đầu.',
                'start_date_offset' => 16,
                'end_date_offset' => 220,
            ],
            [
                'title' => 'Cấp tốc ReactJS Trong 30 ngày',
                'description' => 'Cấp tốc ReactJS Trong 30 ngày cung cấp kiến thức toàn diện và các dự án thực tế. Giúp bạn tự tin ứng tuyển vào các công ty công nghệ hàng đầu.',
                'start_date_offset' => 18,
                'end_date_offset' => 225,
            ],
        ];

        $baseDate = Carbon::now();

        foreach ($coursesData as $courseData) {
            $course = Course::firstOrCreate(
                ['title' => $courseData['title']],
                [
                    'description' => $courseData['description'],
                    'thumbnail_path' => '/static/defaults/course_thumbnail.jpg',
                    'start_date' => $baseDate->copy()->addDays($courseData['start_date_offset']),
                    'end_date' => $baseDate->copy()->addDays($courseData['end_date_offset']),
                    'created_by' => $creator->id,
                ]
            );
        }

        $this->command->info("✓ Hoàn thành! Đã tạo 10 khóa học.");
    }
}


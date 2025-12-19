<?php

namespace Database\Factories;

use App\Models\LessonQuiz;
use App\Models\LessonQuizQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonQuizQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LessonQuizQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $types = ['single_choice', 'multiple_choice'];
        $questions = [
            'Đâu là đặc điểm chính của ngôn ngữ này?',
            'Kết quả của đoạn lệnh sau là gì?',
            'Tại sao chúng ta nên sử dụng framework này?',
            'Hàm nào được dùng để xử lý chuỗi?',
            'Phương pháp tối ưu hiệu năng tốt nhất là gì?',
            'Lỗi logic phổ biến khi triển khai tính năng này là gì?',
            'Thành phần nào đóng vai trò quan trọng nhất trong kiến trúc này?',
            'Quy trình deploy chuẩn bao gồm các bước nào?',
            'Làm thế nào để bảo mật API?',
            'Design Pattern nào phù hợp cho bài toán này?',
        ];

        return [
            'quiz_id' => LessonQuiz::factory(),
            'question_text' => $this->faker->randomElement($questions),
            'question_type' => $this->faker->randomElement($types),
            'points' => $this->faker->numberBetween(1, 5) * 10,
            'display_order' => $this->faker->numberBetween(1, 10),
            'explanation' => 'Giải thích: Đây là kiến thức cơ bản cần nắm vững. Tham khảo tài liệu chính thức để biết thêm chi tiết.',
        ];
    }
}

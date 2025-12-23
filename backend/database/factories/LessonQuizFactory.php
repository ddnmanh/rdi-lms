<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\LessonQuiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonQuizFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LessonQuiz::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $titles = [
            'Bài kiểm tra kiến thức cơ bản',
            'Trắc nghiệm ôn tập',
            'Kiểm tra cuối bài',
            'Quiz đánh giá năng lực',
            'Bài tập trắc nghiệm nhanh',
            'Kiểm tra tư duy lập trình',
            'Ôn tập kiến thức đã học',
        ];

        return [
            'lesson_id' => Lesson::factory(),
            'title' => $this->faker->randomElement($titles) . ' - ' . $this->faker->numberBetween(1, 10),
            'description' => 'Bài kiểm tra này giúp bạn củng cố kiến thức vừa học. Vui lòng hoàn thành để tiếp tục.',
            'passing_percent_score' => $this->faker->numberBetween(50, 80), // Giảm xuống 50-80 cho dễ pass hơn
            'is_required' => $this->faker->boolean(70),
            'start_at_seconds' => $this->faker->optional(0.3)->numberBetween(30, 300),
            'max_questions' => $this->faker->numberBetween(5, 10),
            'is_active' => true,
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}

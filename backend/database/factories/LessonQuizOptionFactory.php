<?php

namespace Database\Factories;

use App\Models\LessonQuizQuestion;
use App\Models\LessonQuizOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonQuizOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LessonQuizOption::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $options = [
            'Đây là đáp án chính xác',
            'Đây là phương án sai',
            'Không có đáp án nào đúng',
            'Tất cả các đáp án trên đều đúng',
            'Cần xem xét thêm ngữ cảnh',
            'Chỉ đúng trong một số trường hợp',
            'Hoàn toàn không chính xác',
            'Là phương pháp tối ưu nhất',
        ];

        return [
            'question_id' => LessonQuizQuestion::factory(),
            'option_text' => $this->faker->randomElement($options),
            'is_correct' => false, // Will be overridden in Seeder logic likely
            'display_order' => $this->faker->numberBetween(1, 4),
        ];
    }
}

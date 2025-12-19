<?php

namespace Database\Factories;

use App\Models\LessonQuizAttempt;
use App\Models\LessonQuizAttemptAnswer;
use App\Models\LessonQuizQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonQuizAttemptAnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LessonQuizAttemptAnswer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'attempt_id' => LessonQuizAttempt::factory(),
            'question_id' => LessonQuizQuestion::factory(),
            'selected_option_ids' => [], // To be populated
            'is_correct' => $this->faker->boolean(),
            'points_earned' => $this->faker->numberBetween(0, 10),
        ];
    }
}

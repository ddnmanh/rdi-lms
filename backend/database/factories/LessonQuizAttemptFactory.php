<?php

namespace Database\Factories;

use App\Models\LessonQuiz;
use App\Models\LessonQuizAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonQuizAttemptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LessonQuizAttempt::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startedAt = $this->faker->dateTimeBetween('-1 month', 'now');
        $completedAt = (clone $startedAt)->modify('+' . $this->faker->numberBetween(300, 1800) . ' seconds');
        
        return [
            'quiz_id' => LessonQuiz::factory(),
            'user_id' => User::factory(),
            'score' => $this->faker->randomFloat(2, 0, 100),
            'points_earned' => $this->faker->numberBetween(0, 100),
            'max_points' => 100,
            'passed' => $this->faker->boolean(),
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
        ];
    }
}

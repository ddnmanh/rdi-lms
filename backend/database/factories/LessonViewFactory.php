<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\LessonView;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonViewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LessonView::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $watchedDuration = $this->faker->numberBetween(60, 3600);
        
        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'watched_duration' => $watchedDuration,
            'last_position' => $this->faker->numberBetween(0, $watchedDuration),
            'last_watched_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}

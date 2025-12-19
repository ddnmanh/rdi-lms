<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $prefixes = ['Bài', 'Chương', 'Phần'];
        $topics = ['Cài đặt môi trường', 'Cấu trúc thư mục', 'Routing và Controller', 'Database Migration', 'Authentication', 'Middleware', 'Deployment', 'Debug và Log'];
        
        $title = $this->faker->randomElement($prefixes) . ' ' . $this->faker->numberBetween(1, 20) . ': ' . $this->faker->randomElement($topics);

        return [
            'course_id' => Course::factory(),
            'title' => $title,
            'description' => "Trong bài học này, chúng ta sẽ tìm hiểu sâu về {$title}. Hãy chú ý các điểm quan trọng và thực hành theo.",
            'thumbnail_path' => '/static/defaults/video-not-available.jpg',
            'duration' => $this->faker->numberBetween(60, 3600),
            'video_path' => null,
            'hls_path' => null,
            'display_order' => $this->faker->numberBetween(1, 10),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}

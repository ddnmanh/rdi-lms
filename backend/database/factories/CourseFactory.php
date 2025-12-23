<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $prefixes = ['Khóa học', 'Lập trình', 'Thực chiến', 'Master', 'Bootcamp'];
        $subjects = ['Laravel', 'ReactJS', 'NodeJS', 'Python', 'DevOps', 'AWS', 'Docker', 'Machine Learning'];
        $suffixes = ['Cơ bản', 'Nâng cao', 'Toàn tập', 'Cho người mới bắt đầu', 'Trong 30 ngày'];

        $title = $this->faker->randomElement($prefixes) . ' ' . 
                 $this->faker->randomElement($subjects) . ' ' . 
                 $this->faker->randomElement($suffixes);

        return [
            'title' => $title,
            'description' => "Khóa học {$title} cung cấp kiến thức toàn diện và các dự án thực tế. Giúp bạn tự tin ứng tuyển vào các công ty công nghệ hàng đầu.",
            'thumbnail_path' => '/static/defaults/course_thumbnail.jpg',
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+2 months', '+6 months'),
            'created_by' => User::factory(),
        ];
    }
}

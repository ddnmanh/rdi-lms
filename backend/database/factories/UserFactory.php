<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ho = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý'];
        $lot = ['Văn', 'Thị', 'Đức', 'Thành', 'Minh', 'Hoàng', 'Tuấn', 'Ngọc', 'Quang', 'Thanh', 'Phương', 'Thảo', 'Hải'];
        $ten = ['Anh', 'Bình', 'Châu', 'Dũng', 'Em', 'Giang', 'Hà', 'Hải', 'Hiếu', 'Hòa', 'Huy', 'Khánh', 'Lan', 'Linh', 'Long', 'Mai', 'Minh', 'Nam', 'Nga', 'Nhi', 'Nhung', 'Phúc', 'Quân', 'Quỳnh', 'Sơn', 'Thảo', 'Thắng', 'Thủy', 'Trang', 'Tú', 'Uyên', 'Vân', 'Việt', 'Vinh', 'Yến'];

        $fullname = $this->faker->randomElement($ho) . ' ' . $this->faker->randomElement($lot) . ' ' . $this->faker->randomElement($ten);
        
        return [
            'fullname' => $fullname,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password (123123123 usually, but this hash is standard laavel 'password')
            'avatar_path' => '/static/defaults/avatar-not-available.jpg',
            'birthday' => $this->faker->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}

<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeederDev extends Seeder
{
    /**
     * Seed the application's database for development.
     * Tạo 150 users với đầy đủ các roles cho môi trường dev.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Đang tạo 150 users cho development...');

        // 1. ROOT User
        $rootUser = User::firstOrCreate(
            ['email' => 'root@lms.vn'],
            [
                'fullname' => 'Root Administrator',
                'password' => '123123123',
                'birthday' => '1990-01-01',
                'avatar_path' => '/static/defaults/avatar-not-available.jpg',
            ]
        );
        $this->assignRole($rootUser, 'ROOT');
        $this->command->info("✓ Root user: {$rootUser->email}");

        // 2. ADMIN Users (5)
        $admins = User::factory()->count(5)->create([
            'password' => '123123123',
        ]);
        foreach ($admins as $index => $admin) {
            // Update email specifically for testing consistency if needed, or just keep random
            $admin->update(['email' => 'admin' . ($index + 1) . '@lms.vn']);
            $this->assignRole($admin, 'ADMIN');
        }
        $this->command->info("✓ Đã tạo 5 Admin users");

        // 3. TEACHER Users (20)
        $teachers = User::factory()->count(20)->create([
            'password' => '123123123',
        ]);
        foreach ($teachers as $index => $teacher) {
            $teacher->update(['email' => 'teacher' . ($index + 1) . '@lms.vn']);
            $this->assignRole($teacher, 'TEACHER');
        }
        $this->command->info("✓ Đã tạo 20 Teacher users");

        // 4. STUDENT Users (124)
        $students = User::factory()->count(124)->create([
            'password' => '123123123',
        ]);
        foreach ($students as $index => $student) {
            $student->update(['email' => 'student' . ($index + 1) . '@lms.vn']);
            $this->assignRole($student, 'STUDENT');
        }
        $this->command->info("✓ Đã tạo 124 Student users");

        $this->command->info("✓ Hoàn thành! Đã tạo tổng cộng 150 users.");
    }

    private function assignRole($user, $roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role && !$user->roles()->where('roles.id', $role->id)->exists()) {
            $user->roles()->attach($role->id);
        }
    }
}


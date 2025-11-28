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

        $users = $this->generateUsers();

        foreach ($users as $userData) {
            $roles = $userData['roles'];
            unset($userData['roles']);

            // Tìm hoặc tạo user
            $user = User::withTrashed()->firstOrNew(['email' => $userData['email']]);
            
            // Chỉ cập nhật nếu user chưa tồn tại hoặc đã bị soft delete
            if (!$user->exists || $user->trashed()) {
                $user->fill($userData);
                $user->deleted_at = null;
                $user->save();
                $this->command->info("✓ Đã tạo/cập nhật user: {$user->fullname} ({$user->email})");
            } else {
                $this->command->info("✓ User đã tồn tại: {$user->fullname} ({$user->email})");
            }

            // Gán roles cho user
            foreach ($roles as $roleName) {
                $role = Role::where('name', $roleName)->first();
                
                if ($role) {
                    if (!$user->roles()->where('roles.id', $role->id)->exists()) {
                        $user->roles()->attach($role->id);
                    }
                } else {
                    $this->command->warn("  ⚠ Không tìm thấy role {$roleName}");
                }
            }
        }

        $this->command->info("✓ Hoàn thành! Đã tạo " . count($users) . " users cho development.");
    }

    /**
     * Generate 150 users with various roles
     *
     * @return array
     */
    private function generateUsers(): array
    {
        $users = [];

        // 1 ROOT user
        $users[] = [
            'email' => 'root@rdi.tvu.vn',
            'password' => '123123123',
            'fullname' => 'Root Administrator',
            'birthday' => '1990-01-01',
            'roles' => ['ROOT'],
            'avatar_path' => '/static/defaults/avatar-not-available.jpg',
        ];

        // 5 ADMIN users
        $adminNames = [
            ['Nguyễn Văn', 'Admin'],
            ['Trần Thị', 'Quản Trị'],
            ['Lê Văn', 'Hệ Thống'],
            ['Phạm Thị', 'Quản Lý'],
            ['Hoàng Văn', 'Điều Hành'],
        ];
        foreach ($adminNames as $index => $name) {
            $users[] = [
                'email' => 'admin' . ($index + 1) . '@lms.vn',
                'password' => '123123123',
                'fullname' => $name[0] . ' ' . $name[1],
                'birthday' => $this->randomDate('1985-01-01', '1995-12-31'),
                'roles' => ['ADMIN'],
                'avatar_path' => '/static/defaults/avatar-not-available.jpg',
            ];
        }

        // 20 TEACHER users
        $teacherFirstNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Vũ', 'Đỗ', 'Bùi', 'Đặng', 'Võ', 'Dương', 'Lý', 'Hồ', 'Phan', 'Vương', 'Tạ', 'Trương', 'Đinh', 'Bạch', 'Chu'];
        $teacherLastNames = ['Văn', 'Thị', 'Đức', 'Minh', 'Hùng', 'Dũng', 'Anh', 'Lan', 'Hoa', 'Mai', 'Thu', 'Hương', 'Giang', 'Linh', 'Phương', 'Hạnh', 'Nga', 'Tuyết', 'Loan', 'Yến'];
        $teacherMiddleNames = ['Giáo', 'Dạy', 'Sư', 'Phạm', 'Huấn', 'Đào', 'Tạo', 'Hướng', 'Dẫn', 'Chỉ'];
        
        for ($i = 1; $i <= 20; $i++) {
            $firstName = $teacherFirstNames[($i - 1) % count($teacherFirstNames)];
            $lastName = $teacherLastNames[($i - 1) % count($teacherLastNames)];
            $middleName = $teacherMiddleNames[($i - 1) % count($teacherMiddleNames)];
            
            $users[] = [
                'email' => 'teacher' . $i . '@lms.vn',
                'password' => '123123123',
                'fullname' => $firstName . ' ' . $lastName . ' ' . $middleName,
                'birthday' => $this->randomDate('1980-01-01', '1990-12-31'),
                'roles' => ['TEACHER'],
                'avatar_path' => '/static/defaults/avatar-not-available.jpg',
            ];
        }

        // 124 STUDENT users (tổng 150 - 1 root - 5 admin - 20 teacher = 124)
        $studentFirstNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Vũ', 'Đỗ', 'Bùi', 'Đặng', 'Võ', 'Dương', 'Lý', 'Hồ', 'Phan', 'Vương', 'Tạ', 'Trương', 'Đinh', 'Bạch', 'Chu', 'Lâm', 'Mai', 'Đào', 'Hà', 'Quách'];
        $studentLastNames = ['Văn', 'Thị', 'Đức', 'Minh', 'Hùng', 'Dũng', 'Anh', 'Lan', 'Hoa', 'Mai', 'Thu', 'Hương', 'Giang', 'Linh', 'Phương', 'Hạnh', 'Nga', 'Tuyết', 'Loan', 'Yến', 'Huy', 'Long', 'Nam', 'Bình', 'Tuấn'];
        $studentMiddleNames = ['Học', 'Sinh', 'Viên', 'Học Viên', 'Sinh Viên', 'Học Sinh', 'Tân', 'Mới', 'Trẻ', 'Trẻ Em'];
        
        for ($i = 1; $i <= 124; $i++) {
            $firstName = $studentFirstNames[($i - 1) % count($studentFirstNames)];
            $lastName = $studentLastNames[($i - 1) % count($studentLastNames)];
            $middleName = $studentMiddleNames[($i - 1) % count($studentMiddleNames)];
            
            $users[] = [
                'email' => 'student' . $i . '@lms.vn',
                'password' => '123123123',
                'fullname' => $firstName . ' ' . $lastName . ' ' . $middleName,
                'birthday' => $this->randomDate('1998-01-01', '2005-12-31'),
                'roles' => ['STUDENT'],
                'avatar_path' => '/static/defaults/avatar-not-available.jpg',
            ];
        }

        return $users;
    }

    /**
     * Generate a random date between two dates
     *
     * @param string $startDate
     * @param string $endDate
     * @return string
     */
    private function randomDate(string $startDate, string $endDate): string
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        $random = mt_rand($start, $end);
        return date('Y-m-d', $random);
    }
}


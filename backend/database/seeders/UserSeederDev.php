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

        // Danh sách tên người Việt Nam thực tế (150 tên)
        $fullnames = [
            // Admin và Teacher (25 tên)
            'Nguyễn Văn Anh', 'Trần Thị Bình', 'Lê Đức Cường', 'Phạm Thị Dung', 'Hoàng Văn Đức',
            'Huỳnh Thị Hoa', 'Phan Văn Hùng', 'Vũ Thị Lan', 'Võ Văn Long', 'Đặng Thị Mai',
            'Bùi Văn Minh', 'Đỗ Thị Nga', 'Hồ Văn Nam', 'Ngô Thị Oanh', 'Dương Văn Phúc',
            'Lý Thị Quỳnh', 'Nguyễn Văn Sơn', 'Trần Thị Thảo', 'Lê Văn Thắng', 'Phạm Thị Thủy',
            'Hoàng Văn Tuấn', 'Huỳnh Thị Uyên', 'Phan Văn Việt', 'Vũ Thị Vân', 'Võ Văn Vinh',
            // Student (125 tên)
            'Nguyễn Thị Yến', 'Trần Văn An', 'Lê Thị Bích', 'Phạm Văn Cảnh', 'Hoàng Thị Duyên',
            'Huỳnh Văn Đạt', 'Phan Thị Giang', 'Vũ Văn Hải', 'Võ Thị Hằng', 'Đặng Văn Hiếu',
            'Bùi Thị Hoa', 'Đỗ Văn Hoàng', 'Hồ Thị Hương', 'Ngô Văn Khánh', 'Dương Thị Linh',
            'Lý Văn Lộc', 'Nguyễn Thị Ly', 'Trần Văn Mạnh', 'Lê Thị Nga', 'Phạm Văn Nhật',
            'Hoàng Thị Oanh', 'Huỳnh Văn Phong', 'Phan Thị Phương', 'Vũ Văn Quang', 'Võ Thị Quyên',
            'Đặng Văn Sơn', 'Bùi Thị Tâm', 'Đỗ Văn Thanh', 'Hồ Thị Thảo', 'Ngô Văn Thành',
            'Dương Thị Thu', 'Lý Văn Tiến', 'Nguyễn Thị Trang', 'Trần Văn Trung', 'Lê Thị Tuyết',
            'Phạm Văn Tùng', 'Hoàng Thị Uyên', 'Huỳnh Văn Văn', 'Phan Thị Vân', 'Vũ Văn Việt',
            'Võ Thị Xuân', 'Đặng Văn Yên', 'Bùi Thị Ánh', 'Đỗ Văn Bảo', 'Hồ Thị Chi',
            'Ngô Văn Dũng', 'Dương Thị Dung', 'Lý Văn Đức', 'Nguyễn Thị Hạnh', 'Trần Văn Hào',
            'Lê Thị Hoa', 'Phạm Văn Huy', 'Hoàng Thị Hương', 'Huỳnh Văn Khoa', 'Phan Thị Lan',
            'Vũ Văn Lâm', 'Võ Thị Liên', 'Đặng Văn Lợi', 'Bùi Thị Mai', 'Đỗ Văn Nam',
            'Hồ Thị Nga', 'Ngô Văn Nghĩa', 'Dương Thị Nhung', 'Lý Văn Phong', 'Nguyễn Thị Phương',
            'Trần Văn Quân', 'Lê Thị Quyên', 'Phạm Văn Sơn', 'Hoàng Thị Tâm', 'Huỳnh Văn Thái',
            'Phan Thị Thanh', 'Vũ Văn Thắng', 'Võ Thị Thu', 'Đặng Văn Tiến', 'Bùi Thị Trang',
            'Đỗ Văn Trung', 'Hồ Thị Tuyết', 'Ngô Văn Tùng', 'Dương Thị Uyên', 'Lý Văn Văn',
            'Nguyễn Thị Vân', 'Trần Văn Việt', 'Lê Thị Xuân', 'Phạm Văn Yên', 'Hoàng Thị Ánh',
            'Huỳnh Văn Bảo', 'Phan Thị Chi', 'Vũ Văn Dũng', 'Võ Thị Dung', 'Đặng Văn Đức',
            'Bùi Thị Hạnh', 'Đỗ Văn Hào', 'Hồ Thị Hoa', 'Ngô Văn Huy', 'Dương Thị Hương',
            'Lý Văn Khoa', 'Nguyễn Thị Lan', 'Trần Văn Lâm', 'Lê Thị Liên', 'Phạm Văn Lợi',
            'Hoàng Thị Mai', 'Huỳnh Văn Nam', 'Phan Thị Nga', 'Vũ Văn Nghĩa', 'Võ Thị Nhung',
            'Đặng Văn Phong', 'Bùi Thị Phương', 'Đỗ Văn Quân', 'Hồ Thị Quyên', 'Ngô Văn Sơn',
            'Dương Thị Tâm', 'Lý Văn Thái', 'Nguyễn Thị Thanh', 'Trần Văn Thắng', 'Lê Thị Thu',
            'Phạm Văn Tiến', 'Hoàng Thị Trang', 'Huỳnh Văn Trung', 'Phan Thị Tuyết', 'Vũ Văn Tùng',
            'Võ Thị Uyên', 'Đặng Văn Văn', 'Bùi Thị Vân', 'Đỗ Văn Việt', 'Hồ Thị Xuân',
            'Ngô Văn Yên', 'Dương Thị Ánh', 'Lý Văn Bảo', 'Nguyễn Thị Chi', 'Trần Văn Dũng',
            'Lê Thị Dung', 'Phạm Văn Đức', 'Hoàng Thị Hạnh', 'Huỳnh Văn Hào', 'Phan Thị Hoa',
            'Vũ Văn Huy', 'Võ Thị Hương', 'Đặng Văn Khoa', 'Bùi Thị Lan', 'Đỗ Văn Lâm',
            'Hồ Thị Liên', 'Ngô Văn Lợi', 'Dương Thị Mai', 'Lý Văn Nam', 'Nguyễn Thị Nga',
            'Trần Văn Nghĩa', 'Lê Thị Nhung', 'Phạm Văn Phong', 'Hoàng Thị Phương', 'Huỳnh Văn Quân',
            'Phan Thị Quyên', 'Vũ Văn Sơn', 'Võ Thị Tâm', 'Đặng Văn Thái', 'Bùi Thị Thanh',
            'Đỗ Văn Thắng', 'Hồ Thị Thu', 'Ngô Văn Tiến', 'Dương Thị Trang', 'Lý Văn Trung',
            'Nguyễn Thị Tuyết', 'Trần Văn Tùng', 'Lê Thị Uyên', 'Phạm Văn Văn', 'Hoàng Thị Vân',
            'Huỳnh Văn Việt', 'Phan Thị Xuân', 'Vũ Văn Yên', 'Võ Thị Ánh', 'Đặng Văn Bảo',
            'Bùi Thị Chi', 'Đỗ Văn Dũng', 'Hồ Thị Dung', 'Ngô Văn Đức', 'Dương Thị Hạnh',
            'Lý Văn Hào', 'Nguyễn Thị Hoa', 'Trần Văn Huy', 'Lê Thị Hương', 'Phạm Văn Khoa',
            'Hoàng Thị Lan', 'Huỳnh Văn Lâm', 'Phan Thị Liên', 'Vũ Văn Lợi', 'Võ Thị Mai',
            'Đặng Văn Nam', 'Bùi Thị Nga', 'Đỗ Văn Nghĩa', 'Hồ Thị Nhung', 'Ngô Văn Phong',
            'Dương Thị Phương', 'Lý Văn Quân', 'Nguyễn Thị Quyên', 'Trần Văn Sơn', 'Lê Thị Tâm',
            'Phạm Văn Thái', 'Hoàng Thị Thanh', 'Huỳnh Văn Thắng', 'Phan Thị Thu', 'Vũ Văn Tiến',
            'Võ Thị Trang', 'Đặng Văn Trung', 'Bùi Thị Tuyết', 'Đỗ Văn Tùng', 'Hồ Thị Uyên',
            'Ngô Văn Văn', 'Dương Thị Vân', 'Lý Văn Việt', 'Nguyễn Thị Xuân', 'Trần Văn Yên',
        ];

        // Hàm lấy tên theo index
        $getFullname = function($index) use ($fullnames) {
            return $fullnames[$index % count($fullnames)];
        };

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

        // Danh sách ngày sinh thực tế cho Admin (1985-1990)
        $adminBirthdays = [
            '1985-03-15', '1986-07-22', '1987-11-08', '1988-05-19', '1989-09-30'
        ];

        // Danh sách ngày sinh thực tế cho Teacher (1988-1995)
        $teacherBirthdays = [
            '1988-01-10', '1988-04-25', '1989-02-14', '1989-06-18', '1989-10-05',
            '1990-03-20', '1990-07-12', '1991-01-28', '1991-05-15', '1991-09-22',
            '1992-02-08', '1992-08-30', '1993-04-17', '1993-11-03', '1994-01-19',
            '1994-06-25', '1994-12-10', '1995-03-05', '1995-08-20', '1995-10-15'
        ];

        // 2. ADMIN Users (5)
        for ($i = 1; $i <= 5; $i++) {
            $admin = User::firstOrCreate(
                ['email' => 'admin' . $i . '@lms.vn'],
                [
                    'fullname' => $getFullname($i - 1),
                    'password' => '123123123',
                    'birthday' => $adminBirthdays[($i - 1) % count($adminBirthdays)],
                    'avatar_path' => '/static/defaults/avatar-not-available.jpg',
                ]
            );
            $this->assignRole($admin, 'ADMIN');
        }
        $this->command->info("✓ Đã tạo 5 Admin users");

        // 3. TEACHER Users (20)
        for ($i = 1; $i <= 20; $i++) {
            $teacher = User::firstOrCreate(
                ['email' => 'teacher' . $i . '@lms.vn'],
                [
                    'fullname' => $getFullname($i + 4),
                    'password' => '123123123',
                    'birthday' => $teacherBirthdays[($i - 1) % count($teacherBirthdays)],
                    'avatar_path' => '/static/defaults/avatar-not-available.jpg',
                ]
            );
            $this->assignRole($teacher, 'TEACHER');
        }
        $this->command->info("✓ Đã tạo 20 Teacher users");

        // Danh sách ngày sinh thực tế cho Student (1995-2005)
        $studentBirthdays = [
            '1995-01-15', '1995-03-22', '1995-05-10', '1995-07-28', '1995-09-14',
            '1996-02-05', '1996-04-18', '1996-06-25', '1996-08-12', '1996-10-30',
            '1997-01-20', '1997-03-08', '1997-05-25', '1997-07-15', '1997-09-22',
            '1998-02-10', '1998-04-28', '1998-06-15', '1998-08-05', '1998-10-18',
            '1999-01-25', '1999-03-12', '1999-05-30', '1999-07-20', '1999-09-08',
            '2000-02-15', '2000-04-22', '2000-06-10', '2000-08-28', '2000-10-15',
            '2001-01-18', '2001-03-25', '2001-05-12', '2001-07-30', '2001-09-20',
            '2002-02-08', '2002-04-15', '2002-06-28', '2002-08-10', '2002-10-25',
            '2003-01-12', '2003-03-20', '2003-05-08', '2003-07-25', '2003-09-15',
            '2004-02-22', '2004-04-10', '2004-06-18', '2004-08-28', '2004-10-05',
            '2005-01-28', '2005-03-15', '2005-05-22', '2005-07-10', '2005-09-25',
        ];

        // 4. STUDENT Users (124)
        for ($i = 1; $i <= 124; $i++) {
            $student = User::firstOrCreate(
                ['email' => 'student' . $i . '@lms.vn'],
                [
                    'fullname' => $getFullname($i + 23),
                    'password' => '123123123',
                    'birthday' => $studentBirthdays[($i - 1) % count($studentBirthdays)],
                    'avatar_path' => '/static/defaults/avatar-not-available.jpg',
                ]
            );
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


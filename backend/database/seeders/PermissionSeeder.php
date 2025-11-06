<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Đang quét routes và thêm vào bảng permissions...');

        // Gọi command quét routes
        Artisan::call('routes:scan', [
            '--prefix' => 'api',
        ]);

        // Hiển thị output của command
        $this->command->info(Artisan::output());
    }
}

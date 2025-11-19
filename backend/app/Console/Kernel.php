<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Xóa các file chunk video cũ mỗi 2 phút
        $schedule->command('video:cleanup-chunks --hours=24')
            ->everyThreeMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        // // Xóa các file chunk video cũ mỗi 180 phút (3 giờ)
        // $schedule->command('video:cleanup-chunks --hours=24')
        //     ->everyThreeHours()
        //     ->withoutOverlapping()
        //     ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

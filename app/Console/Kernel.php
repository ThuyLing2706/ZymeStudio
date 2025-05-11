<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // Cai dat command chay theo ngay vao 00h
        $schedule->command('app:salary')->daily();
        //Neu muon cau dat de chay vao 1 thoi diem cu the thi thay bang dau * va so
        $schedule->command('app:salary')->cron('* * * * *'); // phút giờ ngày tuần tháng 20 5 * * * 5h20p hằng ngày 
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

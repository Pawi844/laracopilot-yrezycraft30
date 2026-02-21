<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    protected function schedule(Schedule $schedule): void {
        // Run expiry termination every hour
        $schedule->command('isp:terminate-expired')->hourly()->runInBackground();
        // Also run at midnight for clean daily cutoff
        $schedule->command('isp:terminate-expired')->dailyAt('00:05')->runInBackground();
    }
    protected function commands(): void {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
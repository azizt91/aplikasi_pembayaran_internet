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
        // WhatsApp reminder (existing)
        $schedule->command('send:whatsapp-reminder')->monthlyOn(15, '00:00');
        
        // Firebase Push Notification reminder - tanggal 15
        $schedule->command('notification:payment-reminder --date=15')->monthlyOn(15, '08:00');
        
        // Firebase Push Notification reminder - tanggal 20
        $schedule->command('notification:payment-reminder --date=20')->monthlyOn(20, '08:00');
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



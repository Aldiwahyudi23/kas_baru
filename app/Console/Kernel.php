<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Jalankan command setiap tanggal 5 dan 25 pada pukul 9 pagi
        $schedule->command('reminder:sendPayment')->monthlyOn(2, '19:30');
        $schedule->command('reminder:sendPayment')->monthlyOn(25, '09:00');
        $schedule->command('loan:reminder')->daily();
    }

    protected function commands()
    {
        // Daftar semua perintah yang ada
        $this->load(__DIR__ . '/Commands');
    }
}

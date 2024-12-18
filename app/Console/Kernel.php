<?php

namespace App\Console;

use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Jalankan command setiap tanggal 5 dan 25 pada pukul 9 pagi
        // Jalankan command setiap hari pada pukul 08:00
        $schedule->command('reminder:sendPayment')->dailyAt('08:00');
        $schedule->command('loan:reminder')->dailyAt('08:00');
    }

    protected function commands()
    {
        // Daftar semua perintah yang ada
        $this->load(__DIR__ . '/Commands');
    }
}
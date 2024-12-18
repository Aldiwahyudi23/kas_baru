<?php

namespace App\Console;

use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SendPaymentReminder;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule)
    {
        // Jadwalkan pengingat setiap bulan pada tanggal 5 dan 26 pukul 00:00
        $schedule->command('reminder:sendPayment')
            ->monthlyOn(5, '00:00')  // Tanggal 5 setiap bulan pukul 00:00
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping();   // Menghindari overlap

        $schedule->command('reminder:sendPayment')
            ->monthlyOn(18, '09:27') // Tanggal 26 setiap bulan pukul 00:00
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping();   // Menghindari overlap
    }

    protected function commands()
    {
        // Daftar semua perintah yang ada
        $this->load(__DIR__ . '/Commands');
    }
    protected $commands = [
        \App\Console\Commands\SendPaymentReminder::class,
    ];
}
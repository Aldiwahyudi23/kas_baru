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
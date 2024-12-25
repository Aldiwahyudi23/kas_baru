<?php

use App\Http\Middleware\AdminRedirectIfAuthenticated;
use Illuminate\Console\Scheduling\Schedule as SchedulingSchedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([

            'admin' => AdminRedirectIfAuthenticated::class,
        ]);
        $middleware->web(append: [
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \RealRashid\SweetAlert\ToSweetAlert::class,
            \App\Http\Middleware\CheckActiveStatusAdmin::class,
            \App\Http\Middleware\CheckActiveStatus::class,
            // Middleware lain
            \App\Http\Middleware\DeadlineWarningMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (SchedulingSchedule $schedule) {
        $schedule->command('reminder:sendPayment')
            ->monthlyOn(25, '06:30');  // Tanggal 5 setiap bulan pukul 00:00

        $schedule->command('reminder:sendPayment')
            ->monthlyOn(2, '06:30');  // Tanggal 5 setiap bulan pukul 00:00

        $schedule->command('reminder:loan')->dailyAt('07:00');
        $schedule->command('reminder:konter')->dailyAt('07:00');
        // Menjalanlan setiap sejam sekali
        $schedule->command('reminder:proses-loans')->hourly();
        $schedule->command('reminder:proses-payment')->hourly();
        $schedule->command('reminder:proses-setor')->hourly();
        $schedule->command('reminder:proses-income')->hourly();
        $schedule->command('reminder:proses-pengeluaran')->hourly();
        $schedule->command('reminder:proses-loan2')->hourly();
        $schedule->command('reminder:proses-payment-loan')->hourly();
        // Menjadwalkan perintah untuk berjalan setiap 10 menit
        $schedule->command('reminder:proses-konter')->everyTenMinutes();

        // $schedule->command('reminder:proses-payment-loan')->everyMinute();
    })
    ->create();

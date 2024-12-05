<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Models\Loan;
use App\Models\Konter;
use App\Models\Role;

class DeadlineWarningMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $warnings = [];

        if (Auth::check()) {
            $user = Auth::user();

            $loanWarnings = Loan::where('data_warga_id', $user->data_warga_id)->where('status', ['Acknowledged', 'In Repayment'])
                ->whereNotNull('deadline_date')
                ->where(function ($query) {
                    $query->whereDate('deadline_date', '<=', now()) // Sudah jatuh tempo
                        ->orWhereBetween('deadline_date', [now(), now()->addDays(8)]); // Akan jatuh tempo dalam 7 hari
                })
                ->get();

            $warnings = []; // Pastikan array warnings diinisialisasi

            foreach ($loanWarnings as $loan) {
                $deadlineDate = Carbon::parse($loan->deadline_date);
                $daysRemaining = now()->diffInDays($deadlineDate, false);
                $days = round($daysRemaining);
                if ($days < 0) {
                    // Sudah melewati deadline
                    $warnings[] = [
                        'type' => 'Penting !',
                        'alert' => 'alert-danger',
                        'icon' => 'fa-info',
                        'description' => "Pinjaman ID {$loan->code} - Sudah lewat deadline {$deadlineDate->format('Y-m-d')} ($days) hari yang lalu).",
                    ];
                } else {
                    // Masih ada waktu sebelum deadline
                    $warnings[] = [
                        'type' => 'Pemberitahuan !',
                        'alert' => 'alert-warning',
                        'icon' => 'fa-exclamation-triangle',
                        'description' => "Pinjaman ID {$loan->code} - Batas waktu tersisa {$days} hari (Deadline: {$deadlineDate->format('Y-m-d')}).",
                    ];
                }
            }


            // // Ambil semua konter yang hampir jatuh tempo (7 hari atau kurang) atau sudah lewat deadline
            // $konterWarnings = Konter::where('data_warga_id', $user->data_warga_id)
            //     ->whereNotNull('deadline_date')
            //     ->where(function ($query) {
            //         $query->whereDate('deadline_date', '<=', now())
            //             ->orWhereBetween('deadline_date', [now()->subDays(7), now()]);
            //     })
            //     ->get();

            // foreach ($konterWarnings as $konter) {
            //     $deadlineDate = Carbon::parse($konter->deadline_date);
            //     $daysRemaining = now()->diffInDays($deadlineDate, false);

            //     $warnings[] = [
            //         'type' => 'konter',
            //         'description' => "Konter ID {$konter->id} - Batas waktu tersisa {$daysRemaining} hari (Deadline: {$deadlineDate->format('Y-m-d')})",
            //     ];
            // }

            // Bagikan pesan hanya ke pengguna dengan role tertentu atau yang relevan
            if ($warnings) {
                View::share('globalWarnings', $warnings);
            }
        }

        return $next($request);
    }
}
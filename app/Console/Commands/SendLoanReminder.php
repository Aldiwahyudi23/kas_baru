<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendLoanReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send loan payment reminders';

    /**
     * Execute the console command.
     */

    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('FONNTE_API_KEY');
        parent::__construct(); // Memanggil constructor parent
    }


    public function handle()
    {
        $loans = Loan::where('status', '!=', 'paid in full')->get();

        foreach ($loans as $loan) {
            $dueDate = Carbon::parse($loan->created_at);
            $now = Carbon::now();

            // Kirim pengingat setiap bulan pada tanggal yang sama
            if ($now->day == $dueDate->day && $now->greaterThanOrEqualTo($dueDate)) {
                $this->sendReminder($loan, "Monthly reminder: Your payment is due on " . $dueDate->toFormattedDateString());
            }

            // Pengecekan khusus jika loan mendekati 3 bulan terakhir
            $monthsRemaining = $now->diffInMonths($dueDate);
            if ($monthsRemaining == 3) {
                // Notifikasi 2 minggu, 1 minggu, dan 3 hari sebelum jatuh tempo 3 bulan
                $weeksBeforeDue = [
                    $dueDate->subWeeks(2),
                    $dueDate->subWeeks(1),
                    $dueDate->subDays(3)
                ];

                foreach ($weeksBeforeDue as $alertDate) {
                    if ($now->isSameDay($alertDate)) {
                        $this->sendReminder($loan, "Reminder: Your payment is due in " . $alertDate->diffInDays($now) . " days!");
                    }
                }
            }
        }
    }
    private function sendReminder($loan, $message)
    {
        // Kirim notifikasi ke pengguna, misalnya lewat WhatsApp atau Email
        // Implementasikan logika pengiriman di sini
        // Contoh: WhatsApp API atau Fonnte API
        $phoneNumber = $loan->warga->no_hp; // Ganti dengan nomor tujuan
        $message = "Pengingat: Harap melakukan pembayaran Pinjaman. Terima kasih.";

        // Kirim pesan lewat API Fonnte
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->post('https://api.fonnte.com/send', [
            'target' => $phoneNumber,    // Nomor tujuan dengan kode negara, misalnya: 6281234567890
            'message' => $message,       // Pesan yang ingin dikirim
            'countryCode' => '62',       // Kode negara Indonesia
        ]);




        if ($response->successful()) {
            $this->info('Notifikasi pengingat pembayaran berhasil dikirim.');
        } else {
            $this->error('Gagal mengirim notifikasi pengingat pembayaran.');
        }
    }
}

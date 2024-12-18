<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\FonnteService;

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

    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        // Panggil constructor parent class
        parent::__construct();

        $this->fonnteService = $fonnteService;
    }

    public function handle()
    {

        $phoneNumber = '083825740395'; // Ganti dengan nomor tujuan
        $message = "Pengingat: Harap melakukan pembayaran kas bulanan. Terima kasih.";
        $image = '';

        // Kirim pesan lewat API Fonnte

        // Kirim pesan lewat FonnteService
        $response = $this->fonnteService->sendWhatsAppMessage($phoneNumber, $message, $image);

        if ($response->successful()) {
            $this->info('Notifikasi pengingat pembayaran berhasil dikirim.');
        } else {
            $this->error('Gagal mengirim notifikasi pengingat pembayaran.');
            $this->error('Response: ' . $response->body());
        }
    }
}
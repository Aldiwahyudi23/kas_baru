<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendPaymentReminder extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:sendPayment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminder notification via WhatsApp';

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
        $phoneNumber = '083825740395'; // Ganti dengan nomor tujuan
        $message = "Pengingat: Harap melakukan pembayaran kas bulanan. Terima kasih.";

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

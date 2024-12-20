<?php

namespace App\Console\Commands;

use App\Models\AccessProgram;
use App\Models\DataWarga;
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
     * API Key for Fonnte
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->apiKey = env('FONNTE_API_KEY'); // API Key dari .env
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil semua anggota yang terdaftar di program dengan program_id = 1
        $accessPrograms = AccessProgram::where('program_id', 1)->get();

        // Iterasi setiap anggota
        foreach ($accessPrograms as $access) {
            // Ambil data warga berdasarkan data_warga_id
            $dataWarga = DataWarga::find($access->data_warga_id);

            if ($dataWarga && $dataWarga->no_hp) {
                // Nomor telepon dan nama warga
                $phoneNumber = $dataWarga->no_hp;
                $namaWarga = $dataWarga->name;

                // Pesan personalisasi
                $message = "Assalamu'alaikum, {$namaWarga},\n\n";
                $message .= "Semoga Anda dan keluarga selalu dalam keadaan sehat dan bahagia. Kami ingin mengingatkan mengenai pembayaran kas bulanan untuk mendukung kelancaran program Keluarga kita.\n\n";
                $message .= "Berikut adalah informasi pembayaran:\n";
                $message .= "==============================\n";
                $message .= "Rekening Pembayaran:\n";
                $message .= "1. Bank NEO: 5859459403511164\n";
                $message .= "2. DANA: 085942004204\n";
                $message .= "A/N Rangga Mulayana\n";
                $message .= "==============================\n\n";
                $message .= "Kami sangat berterima kasih atas dukungan Anda selama ini. Setiap kontribusi Anda akan sangat berarti dalam mendukung berbagai kegiatan positif di Keluarga kita.\n\n";
                $message .= "Mohon untuk segera melakukan pembayaran.\n\n";
                $message .= "Jika Anda telah melakukan pembayaran, silakan abaikan pesan ini. Terima kasih atas perhatian dan kerjasamanya.\n\n";
                $message .= "Salam hangat,\n";
                $message .= "Kel Ma HAYA";


                // Kirim pesan lewat API Fonnte
                $response = Http::withHeaders([
                    'Authorization' => $this->apiKey,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $phoneNumber, // Nomor tujuan
                    'message' => $message,    // Pesan
                    'countryCode' => '62',    // Kode negara Indonesia
                ]);

                // Log hasil pengiriman
                if ($response->successful()) {
                    $this->info("Notifikasi berhasil dikirim ke $namaWarga ($phoneNumber).");
                } else {
                    $this->error("Gagal mengirim notifikasi ke $namaWarga ($phoneNumber).");
                }
            } else {
                $this->warn("Data warga dengan ID {$access->data_warga_id} tidak ditemukan atau nomor telepon kosong.");
            }
        }
    }
}

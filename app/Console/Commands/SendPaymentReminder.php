<?php

namespace App\Console\Commands;

use App\Models\AccessProgram;
use App\Services\FonnteService;
use Illuminate\Console\Command;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        parent::__construct();
        $this->fonnteService = $fonnteService;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil semua anggota yang terdaftar di program dengan program_id = 1
        $accessPrograms = AccessProgram::where('program_id', 1)->get();

        foreach ($accessPrograms as $data) {

            // Validasi jika Warga memiliki nomor telepon
            $phoneNumber = $data->dataWarga->no_hp ?? null;
            if (!$phoneNumber) {
                continue; // Skip jika tidak ada nomor telepon
            }

            $namaWarga = $data->dataWarga->name;

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

            // URL gambar dari direktori storage
            $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

            // Mengirim pesan WhatsApp hanya jika pengaturan memungkinkan
            try {
                // Mengirim pesan ke Pengurus
                $this->fonnteService->sendWhatsAppMessage($phoneNumber, $message, $imageUrl);
            } catch (\Exception $e) {
                \Log::error("Gagal mengirim pesan ke Pengurus: " . $e->getMessage());
            }
        }
    }
}

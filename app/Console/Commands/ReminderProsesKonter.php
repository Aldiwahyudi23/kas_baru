<?php

namespace App\Console\Commands;

use App\Models\AccessNotification;
use App\Models\DataNotification;
use App\Services\FonnteService;
use App\Models\Konter\TransaksiKonter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class ReminderProsesKonter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:proses-konter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // Ambil data pinjaman dengan status pending
        $proses = TransaksiKonter::where('status', 'Proses')->get();
        // Cek apakah ada data
        if ($proses->isNotEmpty()) {
            $notif = DataNotification::where('name', 'Konter')
                ->where('type', 'Pengajuan')
                ->first();

            // ============================Notif untuk pengurus=========================================================

            // Mengambil nomor telepon Ketua Untuk Laporan
            $notifPengurus = AccessNotification::where('notification_id', $notif->id)
                ->where('is_active', true)
                ->get();

            foreach ($notifPengurus as $notif_pengurus) {

                // Validasi jika Warga memiliki nomor telepon
                $phoneNumberPengurus = $notif_pengurus->Warga->no_hp ?? null;
                if (!$phoneNumberPengurus) {
                    continue; // Skip jika tidak ada nomor telepon
                }

                // Data untuk pesan
                $jumlah_pengajuan = $proses->count();
                $actionUrlPengurus = "https://keluargamahaya.com/konter";

                // Membuat pesan WhatsApp untuk Pengurus
                $messagePengurus = "*Pengingat : Notifikasi Pembelian Konter*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name},\n\n";
                $messagePengurus .= "Telah diterima {$jumlah_pengajuan} pengajuan Pembelian Pulsa/Listrik yang memerlukan konfirmasi Anda.\n\n";
                $messagePengurus .= "Silakan cek dan lanjutkan prosesnya melalui tautan berikut:\n";
                $messagePengurus .= "- *Link Konfirmasi*: {$actionUrlPengurus}\n\n";
                $messagePengurus .= "*Harap segera melakukan konfirmasi untuk memastikan status pembayaran.*\n\n";
                $messagePengurus .= "*Salam,*\n";
                $messagePengurus .= "*Sistem Kas Keluarga*\n\n";

                // URL gambar dari direktori storage
                $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

                // Mengirim pesan WhatsApp hanya jika pengaturan memungkinkan
                if ($notif->wa_notification && $notif->pengurus) {
                    try {
                        // Mengirim pesan ke Pengurus
                        $this->fonnteService->sendWhatsAppMessage($phoneNumberPengurus, $messagePengurus, $imageUrl);
                    } catch (\Exception $e) {
                        \Log::error("Gagal mengirim pesan ke Pengurus: " . $e->getMessage());
                    }
                }
            }
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use Illuminate\Console\Command;
use App\Models\AccessNotification;
use App\Models\DataNotification;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Crypt;

class ReminderProsesSetor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:proses-setor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */    protected $fonnteService;

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
        $proses = Deposit::where('status', 'pending')->get();
        foreach ($proses as $data) {
            $notif = DataNotification::where('name', 'Setor Tunai')
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
                $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
                $link = "https://keluargamahaya.com/setor-tunais/pengajuan/show/{$encryptedId}";

                // Membuat pesan WhatsApp
                $messagePengurus = "*Pengingat : Persetujuan Setor Tunai*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name},\n\n";
                $messagePengurus .= "Terdapat pengajuan Setor Tunai yang sudah di input, Perlu di Konfirmasi agar masuk data. Berikut detail pengajuannya:\n\n";
                $messagePengurus .= "- *Kode* : {$data->code}\n";
                $messagePengurus .= "- *Tanggal Deposit* : {$data->created_at}\n";
                $messagePengurus .= "- *Type* : Setor Tunai\n";
                $messagePengurus .= "- *Penyetor* : {$data->submit->name}\n";
                $messagePengurus .= "- *Nominal* : Rp" . number_format($data->amount, 0, ',', '.') . "\n\n";
                $messagePengurus .= "Silakan klik link berikut untuk memberikan persetujuan:\n";
                $messagePengurus .= $link . "\n\n";
                $messagePengurus .= "*Salam hormat,*\n";
                $messagePengurus .= "*Sistem Kas Keluarga*";

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

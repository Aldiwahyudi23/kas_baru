<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AccessNotification;
use App\Models\DataNotification;
use App\Models\LoanExtension;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Crypt;


class ReminderProsesLoan2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:proses-loan2';

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
        // Ambil data pinjaman dengan status pending
        $proses = LoanExtension::where('status', 'pending')->get();
        foreach ($proses as $data) {
            $notif = DataNotification::where('name', 'Pinjaman ke 2')
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

                $encryptedIdpengurus = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
                $actionUrlPengurus = "https://keluargamahaya.com/confirm/pinjaman-ke-2/{$encryptedIdpengurus}";

                // Pesan untuk Ketua
                $messagePengurus = "*Pengingat*\n";
                $messagePengurus .= "*Pemberitahuan Pengajuan Perpanjangan Pinjaman atau Pinjaman Kedua*\n\n";
                $messagePengurus .= "Halo *{$notif_pengurus->Warga->name}*,\n";
                $messagePengurus .= "Terdapat pengajuan baru yang membutuhkan persetujuan Anda. Berikut adalah detail pengajuan:\n\n";
                $messagePengurus .= "📝 *Detail Pengajuan* :\n";
                $messagePengurus .= "- *Kode Pinjaman* : {$data->pinjaman->code}\n";
                $messagePengurus .= "- *Tanggal Pengajuan* : {$data->extension_date}\n";
                $messagePengurus .= "- *Nama Warga* : {$data->pinjaman->warga->name}\n";
                $messagePengurus .= "- *Di Ajukan Oleh* : {$data->data_warga->name}\n";
                $messagePengurus .= "- *Nominal Pinjaman Awal* : Rp" . number_format($data->pinjaman->loan_amount, 0, ',', '.') . "\n";
                $messagePengurus .= "- *Sisa Pinjaman Saat Ini* : Rp" . number_format($data->pinjaman->remaining_balance, 0, ',', '.') . "\n";
                $messagePengurus .= "- *Alasan Pengajuan* : {$data->reason}\n\n";
                $messagePengurus .= "Mohon untuk segera memproses pengajuan ini sesuai prosedur yang berlaku.\n";
                $messagePengurus .= "- *Link Konfirmasi* : " . $actionUrlPengurus . "\n\n";
                $messagePengurus .= "*Terima kasih atas perhatian dan kerja sama Anda!*\n\n";
                $messagePengurus .= "*Salam hormat,*\n";
                $messagePengurus .= "*Pengurus Kas Keluarga*";

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

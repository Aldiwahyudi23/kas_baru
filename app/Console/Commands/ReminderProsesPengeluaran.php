<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AccessNotification;
use App\Models\CashExpenditures;
use App\Models\DataNotification;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Crypt;

class ReminderProsesPengeluaran extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:proses-pengeluaran';

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
        $proses = CashExpenditures::where('status', 'approved_by_chairman')->get();
        foreach ($proses as $data) {
            $notif = DataNotification::where('name', 'Pengeluaran')
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
                $link = "https://keluargamahaya.com/confirm/pengeluaran/{$encryptedId}";

                // Membuat pesan WhatsApp
                $messagePengurus = "*Pengingat : Persetujuan Pengeluaran Anggaran Diperlukan*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name},\n\n";
                $messagePengurus .= "Terdapat pengajuan Pengeluaran anggaran yang memerlukan persetujuan Anda sebelum dapat dicairkan oleh Bendahara. Berikut detail pengajuannya:\n\n";
                $messagePengurus .= "- *Kode Anggaran*: {$data->code}\n";
                $messagePengurus .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
                $messagePengurus .= "- *Nama Anggaran*: {$data->anggaran->name}\n";
                $messagePengurus .= "- *Di Input*: {$data->sekretaris->name}\n";
                $messagePengurus .= "- *Nominal*: Rp" . number_format($data->amount, 0, ',', '.') . "\n\n";
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

        // Ambil data pinjaman dengan status pending
        $proses = CashExpenditures::where('status', 'disbursed_by_treasurer')->get();
        foreach ($proses as $data) {
            $notif = DataNotification::where('name', 'Pengeluaran')
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
                $link = "https://keluargamahaya.com/confirm/pengeluaran/{$encryptedId}";

                // Membuat pesan WhatsApp
                $messageBendahara = "*Pengingat : Pengajuan Pengeluaran Anggaran Disetujui*\n";
                $messageBendahara .= "Halo {$notif_pengurus->Warga->name},\n\n";
                $messageBendahara .= "Pengajuan Pengeluaran anggaran berikut telah disetujui oleh {$data->ketua->name} dan sekarang dapat dilanjutkan ke tahap pencairan:\n\n";
                $messageBendahara .= "- *Kode Anggaran*: {$data->code}\n";
                $messageBendahara .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
                $messageBendahara .= "- *Nama Anggaran*: {$data->anggaran->name}\n";
                $messageBendahara .= "- *Di Input*: {$data->sekretaris->name}\n";
                $messageBendahara .= "- *Nominal*: Rp" . number_format($data->amount, 0, ',', '.') . "\n\n";
                $messageBendahara .= "- *Di Konformasi*: {$data->ketua->name}\n";
                $messageBendahara .= "- *Pada Tanggal*: {$data->approved_date}\n\n";
                $messageBendahara .= "Silakan klik link berikut untuk memberikan persetujuan:\n";
                $messageBendahara .= $link . "\n\n";
                $messageBendahara .= "*Salam hormat,*\n";
                $messageBendahara .= "*Sistem Kas Keluarga*";

                // URL gambar dari direktori storage
                $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

                // Mengirim pesan WhatsApp hanya jika pengaturan memungkinkan
                if ($notif->wa_notification && $notif->pengurus) {
                    try {
                        // Mengirim pesan ke Pengurus
                        $this->fonnteService->sendWhatsAppMessage($phoneNumberPengurus, $messageBendahara, $imageUrl);
                    } catch (\Exception $e) {
                        \Log::error("Gagal mengirim pesan ke Pengurus: " . $e->getMessage());
                    }
                }
            }
        }
    }
}

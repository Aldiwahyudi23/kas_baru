<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AccessNotification;
use App\Models\DataNotification;
use App\Models\loanRepayment;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Crypt;

class ReminderProsesPaymentLoan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:proses-payment-loan';

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
        $proses = loanRepayment::where('status', 'process')->get();
        foreach ($proses as $data) {
            $notif = DataNotification::where('name', 'Bayar Pinjaman')
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
                $actionUrlPengurus = "https://keluargamahaya.com/confirm/bayar-pinjaman/{$encryptedIdpengurus}";

                // Pesan untuk Pengurus
                $messagePengurus = "*Pengingat : Notifikasi Pembayaran Pinjaman Baru*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name}.\n\n";
                $messagePengurus .= "Telah diterima pembayaran pinjaman yang memerlukan konfirmasi Anda.\n\n";
                $messagePengurus .= "Berikut adalah detail pembayaran:\n";
                $messagePengurus .= "- *Kode*: {$data->code}\n";
                $messagePengurus .= "- *Tanggal Pembayaran*: {$data->payment_date}\n";
                $messagePengurus .= "- *Nama*: {$data->data_warga->name}\n";
                $messagePengurus .= "- *Di Input*: {$data->submitted->name}\n";
                $messagePengurus .= "- *Nominal*: Rp" . number_format($data->amount, 0, ',', '.') . "\n";
                $messagePengurus .= "- *Keterangan*: {$data->description}\n\n";
                $messagePengurus .= "Silakan cek dan konfirmasi pembayaran ini melalui link berikut:\n";
                $messagePengurus .= "- *Link Konfirmasi*: " . $actionUrlPengurus . "\n\n";
                $messagePengurus .= "*Harap segera melakukan konfirmasi untuk memastikan status pembayaran.*\n\n";
                $messagePengurus .= "*Salam,*\n";
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
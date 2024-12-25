<?php

namespace App\Console\Commands;

use App\Models\AccessNotification;
use App\Models\DataNotification;
use App\Models\DataWarga;
use App\Models\Loan;
use App\Services\FonnteService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class ReminderProsesLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:proses-loans';

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
        $pendingLoans = Loan::where('status', 'pending')->get();
        foreach ($pendingLoans as $data) {
            $notif = DataNotification::where('name', 'Pinjaman')
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
                $link = "https://keluargamahaya.com/confirm/pinjaman/{$encryptedId}";

                // Membuat pesan WhatsApp
                $messagePengurus = "*Pengingat: Pengajuan Pinjaman Menunggu Persetujuan*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name},\n\n";
                $messagePengurus .= "Kami informasikan bahwa ada pengajuan pinjaman yang membutuhkan persetujuan segera untuk dapat melanjutkan ke proses berikutnya. Berikut detail pengajuan:\n\n";
                $messagePengurus .= "- *Kode Pinjaman*: {$data->code}\n";
                $messagePengurus .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
                $messagePengurus .= "- *Nama Anggaran*: {$data->anggaran->name}\n";
                $messagePengurus .= "- *Di Input oleh*: {$data->sekretaris->name}\n";
                $messagePengurus .= "- *Nominal*: Rp" . number_format($data->loan_amount, 0, ',', '.') . "\n\n";
                $messagePengurus .= "Silakan segera konfirmasi untuk melanjutkan proses pinjaman:\n";
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


        // Ambil data pinjaman yang disetujui oleh Ketua
        $approvedLoans = Loan::where('status', 'approved_by_chairman')->get();
        foreach ($approvedLoans as $data) {
            $notif = DataNotification::where('name', 'Pinjaman')
                ->where('type', 'Konfirmasi')
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
                $link = "https://keluargamahaya.com/confirm/pinjaman/{$encryptedId}";

                // Membuat pesan WhatsApp untuk Bendahara
                $messageBendahara = "*Pengingat: Pengajuan Pinjaman Menunggu Pencairan*\n";
                $messageBendahara .= "Halo {$notif_pengurus->Warga->name},\n\n";
                $messageBendahara .= "Kami informasikan bahwa pengajuan pinjaman berikut telah disetujui oleh {$data->ketua->name} dan menunggu pencairan oleh bendahara. Berikut detail pengajuan:\n\n";
                $messageBendahara .= "- *Kode Pinjaman*: {$data->code}\n";
                $messageBendahara .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
                $messageBendahara .= "- *Nama Anggaran*: {$data->anggaran->name}\n";
                $messageBendahara .= "- *Di Input oleh*: {$data->sekretaris->name}\n";
                $messageBendahara .= "- *Nominal*: Rp" . number_format($data->loan_amount, 0, ',', '.') . "\n";
                $messageBendahara .= "- *Disetujui oleh*: {$data->ketua->name}\n";
                $messageBendahara .= "- *Tanggal Persetujuan*: {$data->approved_date}\n\n";
                $messageBendahara .= "Silakan segera lakukan pencairan untuk melanjutkan proses pinjaman:\n";
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


        // Ambil data pinjaman yang telah dicairkan oleh Bendahara
        $disbursedLoans = Loan::where('status', 'disbursed_by_treasurer')->get();
        foreach ($disbursedLoans as $data) {
            // -------------------------------------
            $notif = DataNotification::where('name', 'Pinjaman')
                ->where('type', 'Pencairan')
                ->first();

            // ==========================Notif Anggota=======================================

            // Data Warga
            $data_warga = DataWarga::find($data->data_warga_id);
            $phoneNumberWarga = $data_warga->no_hp;
            // URL gambar dari direktori storage
            $imageUrl = '';
            $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            $link = "https://keluargamahaya.com/pinjaman/{$encryptedId}";

            // Pesan untuk Warga
            $messageWarga = "*Pengingat: Pinjaman Anda Telah Dicairkan*\n";
            $messageWarga .= "Halo {$data_warga->name},\n\n";
            $messageWarga .= "Kami informasikan bahwa pengajuan pinjaman Anda telah disetujui dan dana telah dicairkan oleh bendahara {$data->bendahara->name}. Berikut adalah detailnya:\n\n";
            $messageWarga .= "- *Kode Pinjaman*: {$data->code}\n";
            $messageWarga .= "- *Tanggal Pencairan*: {$data->disbursed_date}\n";
            $messageWarga .= "- *Nama Peminjam*: {$data_warga->name}\n";
            $messageWarga .= "- *Nominal*: Rp" . number_format($data->loan_amount, 0, ',', '.') . "\n";
            $messageWarga .= "- *Jatuh Tempo*: {$data->deadline_date}\n\n";
            $messageWarga .= "Mohon segera cek saldo di rekening Anda untuk memastikan dana telah masuk atau ambil sesuai kesepakatan.\n\n";
            $messageWarga .= "Setelah menerima dana, harap segera konfirmasi bahwa uang telah diterima dengan menghubungi kami melalui sistem atau langsung kepada pengurus. Konfirmasi ini penting untuk melanjutkan proses administrasi.\n\n";
            $messageWarga .= $link . "\n";
            $messageWarga .= "Terima kasih atas perhatian Anda.\n\n";
            $messageWarga .= "*Salam hormat,*\n";
            $messageWarga .= "*Sistem Kas Keluarga*";
        }
        if ($notif->wa_notification  && $notif->anggota) {
            $this->fonnteService->sendWhatsAppMessage($phoneNumberWarga, $messageWarga, $imageUrl);
        }
    }
}

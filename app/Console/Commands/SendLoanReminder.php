<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\loanRepayment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Crypt;

class SendLoanReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:loan';

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
        parent::__construct();
        $this->fonnteService = $fonnteService;
    }


    public function handle()
    {
        // Ambil data pinjaman dengan status 'Diterima' dan deadline_date valid
        $loans = Loan::whereIn('status', ['Acknowledged', 'In Repayment'])
            ->get();

        foreach ($loans as $loan) {
            // Menghitung hari pinjaman dari awal pinjaman sampai sekarang
            $waktuSekarang = Carbon::now();
            $jatuhTempo = Carbon::parse($loan->deadline_date);
            $daysElapsed = $waktuSekarang->diffInDays($jatuhTempo, false); //mengambil data yang di hitung hari
            $sisaWaktu = round($daysElapsed); //membulatkan hasil

            // Kirim notifikasi berdasarkan sisa waktu yang ditentukan
            if (in_array($sisaWaktu, [60, 30, 14, 7, 3, 2, 1, 0, -1, -3, -7, -10, -14, -17, -21, -25, -30])) {
                $this->sendReminder($loan, $sisaWaktu);
            }
        }
    }

    private function sendReminder($loan, $sisaWaktu)
    {
        $phoneNumber = $loan->warga->no_hp; // Asumsikan ada relasi 'warga' ke peminjam
        $namaPeminjam = $loan->warga->name;
        $pembayarans = loanRepayment::where('loan_id', $loan->id)->sum('amount');
        if ($pembayarans) {
            $pembayaran = "Rp " . number_format($pembayarans, 0, ',', '.');
        } else {
            $pembayaran = "Belum ada pembayaran";
        }
        $nominalPinjaman = number_format($loan->loan_amount, 0, ',', '.');
        $sisa =  number_format($loan->remaining_balance, 0, ',', '.');
        $lebih = number_format($loan->overpayment_balance, 0, ',', '.');
        $jatuhTempo = Carbon::parse($loan->deadline_date)->format('d M Y');
        // Data untuk pesan
        $encryptedId = Crypt::encrypt($loan->id); // Mengenkripsi ID untuk keamanan
        $link = "https://keluargamahaya.com/pembayaran/bayar-pinjaman/{$encryptedId}";

        // Tentukan pesan berdasarkan sisa waktu
        if ($sisaWaktu > 0) {
            $pesan = "Halo {$namaPeminjam},\n\n";
            $pesan .= "Ini adalah pengingat untuk pembayaran pinjaman Anda sejumlah *Rp {$nominalPinjaman}*.\n\n";
            $pesan .= "- *Jatuh tempo* : {$jatuhTempo}.\n";
            $pesan .= "- *Sisa waktu Anda* : {$sisaWaktu} hari lagi.\n";
            $pesan .= "- *Sisa Pinjaman* : Rp {$sisa} .\n\n";
            $pesan .= "*Pembayaran Masuk* : {$pembayaran} .\n\n";
            $pesan .= "Mohon segera lakukan pembayaran.\n\n";
            $pesan .= "*Link Pembayaran*\n";
            $pesan .= $link . "\n\n";
            $pesan .= "Terima kasih atas perhatian Anda.";
        } elseif ($sisaWaktu == 0) {
            $pesan = "Halo {$namaPeminjam},\n\n";
            $pesan .= "Hari ini adalah *JATUH TEMPO* pinjaman Anda sejumlah *Rp {$nominalPinjaman}*.\n\n";
            $pesan .= "Mohon segera lakukan pembayaran.\n\n";
            $pesan .= "- *Nominal Pinjaman* : Rp {$nominalPinjaman} .\n";
            $pesan .= "- *Sisa Pinjaman* : Rp {$sisa} .\n\n";
            $pesan .= "*Pembayaran Masuk* : {$pembayaran} .\n\n";
            $pesan .= "*Link Pembayaran*\n";
            $pesan .= $link . "\n\n";
            $pesan .= "Terima kasih atas perhatian Anda.";
        } else {
            $pesan = "Halo {$namaPeminjam},\n\n";
            $pesan .= "Pinjaman Anda sejumlah *Rp {$nominalPinjaman}* telah melewati jatuh tempo pada tanggal {$jatuhTempo}.\n\n";
            $pesan .= "Mohon segera lakukan pembayaran.\n\n";
            $pesan .= "- *Nominal Pinjaman* : Rp {$nominalPinjaman} .\n";
            $pesan .= "- *Sisa Pinjaman* : Rp {$sisa} .\n\n";
            $pesan .= "*Pembayaran Masuk* : {$pembayaran} .\n\n";
            $pesan .= "*Link Pembayaran*\n";
            $pesan .= $link . "\n\n";
            $pesan .= "Terima kasih atas perhatian Anda.";
        }

        $imageUrl = "";

        $this->fonnteService->sendWhatsAppMessage($phoneNumber, $pesan, $imageUrl);
    }
}

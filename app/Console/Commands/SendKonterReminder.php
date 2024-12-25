<?php

namespace App\Console\Commands;

use App\Models\Konter\TransaksiKonter;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendKonterReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:konter';

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

    public function handle()
    {
        // Ambil data pinjaman dengan status 'Diterima' dan deadline_date valid
        $konters = TransaksiKonter::where('status', 'Berhasil')
            ->where('payment_status', 'Hutang')
            ->get();

        foreach ($konters as $konter) {
            // Menghitung hari pinjaman dari awal pinjaman sampai sekarang
            $waktuSekarang = Carbon::now();
            $jatuhTempo = Carbon::parse($konter->deadline_date);
            $daysElapsed = $waktuSekarang->diffInDays($jatuhTempo, false); //mengambil data yang di hitung hari
            $sisaWaktu = round($daysElapsed); //membulatkan hasil

            // Kirim notifikasi berdasarkan sisa waktu yang ditentukan
            if (in_array($sisaWaktu, [1, 0, -1, -3, -7, -10, -14, -17, -21, -25, -30])) {
                $this->sendReminder($konter, $sisaWaktu);
            }
        }
    }

    private function sendReminder($konter, $sisaWaktu)
    {
        $phoneNumber = $konter->detail->no_hp; // Asumsikan ada relasi 'warga' ke peminjam
        $nama = $konter->detail->name;
        $nominal = number_format($konter->product->amount, 0, ',', '.');
        $tagihan = number_format($konter->invoice, 0, ',', '.');
        $kategori = $konter->product->kategori->name . "( " . $konter->product->provider->name . " )";
        $jatuhTempo = Carbon::parse($konter->deadline_date)->format('d M Y');

        // Tentukan pesan berdasarkan sisa waktu
        if ($sisaWaktu > 0) {
            $pesan = "Halo {$nama},\n\n";
            $pesan .= "Pembelian {$kategori} sejumlah *Rp {$nominal}*.\n\n";
            $pesan .= "- *Jatuh tempo* : {$jatuhTempo}.\n";
            $pesan .= "- *Sisa waktu Anda* : {$sisaWaktu} hari lagi.\n";
            $pesan .= "- *Yang di Input* : {$konter->submitted_by} pada {$konter->created_at}.\n";
            $pesan .= "- *Tagihan* : Rp {$tagihan} .\n\n";
            if ($konter->product->kategori->name == "Listrik") {
                $pesan .= "- *No Meteran*: {$konter->detail->no_listrik}\n";
                $pesan .= "- *No TOKEN*: *{$konter->detail->token_code}* \n";
                $pesan .= "- *A/N*: {$konter->detail->name}\n\n";
            }
            $pesan .= "Mohon segera lakukan pembayaran.\n\n";
            $pesan .= "Terima kasih atas perhatian Anda.";
        } elseif ($sisaWaktu == 0) {
            $pesan = "Halo {$nama},\n\n";
            $pesan .= "Hari ini adalah *JATUH TEMPO* Pembelian {$kategori} sejumlah *Rp {$nominal}*.\n\n";
            $pesan .= "Mohon segera lakukan pembayaran.\n\n";
            $pesan .= "- *Nominal* : Rp {$nominal} .\n";
            $pesan .= "- *Yang di Input* : {$konter->submitted_by} pada {$konter->created_at}.\n";
            $pesan .= "- *Tagihan* : Rp {$tagihan} .\n\n";
            if ($konter->product->kategori->name == "Listrik") {
                $pesan .= "- *No Meteran*: {$konter->detail->no_listrik}\n";
                $pesan .= "- *No TOKEN*: *{$konter->detail->token_code}* \n";
                $pesan .= "- *A/N*: {$konter->detail->name}\n\n";
            }
            $pesan .= "Terima kasih atas perhatian Anda.";
        } else {
            $pesan = "Halo {$nama},\n\n";
            $pesan .= "Pembelian {$kategori} sejumlah *Rp {$nominal}* telah melewati jatuh tempo pada tanggal {$jatuhTempo}.\n\n";
            $pesan .= "Mohon segera lakukan pembayaran.\n\n";
            $pesan .= "- *Nominal* : Rp {$nominal} .\n";
            $pesan .= "- *Yang di Input* : {$konter->submitted_by} pada {$konter->created_at}.\n";
            $pesan .= "- *Tagihan* : Rp {$tagihan} .\n\n";
            if ($konter->product->kategori->name == "Listrik") {
                $pesan .= "- *No Meteran*: {$konter->detail->no_listrik}\n";
                $pesan .= "- *No TOKEN*: *{$konter->detail->token_code}* \n";
                $pesan .= "- *A/N*: {$konter->detail->name}\n\n";
            }
            $pesan .= "Terima kasih atas perhatian Anda.";
        }
        $imageUrl = "";
        $this->fonnteService->sendWhatsAppMessage($phoneNumber, $pesan, $imageUrl);
    }
}

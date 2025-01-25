<?php

namespace App\Console\Commands;

use App\Models\KasPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

use App\Mail\MonthlyReportMail;
use App\Mail\ReportMail;
use App\Models\AnggaranSaldo;
use App\Models\CashExpenditures;
use App\Models\Konter\TransaksiKonter;
use App\Models\Loan;
use App\Models\OtherIncomes;
use App\Models\Saldo;

class MonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monthly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        // Ambil data dari database

        $name = "Aldi Wahyudi";   // Nama warga
        // $email = "aldiwahyudi1223@gmail.com";   // Nama warga
        $email = "miraulpah0110@gmail.com";   // Nama warga

        // Ambil data KasPayment untuk bulan ini
        $kasPayments = KasPayment::whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->where('data_warga_id', 1)
            ->get();

        // Ambil data Tagihan Pinjaman User
        $loans = Loan::whereIn('status',  ['Acknowledged', 'In Repayment'])
            ->where('submitted_by', 1)
            ->get();


        // Ambil data Tagihan Konter
        $konters = TransaksiKonter::where('status', 'Berhasil')
            ->where('submitted_by', $name)
            ->get();



        // Data KasPayment
        $data_kas = KasPayment::whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->where('status', 'confirmed')
            ->get();
        $kasPaymentsTotal = $data_kas->sum('amount');

        // Data CashExpenditure
        $cashExpenditures = CashExpenditures::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'Acknowledged')
            ->get();
        $cashExpendituresTotal = $cashExpenditures->sum('amount');

        // Data OtherIncome
        $otherIncomes = OtherIncomes::whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->where('status', 'confirmed')
            ->get();
        $otherIncomesTotal = $otherIncomes->sum('amount');

        // Ambil data Tagihan Pinjaman
        $Data_loan = Loan::whereIn('status',  ['Acknowledged', 'In Repayment'])
            ->get();

        $totalRemainingBalance = $Data_loan->sum('remaining_balance');
        $totalOverPaymentBalance = Loan::where('status',  'Paid in Full')->sum('overpayment_balance');

        $data_konter = TransaksiKonter::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'Selesai')
            ->get();

        $totalMargin = $data_konter->sum('margin');
        $totalDiskon = $data_konter->sum('diskon');
        $totalKonter = $totalMargin + $totalDiskon;


        // Hitung Saldo dan AnggaranSaldo
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();
        // Mengambil data saldo untuk akhir bulan lalu
        $saldo_bulan_lalu = Saldo::whereMonth('created_at', $endOfLastMonth)
            ->orderBy('created_at', 'desc')  // Mengurutkan berdasarkan created_at, terbaru di atas
            ->first();  // Mengambil hanya satu data terakhir
        //mengambil saldo yang terbaru
        $saldoTotal = Saldo::latest()->first();
        $data_saldo = Saldo::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)->get();

        $saldoKas = AnggaranSaldo::where('type', 'Dana Kas')->latest()->first();
        $saldoDarurat = AnggaranSaldo::where('type', 'Dana Darurat')->latest()->first();
        $saldoAmal = AnggaranSaldo::where('type', 'Dana Amal')->latest()->first();
        $saldoPinjam = AnggaranSaldo::where('type', 'Dana Pinjam')->latest()->first();

        $message = "*Halo {$name},*\n\n";
        $message .= "Berikut adalah laporan keuangan Anda untuk bulan *" . Carbon::now()->translatedFormat('F Y') . "*.\n";
        $message .= "Laporan ini hanya mencakup transaksi yang terjadi selama bulan ini.\n\n";

        $message .= "========================\n";
        $message .= "*🔔 Pembayaran Kas Bulanan*\n";
        if ($kasPayments->isNotEmpty()) {
            $message .= "Anda telah melakukan pembayaran berikut:\n";
            foreach ($kasPayments as $payment) {
                $message .= "- Rp " . number_format($payment->amount, 0, ',', '.') . "\n";
            }
        } else {
            $message .= "Sayangnya, Anda belum melakukan pembayaran Kas di bulan ini.\n";
        }
        $message .= "\n";

        $message .= "*📋 Tagihan Pinjaman*\n";
        if ($loans->isNotEmpty()) {
            $message .= "Berikut adalah daftar pinjaman yang belum lunas:\n";
            foreach ($loans as $loan) {
                $deadlineLoan = round(Carbon::now()->diffInDays(Carbon::parse($loan->deadline_date), false));
                $message .= "- " . $loan->warga->name . " : Rp " . number_format($loan->remaining_balance, 0, ',', '.') . "          " . $deadlineLoan . " hari lagi\n";
            }
        } else {
            $message .= "Selamat! Anda tidak memiliki tagihan pinjaman bulan ini.\n";
        }
        $message .= "\n";

        $message .= "*💳 Tagihan Konter*\n";
        if ($konters->isNotEmpty()) {
            $message .= "Tagihan konter Anda yang belum lunas adalah:\n";
            foreach ($konters as $konter) {
                $deadlineKonter = round(Carbon::now()->diffInDays(Carbon::parse($konter->deadline_date), false));
                $message .= "- " . $konter->product->kategori->name . " (" . $konter->product->provider->name . "): Rp " . number_format($konter->invoice, 0, ',', '.')
                    . "          " . $deadlineKonter . " hari lagi\n";
            }
        } else {
            $message .= "Tidak ada tagihan konter bulan ini. Good job! 🎉\n";
        }
        $message .= "\n";

        $message .= "========================\n";
        $message .= "*📊 Ringkasan Keuangan Bulan Ini*\n";
        $message .= "- Total Kas: *Rp " . number_format($kasPaymentsTotal, 0, ',', '.') . "*\n";
        $message .= "- Total Pemasukan Lain-Lain: *Rp " . number_format($otherIncomesTotal, 0, ',', '.') . "*\n";
        $message .= "- Total Pengeluaran: *Rp " . number_format($cashExpendituresTotal, 0, ',', '.') . "*\n";
        $message .= "- Total Uang yang Masih Dipinjam: *Rp " . number_format($totalRemainingBalance, 0, ',', '.') . "*\n";
        $message .= "- Total Keuntungan Konter: *Rp " . number_format($totalKonter, 0, ',', '.') . "*\n\n";

        $message .= "========================\n";
        $message .= "*💰 Saldo Keseluruhan*\n";
        $message .= "Berikut adalah rincian saldo Anda:\n";
        $message .= " *Saldo Total: Rp " . number_format($saldoTotal->total_balance, 0, ',', '.') . "*\n";
        $message .= "- Saldo Kas: Rp " . number_format($saldoKas->saldo, 0, ',', '.') . "\n";
        $message .= "- Saldo Pinjaman: Rp " . number_format($saldoPinjam->saldo, 0, ',', '.') . "\n";
        $message .= "- Saldo Darurat: Rp " . number_format($saldoDarurat->saldo, 0, ',', '.') . "\n";
        $message .= "- Saldo Amal: Rp " . number_format($saldoAmal->saldo, 0, ',', '.') . "\n";
        $message .= "========================\n";

        $message .= "\nTerima kasih, Jangan ragu untuk menghubungi kami jika ada pertanyaan. 😊";

        // Untuk mengirim email
        // Untuk mengirim email
        $recipientEmailPengurus = $email;
        $recipientNamePengurus = "Aldi wahyudi";
        $status = "Selesai";
        // Data untuk email pengurus
        $bodyMessagePengurus = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $message);
        $actionUrlPengurus = "http://keluargamahaya.com";

        // Generate konten PDF
        $pdf = Pdf::loadView('reports.monthly', compact(
            'data_saldo',
            'data_kas',
            'Data_loan',
            'kasPayments',
            'loans',
            'konters',
            'kasPaymentsTotal',
            'otherIncomesTotal',
            'cashExpendituresTotal',
            'totalRemainingBalance',
            'totalOverPaymentBalance',
            'totalKonter',
            'saldoTotal',
            'saldoKas',
            'saldoPinjam',
            'saldoDarurat',
            'saldoAmal',
            'currentMonth',
            'currentYear',
            'saldo_bulan_lalu',
            'name'
        ));

        // Simpan PDF ke storage
        $fileName = 'laporan_' . strtolower(Carbon::now()->format('FY')) . '.pdf';
        $filePath = public_path('laporan/' . $fileName);
        $pdf->save($filePath); // Simpan file PDF


        Mail::to($recipientEmailPengurus)->send(new ReportMail($recipientNamePengurus, $bodyMessagePengurus, $status, $actionUrlPengurus, $filePath, $fileName));
        // Hapus file setelah email terkirim
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $this->info('Laporan bulanan berhasil dikirim!');
    }
}

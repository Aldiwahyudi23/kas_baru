<?php

namespace App\Http\Controllers\User\Kas;

use App\Http\Controllers\Controller;
use App\Mail\Notification;
use App\Models\Anggaran;
use App\Models\DataWarga;
use App\Models\Loan;
use App\Models\LoanExtension;
use App\Models\loanRepayment;
use App\Models\User;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LoanExtensionController extends Controller
{
    protected $fonnteService;
    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pinjaman = LoanExtension::where('status', 'pending')->get();
        $pinjaman_reject = LoanExtension::where('status', 'rejected')->get();

        return view('user.program.kas.pengajuan.pinjamanKeDua', compact('pinjaman', 'pinjaman_reject'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'loan_id' => 'required',
            'data_warga_id' => 'required',
            'loan_amount' => 'required',
            'remaining_balance' => 'required',
            'overpayment_balance' => 'required',
            'status' => 'required',
            'reason' => 'required',
        ]);

        $pinjaman = Loan::findOrFail($request->loan_id);
        $cek_pengajuan = LoanExtension::where('loan_id', $pinjaman->id)->where('status', 'pending')->latest('created_at')->first();
        $cek_pembayaran = loanRepayment::where('loan_id', $pinjaman->id)->sum('amount');
        // Ambil nilai catatan_anggaran dari tabel anggaran_settings untuk menentukan deadline
        $anggaranSetting = DB::table('anggaran_settings')
            ->where('label_anggaran', 'Uang Kasih Sayang')
            ->first();
        if ($cek_pembayaran < $anggaranSetting->catatan_anggaran) {
            return redirect()->back()->with('error', 'Pengajuan belum bisa di lakukan pembayaran masih Rp ' . number_format($cek_pembayaran, 0, ',', '.'));
        }
        if ($cek_pengajuan) {
            return redirect()->back()->with('error', 'Pengajuan sudah ada menunggu diKonfirmasi');
        }
        DB::beginTransaction();

        try {
            $waktuSekarang = now();

            $nominal = number_format($request->loan_amount, 0, ',', '.');
            $sisa = number_format($request->remaining_balance, 0, ',', '.');
            $lebih = number_format($request->overpayment_balance, 0, ',', '.');

            $note = "<b> Catatan : </b> <br> data tambahan yang di ambil dari data sebelumnya sebelum di perbaharui";
            $note .= "<p><b> - Nominal Pinjaman </b> : Rp {$nominal}<br>";
            $note .= "<b> - Sisa Pinjaman </b> : Rp {$sisa} <br>";
            $note .= "<b> - Apakah ada lebih </b> : Rp {$lebih}<br>";
            $note .= "<b> - Status terakhir </b> : {$request->status} </p>";
            $note .= "<p>Data diatas akan di perbaharui jika di setujui, status pinjaman sebelumnya akan menjadi Lunas dan nominal terbayar full dengan uang kasih sayang nya lalu pengajuan baru di buat untuk menggantikan data lama dengan tenor waktu yang di kurangi </p> <br>";

            $data = new LoanExtension();

            $data->loan_id = $request->loan_id;
            $data->reason = $request->reason;
            $data->extension_date = $waktuSekarang;
            $data->notes = $note;
            $data->status = "pending";
            $data->submitted_by = Auth::user()->data_warga_id;

            $data->save();

            // // Mengambil data pengaju (pengguna yang menginput)
            // $pengaju = DataWarga::find(Auth::user()->data_warga_id);

            // // Data Warga
            // $data_warga = DataWarga::find($pinjaman->data_warga_id);
            // $phoneNumberWarga = $data_warga->no_hp;

            // // Pesan untuk Warga
            // $messageWarga = "*Pengajuan Perpanjangan Pinjaman atau Pinjaman Kedua*\n\n";
            // $messageWarga .= "Halo *{$data_warga->name}*,\n";
            // $messageWarga .= "Pengajuan Anda saat ini sedang dalam proses verifikasi. Berikut adalah detail pengajuan Anda:\n\n";
            // $messageWarga .= "📝 *Detail Pengajuan:*\n";
            // $messageWarga .= "- *Kode Pinjaman*: {$pinjaman->code}\n";
            // $messageWarga .= "- *Tanggal Pengajuan*: {$data->extension_date}\n";
            // $messageWarga .= "- *Nama Pengaju*: {$data_warga->name}\n";
            // $messageWarga .= "- *DiAjukan oleh*: {$pengaju->name}\n";
            // $messageWarga .= "- *Nominal Pinjaman Awal*: Rp" . number_format($request->loan_amount, 0, ',', '.') . "\n";
            // $messageWarga .= "- *Sisa Pinjaman Saat Ini*: Rp" . number_format($request->remaining_balance, 0, ',', '.') . "\n";
            // $messageWarga .= "- *Alasan Pengajuan*: {$request->reason}\n\n";
            // $messageWarga .= "Kami mengapresiasi tanggung jawab Anda dalam memenuhi kewajiban pembayaran pinjaman.\n\n";
            // $messageWarga .= "Jika ada pertanyaan atau membutuhkan bantuan lebih lanjut, jangan ragu untuk menghubungi pengurus melalui kontak resmi kami.\n\n";
            // $messageWarga .= "*Terima kasih atas perhatian Anda dan semoga pengajuan Anda segera selesai!*\n\n";
            // $messageWarga .= "*Salam hangat,*\n";
            // $messageWarga .= "*Pengurus Kas Keluarga*";

            // // mengirim ke email 
            // $recipientEmail = $data_warga->email;
            // $recipientName = $data_warga->name;
            // // Ganti tanda bintang dengan HTML <strong>
            // $bodyMessage = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messageWarga);
            // $status = $data->status;
            // $actionUrl = 'https://keluargamahaya.com/detail';

            // // Mengambil nomor telepon Ketua Untuk Laporan
            // $ketua = User::whereHas('role', function ($query) {
            //     $query->where('name', 'Ketua');
            // })->with('dataWarga')->first();

            // $phoneNumberPengurus = $ketua->dataWarga->no_hp ?? null;

            // // Pesan untuk Ketua
            // $messageKetua = "*Pemberitahuan Pengajuan Perpanjangan Pinjaman atau Pinjaman Kedua*\n\n";
            // $messageKetua .= "Halo *{$ketua->dataWarga->name}*,\n";
            // $messageKetua .= "Terdapat pengajuan baru yang membutuhkan persetujuan Anda. Berikut adalah detail pengajuan:\n\n";
            // $messageKetua .= "📝 *Detail Pengajuan:*\n";
            // $messageKetua .= "- *Kode Pinjaman*: {$pinjaman->code}\n";
            // $messageKetua .= "- *Tanggal Pengajuan*: {$data->extension_date}\n";
            // $messageKetua .= "- *Nama Warga*: {$data_warga->name}\n";
            // $messageKetua .= "- *Di Ajukan Oleh*: {$pengaju->name}\n";
            // $messageKetua .= "- *Nominal Pinjaman Awal*: Rp" . number_format($request->loan_amount, 0, ',', '.') . "\n";
            // $messageKetua .= "- *Sisa Pinjaman Saat Ini*: Rp" . number_format($request->remaining_balance, 0, ',', '.') . "\n";
            // $messageKetua .= "- *Alasan Pengajuan*: {$request->reason}\n\n";
            // $messageKetua .= "Mohon untuk segera memproses pengajuan ini sesuai prosedur yang berlaku.\n\n";
            // $messageKetua .= "*Terima kasih atas perhatian dan kerja sama Anda!*\n\n";
            // $messageKetua .= "*Salam hormat,*\n";
            // $messageKetua .= "*Pengurus Kas Keluarga*";

            // // URL gambar dari direktori storage
            // $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

            // $recipientEmailPengurus = $ketua->dataWarga->email;
            // $recipientNamePengurus = $ketua->dataWarga->name;
            // // Data untuk email pengurus
            // $bodyMessagePengurus = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messageKetua);
            // $actionUrlPengurus = 'https://keluargamahaya.com/detail';

            // // Mengirim notifikasi email ke anggota
            // Mail::to($recipientEmail)->send(new Notification($recipientName, $bodyMessage, $status, $actionUrl));

            // // Mengirim email bendahara
            // Mail::to($recipientEmailPengurus)->send(new Notification($recipientNamePengurus, $bodyMessagePengurus, $status, $actionUrlPengurus));

            // // Mengirim pesan ke Warga
            // $responseWarga = $this->fonnteService->sendWhatsAppMessage($phoneNumberWarga, $messageWarga, $imageUrl);

            // // Mengirim pesan ke Pengurus
            // $responsePengurus = $this->fonnteService->sendWhatsAppMessage($phoneNumberPengurus, $messageKetua, $imageUrl);

            // DB::commit();
            // // Cek hasil pengiriman
            // if (
            //     (isset($responseWarga['status']) && $responseWarga['status'] == 'success')
            //     &&
            //     (isset($responsePengurus['status']) && $responsePengurus['status'] == 'success')
            // ) {
            //     return redirect()->route('bayar-pinjaman.pembayaran', Crypt::encrypt($request->loan_id))->with('success', 'Data tersimpan, Notifikasi berhasil dikirim ke Warga dan Pengurus!');
            // }

            // return back()->with('error', 'Data tersimpan, Gagal mengirim notifikasi');


            DB::commit();
            return redirect()->route('bayar-pinjaman.pembayaran', Crypt::encrypt($request->loan_id))->with('success', 'Pengajuan pinjaman ke dua sudah masuk, dan sedah di proses');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat pembayaran.' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LoanExtension $loanExtension)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoanExtension $loanExtension)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LoanExtension $loanExtension)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoanExtension $loanExtension)
    {
        //
    }

    public function pengajuan(String $id)
    {
        $id = Crypt::decrypt($id);
        $pinjaman = Loan::findOrFail($id);
        $cek_pengajuan = LoanExtension::where('loan_id', $pinjaman->id)->where('status', 'pending')->latest('created_at')->first();
        $cek_pembayaran = loanRepayment::where('loan_id', $pinjaman->id)->sum('amount');

        // Ambil nilai catatan_anggaran dari tabel anggaran_settings untuk menentukan deadline
        $anggaranSetting = DB::table('anggaran_settings')
            ->where('label_anggaran', 'Uang Kasih Sayang')
            ->first();
        if (!$anggaranSetting) {
            return back()->withErrors('Pengaturan anggaran untuk "Uang Kasih Sayang" tidak ditemukan.');
        }
        if ($cek_pembayaran < $anggaranSetting->catatan_anggaran) {
            return redirect()->back()->with('error', 'Pengajuan belum bisa di lakukan pembayaran masih Rp ' . number_format($cek_pembayaran, 0, ',', '.'));
        } elseif ($cek_pengajuan) {
            return redirect()->back()->with('error', 'Pengajuan sudah ada menunggu diKonfirmasi');
        } else {
            return view('user.program.kas.pinjamanKeDua', compact('pinjaman'));
        }
    }
    public function rejected(Request $request, String $id)
    {
        $id = Crypt::decrypt($id);
        $data = LoanExtension::findOrFail($id);
        $data->status = $request->status;
        $data->submitted_by = Auth::user()->data_warga_id;

        $data->update();

        return redirect()->back()->with('success', 'Pengajuan sudah di batalkan');
    }

    public function show_confirm(string $id)
    {
        $id = Crypt::decrypt($id);
        $pinjamanKeDua = LoanExtension::findOrFail($id);
        $pinjaman = Loan::findOrFail($pinjamanKeDua->loan_id);
        $jumlahBayarPinjaman = loanRepayment::where('loan_id', $pinjaman->id)->sum('amount');

        // Menghitung hari pinjaman dari awal pinjaman sampai sekarang
        $waktuSekarang = Carbon::now();
        $jatuhTempo = Carbon::parse($pinjaman->created_at);
        $daysElapsed = $jatuhTempo->diffInDays($waktuSekarang, false); //mengambil data yang di hitung hari
        $hitungWaktu = round($daysElapsed); //membulatkan hasil

        return view('user.program.kas.konfirmasi.pinjamanKeDua', compact('pinjamanKeDua', 'pinjaman', 'hitungWaktu', 'jumlahBayarPinjaman'));
    }

    public function confirm(Request $request, String $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate(
            [
                'pembayaran' => 'required',
            ],
            [
                'pembayaran.required' => 'Nominal Pmebayaran sebelum nya kurang dari kesepakatan'
            ]
        );
        // Ambil nilai catatan_anggaran dari tabel anggaran_settings untuk menentukan deadline
        $kasihSayang = DB::table('anggaran_settings')
            ->where('label_anggaran', 'Uang Kasih Sayang')
            ->first();
        if (!$kasihSayang) {
            return back()->withErrors('Pengaturan anggaran untuk "Uang Kasih Sayang" tidak ditemukan.');
        }
        if ($request->pembayaran < $kasihSayang->catatan_anggaran) {
            return redirect()->back()->with('error', 'Tidak bisa di lanjut Total Pembayaran sebelumnya masih kurang dari kesepakatan');
        }

        $pinjamanKeDua = LoanExtension::findOrFail($id);
        $dataPinjaman = Loan::findOrFail($pinjamanKeDua->loan_id);

        DB::beginTransaction();
        try {
            // Membuat kode ========================================
            // Mengambil waktu saat ini
            $dateTime = now();
            // Format tanggal dan waktu
            $formattedDate = $dateTime->format('dmy'); // Dapatkan format DDMMYY
            $formattedTime = $dateTime->format('His'); // Dapatkan format HHMMSS
            // Menghitung jumlah admin saat ini dan menambahkan 1 untuk urutan
            $kasCount = Loan::count() + 1;
            // Membuat kode kas
            $dataAnggaran = Anggaran::where('name', 'Dana Pinjam')->first();
            $code = $dataAnggaran->code_anggaran . '-' . $formattedDate . $formattedTime . str_pad($kasCount, 1, '0', STR_PAD_LEFT);
            // Format akhir: ADM-DDMMYYHHMMSS1
            // Akhir membuat kode ===============================

            // Mengambil data batas waktu dari anggaran =======================================
            // Ambil nilai catatan_anggaran dari tabel anggaran_settings untuk menentukan deadline
            $anggaranSetting = DB::table('anggaran_settings')
                ->where('label_anggaran', 'Max Pinjaman ke 2 (Minggu)')
                ->first();
            if (!$anggaranSetting) {
                return back()->withErrors('Pengaturan anggaran untuk "Max Pinjaman ke 2 (Minggu)" tidak ditemukan.');
            }
            // Ambil nilai durasi pinjaman dalam bulan dari catatan_anggaran
            $loanDurationInMonths = (int)$anggaranSetting->catatan_anggaran;
            // Hitung deadline_date berdasarkan disbursed_date + durasi pinjaman
            $deadlineDate = Carbon::parse($dateTime)->addWeeks($loanDurationInMonths);
            // batas akhir menghitung deadline ==================================

            // Mengambil Jumlah atau total pembayaran pinjaman
            $bayarPinjaman = loanRepayment::where('loan_id', $dataPinjaman->id)->where('status', 'confirmed')->sum('amount');
            //    mengurangi total pembayaran dengan jumlah yang di tentukan
            $cek_sisa = $bayarPinjaman - $kasihSayang->catatan_anggaran;
            //   Menghitung sisa pinjaman yang tersisa
            $sisa_nominal = $dataPinjaman->loan_amount - $cek_sisa;

            // Membuat pinjaman baru berdasarkan data sebelumnya 
            $pinjaman = new Loan();
            $pinjaman->code = $code;
            $pinjaman->anggaran_id = $dataPinjaman->anggaran_id;
            $pinjaman->data_warga_id = $dataPinjaman->data_warga_id;
            $pinjaman->loan_amount = $sisa_nominal;
            $pinjaman->remaining_balance = 0;
            $pinjaman->overpayment_balance = 0;
            $pinjaman->description = $pinjamanKeDua->notes;
            $pinjaman->status = 'disbursed_by_treasurer';
            $pinjaman->submitted_by = $pinjamanKeDua->submitted_by;
            $pinjaman->approved_by = Auth::user()->data_warga_id;
            $pinjaman->approved_date = $dateTime;
            // $pinjaman->disbursed_by =
            // $pinjaman->disbursed_date =
            $pinjaman->deadline_date = $deadlineDate;

            $pinjaman->save();

            // Pengupdate pinjaman sebelumnya menjadi ke Lunas
            $pinjamanUpdate = Loan::findOrFail($pinjamanKeDua->loan_id);
            $pinjamanUpdate->status = 'Paid in Full';
            $pinjamanUpdate->remaining_balance = $sisa_nominal;
            $pinjamanUpdate->overpayment_balance = $kasihSayang->catatan_anggaran;

            $pinjamanUpdate->update();

            $ke_dua = LoanExtension::findOrFail($id);
            $ke_dua->status = 'approved';
            $ke_dua->new_loan_id = $pinjaman->id;

            $ke_dua->update();

            // // Mengambil data pengaju (pengguna yang menginput)
            // $pengaju = DataWarga::find($);

            // // Data Warga
            // $data_warga = DataWarga::find($pinjaman->data_warga_id);
            // $phoneNumberWarga = $data_warga->no_hp;

            // // Pesan untuk Warga
            // $messageWarga = "*Pengajuan Perpanjangan Pinjaman atau Pinjaman Kedua Disetujui*\n\n";
            // $messageWarga .= "Halo *{$data_warga->name}*,\n";
            // $messageWarga .= "Selamat! Pengajuan pinjaman Anda telah disetujui oleh pengurus. Berikut adalah detail pengajuan Anda:\n\n";
            // $messageWarga .= "📝 *Detail Pengajuan:*\n";
            // $messageWarga .= "- *Kode Pinjaman*: {$pinjaman->code}\n";
            // $messageWarga .= "- *Tanggal Pengajuan*: {$dateTime}\n";
            // $messageWarga .= "- *Tanggal Jatuh Tempo*: {$deadlineDate}\n";
            // $messageWarga .= "- *Nama Pengaju*: {$data_warga->name}\n";
            // $messageWarga .= "- *DiAjukan oleh*: {$pinjamanKeDua->data_warga->name}\n";
            // $messageWarga .= "- *Nominal Pinjaman Awal*: Rp" . number_format($sisa_nominal, 0, ',', '.') . "\n\n";
            // $messageWarga .= "- *Alasan Pengajuan*: {$request->reason}\n\n";
            // $messageWarga .= "Pengajuan Anda telah berhasil diproses. Silakan cek kembali detail di atas dan hubungi pengurus jika ada pertanyaan lebih lanjut.\n\n";
            // $messageWarga .= "Terima kasih atas kepercayaan Anda. Semoga pinjaman ini dapat membantu kebutuhan Anda.\n\n";
            // $messageWarga .= "*Salam hangat,*\n";
            // $messageWarga .= "*Pengurus Kas Keluarga*";


            // // mengirim ke email 
            // $recipientEmail = $data_warga->email;
            // $recipientName = $data_warga->name;
            // // Ganti tanda bintang dengan HTML <strong>
            // $bodyMessage = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messageWarga);
            // $status = $data->status;
            // $actionUrl = 'https://keluargamahaya.com/detail';

            // // Mengirim pesan ke Warga
            // $responseWarga = $this->fonnteService->sendWhatsAppMessage($phoneNumberWarga, $messageWarga, $imageUrl);

            // // Mengirim notifikasi email ke anggota
            // Mail::to($recipientEmail)->send(new Notification($recipientName, $bodyMessage, $status, $actionUrl));


            // DB::commit();
            // // Cek hasil pengiriman
            // if (isset($responseWarga['status']) && $responseWarga['status'] == 'success' ) {
            //     return redirect()->route('bayar-pinjaman.pembayaran', Crypt::encrypt($request->loan_id))->with('success', 'Data tersimpan, Notifikasi berhasil dikirim ke Warga dan Pengurus!');
            // }

            // return back()->with('error', 'Data tersimpan, Gagal mengirim notifikasi');


            DB::commit();
            return redirect()->back()->with('success', 'Pengajuan sudah selesai');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', ' Terjadi kesalahan saat menyimpan data ' . $e->getMessage());
        }
    }
}

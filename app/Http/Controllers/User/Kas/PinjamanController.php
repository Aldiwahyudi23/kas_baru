<?php

namespace App\Http\Controllers\User\Kas;

use App\Http\Controllers\Controller;
use App\Mail\Notification;
use App\Models\AccessProgram;
use App\Models\Anggaran;
use App\Models\AnggaranSaldo;
use App\Models\AnggaranSetting;
use App\Models\DataWarga;
use App\Models\LayoutsForm;
use App\Models\Loan;
use App\Models\LoanExtension;
use App\Models\loanRepayment;
use App\Models\Program;
use App\Models\Saldo;
use App\Models\User;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use SebastianBergmann\Type\NullType;

class PinjamanController extends Controller
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
        //Untuk konfirmasi delete
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $layout_form = LayoutsForm::first();

        $pinjaman = Loan::where('data_warga_id', Auth::user()->data_warga_id)->get();
        // mengambil data pinjaman yang belum lunas 
        $pinjaman_proses = Loan::where('data_warga_id', Auth::user()->data_warga_id)->where('status', '!=', 'Paid in Full');
        $pinjaman_tersambung = Loan::where('submitted_by', Auth::user()->data_warga_id)->where('data_warga_id', '!=', Auth::user()->data_warga_id)->get(); //mengambil data pinjaman yang di input oleh user

        // mengambil data anggaran untuk Dana Pinajaman
        $anggaran = Anggaran::where('name', 'Dana Pinjam')->first();
        // mengambil data anggota kas yng di atur oleh access program
        $program = Program::where('name', 'Kas Keluarga')->first();
        $access = AccessProgram::where('program_id', $program->id)->get();

        $saldo_pinjam = AnggaranSaldo::where('type', 'Dana Pinjam')->latest()->first();


        return view('user.program.kas.pinjaman', compact('layout_form', 'pinjaman', 'anggaran', 'pinjaman_proses', 'access', 'pinjaman_tersambung', 'saldo_pinjam'));
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
            'amount' => 'required',
            'description' => 'required',

        ]);

        $dataAnggaran = Anggaran::where('name', 'Dana Pinjam')->first();
        // Cek apakah Saldo cukup berdasarkan anggaran

        $saldo_akhir_request =  AnggaranSaldo::where('type', $dataAnggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran

        if (!$saldo_akhir_request || !$saldo_akhir_request->saldo) {
            // Jika tidak ada data saldo atau saldo kosong, redirect dengan pesan error
            return redirect()->back()->with('error', 'Saldo tidak tersedia atau tidak ada nilai untuk anggaran ini.');
        }
        if ($saldo_akhir_request->saldo <  $request->amount) {
            return redirect()->back()->with('error', 'Saldo untuk ' . $dataAnggaran->name . ' Kurang dari pengajuan.');
        }
        // -------------------------------------------

        // cek untuk nominal Max sesuai kesepakatan
        $nominal_max = AnggaranSetting::where('anggaran_id', $dataAnggaran->id)->where('label_anggaran', 'Alokasi Anggaran Max')->first();
        if ($request->amount > $nominal_max->catatan_anggaran) {
            return back()->with('error', 'Nominal yang di ajukan melebihi batas max yang telah di sepakati');
        }
        // -------------------------------------------

        // Ambil data pinjaman terbaru dari data_warga_id tertentu
        $latestLoan = Loan::where('data_warga_id', $request->data_warga_id)
            ->latest()
            ->first();

        // Cek jika sudah ada pengajuan pinjaman sebelumnya
        if ($latestLoan) {
            // Jika status belum lunas atau bukan 'Paid in Full', beri alert
            if ($latestLoan->status !== 'Paid in Full') {
                return back()->with('error', 'Pengajuan pinjaman tidak dapat dilanjutkan. Status pengajuan terakhir masih: ' . $latestLoan->status);
            }

            // Cek pembayaran terakhir di LoanRepayment untuk pinjaman ini
            $lastRepayment = LoanRepayment::where('loan_id', $latestLoan->id)
                ->latest('payment_date')
                ->first();

            if ($lastRepayment) {
                // Tanggal pembuatan pinjaman terbaru
                $loanCreationDate = Carbon::parse($latestLoan->created_at);
                // Tanggal pembayaran terakhir
                $lastPaymentDate = Carbon::parse($lastRepayment->payment_date);

                // Ambil data pengaturan waktu tunggu
                $kurangSebulan = AnggaranSetting::where('label_anggaran', 'Lunas kurang sebulan (Minggu)')
                    ->where('anggaran_id', $dataAnggaran->id)
                    ->first();
                $pembayaranTanpaLebih = AnggaranSetting::where('label_anggaran', 'Pembayaran tanpa lebih (hari)')
                    ->where('anggaran_id', $dataAnggaran->id)
                    ->first();
                $weeksToAdd = intval($kurangSebulan->catatan_anggaran);
                $tanpaLebih = intval($pembayaranTanpaLebih->catatan_anggaran);

                // Hitung tanggal pengajuan berikutnya berdasarkan aturan waktu tunggu
                $nextEligibleDate = $lastPaymentDate->copy()->addWeeks($weeksToAdd);

                // Cek jika selisih antara pembayaran terakhir dan pengajuan baru kurang dari sebulan
                $daysDifference = $loanCreationDate->diffInDays($lastPaymentDate);

                if ($daysDifference < $tanpaLebih && now()->lessThan($nextEligibleDate)) {
                    return back()->with('error', 'Pengajuan baru tidak dapat dilakukan. Coba lagi pada tanggal ' . $nextEligibleDate->format('d-m-Y'));
                }
            }
        }


        DB::beginTransaction();

        try {
            // Mengambil waktu saat ini
            $dateTime = now();

            // Format tanggal dan waktu
            $formattedDate = $dateTime->format('dmy'); // Dapatkan format DDMMYY
            $formattedTime = $dateTime->format('His'); // Dapatkan format HHMMSS

            // Menghitung jumlah admin saat ini dan menambahkan 1 untuk urutan
            $kasCount = Loan::count() + 1;

            // Membuat kode kas
            $code = $dataAnggaran->code_anggaran . '-' . $formattedDate . $formattedTime . str_pad($kasCount, 1, '0', STR_PAD_LEFT);
            // Format akhir: ADM-DDMMYYHHMMSS1

            // Ambil description lama
            $existingDescription = $request->input('description', '');

            // Tambahkan data sesuai metode pembayaran
            $newDescription = "<b>Alasan</b> : " . $existingDescription;
            if ($request->payment_method === 'transfer') {
                $newDescription .= "<p><b> Transfer </b> <br>";
                $newDescription .= "Nama Bank/Ewallet : {$request->bank_name} <br>";
                $newDescription .= "No Rekening/Ewallet : {$request->account_number}<br>";
                $newDescription .= "Atas Nama  : {$request->account_name} </p>";
            } elseif ($request->payment_method === 'cash') {
                $newDescription .= "<p><b>Pengambilan</b>  : <br> {$request->cash_notes}";
            }

            if ($request->data_warga_id) {
                $warga = $request->data_warga_id;
            } else {
                $warga = Auth::user()->data_warga_id;
            }

            $data = new Loan();
            $data->code = $code;
            $data->anggaran_id = $dataAnggaran->id;
            $data->data_warga_id = $warga;
            $data->loan_amount = $request->amount;
            $data->remaining_balance = $request->amount;
            $data->status = 'pending';
            $data->submitted_by = Auth::user()->data_warga_id;


            $data->description = $newDescription;

            $data->save();
            // -------------------------------------
            // // Mengambil data pengaju (pengguna yang menginput)

            // $pengaju = DataWarga::find($data->submitted_by);
            // // mengambil data anggaran berdasarkan anggaran_id
            // $anggaran = Anggaran::findOrFail($dataAnggaran->id);
            // // Data Warga
            // $data_warga = DataWarga::find($request->data_warga_id);
            // $phoneNumberWarga = $data_warga->no_hp;

            // $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            // $linkWarga = "https://keluargamahaya.com/pinjaman/{$encryptedId}";

            // // Pesan untuk Warga
            // // Pesan untuk Warga
            // $messageWarga = "*Pengajuan Pinjaman Anda Telah Diterima dan Menunggu Konfirmasi*\n";
            // $messageWarga .= "Halo {$data_warga->name},\n\n";
            // $messageWarga .= "Kami senang memberitahukan bahwa pengajuan pinjaman Anda telah kami terima dan saat ini sedang dalam proses konfirmasi oleh pengurus. Berikut detail pengajuan Anda:\n\n";
            // $messageWarga .= "- *Kode Pinjaman*: {$code}\n";
            // $messageWarga .= "- *Nama Anggaran*: {$anggaran->name}\n";
            // $messageWarga .= "- *Tanggal Pengajuan*: {$dateTime}\n";
            // $messageWarga .= "- *Nama Pengaju*: {$data_warga->name}\n";
            // $messageWarga .= "- *Diinput Oleh*: {$pengaju->name}\n";
            // $messageWarga .= "- *Nominal*: Rp" . number_format($request->amount, 0, ',', '.') . "\n\n";
            // $messageWarga .= "Mohon untuk menunggu proses konfirmasi dari pengurus kami. Setelah dana dicairkan, Anda akan mendapatkan pemberitahuan lanjutan.\n\n";
            // $messageWarga .= "Apabila terdapat pertanyaan, silakan hubungi kami melalui sistem atau pengurus langsung.\n\n";
            // $messageWarga .= "*Terima kasih atas kepercayaan Anda, Pantau prosesnya klik link di bawah.*\n";
            // $messageWarga .= $linkWarga . "\n\n";
            // $messageWarga .= "*Salam hangat,*\n";
            // $messageWarga .= "*Sistem Kas Keluarga*";


            // // mengirim ke email 
            // $recipientEmail = $data_warga->email;
            // $recipientName = $data_warga->name;
            // // Ganti tanda bintang dengan HTML <strong>
            // $bodyMessage = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messageWarga);
            // $status = $data->status;
            // $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            // $actionUrl = $linkWarga;


            // // Mengambil nomor telepon Ketua Untuk Laporan
            // $ketua = User::whereHas('role', function ($query) {
            //     $query->where('name', 'Ketua');
            // })->with('dataWarga')->first();

            // $phoneNumberPengurus = $ketua->dataWarga->no_hp ?? null;

            // // Data untuk pesan
            // $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            // $link = "https://keluargamahaya.com/confirm/pinjaman/{$encryptedId}";

            // // Membuat pesan WhatsApp
            // $messageKetua = "*Persetujuan Pinjaman Diperlukan*\n";
            // $messageKetua .= "Halo {$ketua->dataWarga->name},\n\n";
            // $messageKetua .= "Terdapat pengajuan Pinjamn yang memerlukan persetujuan Anda sebelum dapat dicairkan oleh Bendahara. Berikut detail pengajuannya:\n\n";
            // $messageKetua .= "- *Kode Pinjaman*: {$code}\n";
            // $messageKetua .= "- *Nama Anggaran*: {$anggaran->name}\n";
            // $messageKetua .= "- *Tanggal Pengajuan*: {$dateTime}\n";
            // $messageKetua .= "- *Nama *: {$data_warga->name}\n";
            // $messageKetua .= "- *Di Input*: {$pengaju->name}\n";
            // $messageKetua .= "- *Nominal*: Rp" . number_format($request->amount, 0, ',', '.') . "\n\n";
            // $messageKetua .= "Silakan klik link berikut untuk memberikan persetujuan:\n";
            // $messageKetua .= $link . "\n\n";
            // $messageKetua .= "*Salam hormat,*\n";
            // $messageKetua .= "*Sistem Kas Keluarga*";


            // // URL gambar dari direktori storage
            // $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

            // $recipientEmailPengurus = $ketua->dataWarga->email;
            // $recipientNamePengurus = $ketua->dataWarga->name;
            // $status = "Menunggu persetujuan Ketua";
            // // Data untuk email pengurus
            // $bodyMessagePengurus = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messageKetua);
            // $actionUrlPengurus = $link;


            // // Mengirim notifikasi email ke anggota
            // Mail::to($recipientEmail)->send(new Notification($recipientName, $bodyMessage, $status, $actionUrl));

            // // Mengirim pesan ke Warga
            // $responseWarga = $this->fonnteService->sendWhatsAppMessage($phoneNumberWarga, $messageWarga, $imageUrl);

            // // Mengirim email bendahara
            // Mail::to($recipientEmailPengurus)->send(new Notification($recipientNamePengurus, $bodyMessagePengurus, $status, $actionUrlPengurus));

            // // Mengirim pesan ke Pengurus
            // $responsePengurus = $this->fonnteService->sendWhatsAppMessage($phoneNumberPengurus, $messageKetua, $imageUrl);

            // DB::commit();
            // // Cek hasil pengiriman
            // if (
            //     (isset($responsePengurus['status']) && $responsePengurus['status'] == 'success')
            // ) {
            //     return back()->with('success', 'Data tersimpan, Notifikasi berhasil dikirim ke Warga dan Pengurus!');
            // }

            // return back()->with('error', 'Data tersimpan, Gagal mengirim notifikasi');

            // Jik nitifikasi di aktifkan return yang ini di hapus
            DB::commit();

            return redirect()->back()->with('success', 'Data Pinjaman berhasil, tidak ada notif');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data pengeluaran.' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //Untuk konfirmasi delete
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $id = Crypt::decrypt($id);
        $pinjaman = Loan::findOrFail($id);
        $bayarPinjaman = loanRepayment::where('loan_id', $pinjaman->id)->get();
        // Ambil tanggal pembayaran terakhir
        $lastRepayment = LoanRepayment::where('loan_id', $pinjaman->id)->where('status', 'confirmed')
            ->where('status', 'confirmed')
            ->latest('payment_date')
            ->first();
        // Tanggal pembuatan pinjaman terbaru
        $loanCreationDate = Carbon::parse($pinjaman->created_at);
        // Tanggal pembayaran terakhir (cek apakah $lastRepayment ada)
        if ($lastRepayment) {
            $lastPaymentDate = Carbon::parse($lastRepayment->payment_date);
            // Cek jika selisih antara pembayaran terakhir dan pengajuan baru kurang dari sebulan
        } else {
            // Jika tidak ada data pembayaran, beri nilai default atau abaikan logika tertentu
            $lastPaymentDate = $loanCreationDate; // Bisa diganti dengan default sesuai kebutuhan
            // Logika alternatif jika pembayaran terakhir tidak ada
            // Misalnya, lanjutkan pengajuan pinjaman
        }
        $waktubayar = $loanCreationDate->diffInDays($lastPaymentDate);
        $waktuPembayaran =  round($waktubayar);
        // mengecek untuk data yang telah di atau runtuk pembayaran yang tanpa lebih
        $pembayaranTanpaLebih = AnggaranSetting::where('label_anggaran', 'Pembayaran tanpa lebih (hari)')
            ->where('anggaran_id', $pinjaman->anggaran_id)
            ->first();
        $tanpaLebih = intval($pembayaranTanpaLebih->catatan_anggaran);
        $waktuDitentukan = $tanpaLebih;
        // Menghitung hari pinjaman dari awal pinjaman sampai sekarang
        $waktuSekarang = Carbon::now();
        $jatuhTempo = Carbon::parse($pinjaman->deadline_date);
        $daysElapsed = $waktuSekarang->diffInDays($jatuhTempo, false); //mengambil data yang di hitung hari
        $hitungWaktu = round($daysElapsed); //membulatkan hasil

        $pinjamanKeDua = LoanExtension::where('loan_id', $pinjaman->id)->where('status', 'approved')->first();

        return view('user.program.kas.detail.show_pinjaman', compact('pinjaman', 'bayarPinjaman', 'waktuPembayaran', 'waktuDitentukan', 'hitungWaktu', 'pinjamanKeDua'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = Crypt::decrypt($id);
        $cek_data = Loan::findOrFail($id);
        if ($cek_data->status != "pending") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $cek_data->status);
        }
        $pinjaman = Loan::findOrFail($id);
        return view('user.program.kas.edit.pinjaman', compact('pinjaman'));
    }
    public function editPengurus(string $id)
    {
        $id = Crypt::decrypt($id);
        $cek_data = Loan::findOrFail($id);
        if (!in_array($cek_data->status, ['pending', 'approved_by_chairman'])) {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $cek_data->status);
        }
        $pinjaman = Loan::findOrFail($id);
        return view('user.program.kas.edit.pengurus.pinjaman', compact('pinjaman'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'data_warga_id' => 'required',
            'amount' => 'required',
            'description' => 'required',

        ]);

        $dataAnggaran = Anggaran::where('name', 'Dana Pinjam')->first();
        // Cek apakah Saldo cukup berdasarkan anggaran

        $saldo_akhir_request =  AnggaranSaldo::where('type', $dataAnggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran

        if (!$saldo_akhir_request || !$saldo_akhir_request->saldo) {
            // Jika tidak ada data saldo atau saldo kosong, redirect dengan pesan error
            return redirect()->back()->with('error', 'Saldo tidak tersedia atau tidak ada nilai untuk anggaran ini.');
        }
        if ($saldo_akhir_request->saldo <  $request->amount) {
            return redirect()->back()->with('error', 'Saldo untuk ' . $dataAnggaran->name . ' Kurang dari pengajuan.');
        }
        // -------------------------------------------

        // cek untuk nominal Max sesuai kesepakatan
        $nominal_max = AnggaranSetting::where('anggaran_id', $dataAnggaran->id)->where('label_anggaran', 'Alokasi Anggaran Max')->first();
        if ($request->amount > $nominal_max->catatan_anggaran) {
            return back()->with('error', 'Nominal yang di ajukan melebihi batas max yang telah di sepakati');
        }

        $cek_data = Loan::findOrFail($id);
        if ($cek_data->status != "pending") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $cek_data->status);
        }

        $data = Loan::findOrFail($id);
        $data->loan_amount = $request->amount;
        $data->remaining_balance = $request->amount;
        $data->data_warga_id = $request->data_warga_id;
        $data->description = $request->description;

        $data->update();

        return redirect()->back()->with('success', 'Pengajuan Pinjaman sudah di edit');
    }
    public function updatePengurus(Request $request, string $id)
    {

        $id = Crypt::decrypt($id);
        $request->validate([
            'data_warga_id' => 'required',
            'amount' => 'required',
            'description' => 'required',

        ]);

        $dataAnggaran = Anggaran::where('name', 'Dana Pinjam')->first();
        // Cek apakah Saldo cukup berdasarkan anggaran

        $saldo_akhir_request =  AnggaranSaldo::where('type', $dataAnggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran

        if (!$saldo_akhir_request || !$saldo_akhir_request->saldo) {
            // Jika tidak ada data saldo atau saldo kosong, redirect dengan pesan error
            return redirect()->back()->with('error', 'Saldo tidak tersedia atau tidak ada nilai untuk anggaran ini.');
        }
        if ($saldo_akhir_request->saldo <  $request->amount) {
            return redirect()->back()->with('error', 'Saldo untuk ' . $dataAnggaran->name . ' Kurang dari pengajuan.');
        }
        // -------------------------------------------

        // cek untuk nominal Max sesuai kesepakatan
        $nominal_max = AnggaranSetting::where('anggaran_id', $dataAnggaran->id)->where('label_anggaran', 'Alokasi Anggaran Max')->first();
        if ($request->amount > $nominal_max->catatan_anggaran) {
            return back()->with('error', 'Nominal yang di ajukan melebihi batas max yang telah di sepakati');
        }


        $cek_data = Loan::findOrFail($id);
        if (!in_array($cek_data->status, ['pending', 'approved_by_chairman'])) {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $cek_data->status);
        }

        $data = Loan::findOrFail($id);
        $data->loan_amount = $request->amount;
        $data->remaining_balance = $request->amount;
        $data->data_warga_id = $request->data_warga_id;
        $data->description = $request->description;

        $data->update();

        return redirect()->route('pinjaman.show.confirm', Crypt::encrypt($id))->with('success', 'Pengajuan Pinjaman sudah di edit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $id = Crypt::decrypt($id);
        $data = Loan::find($id);
        if (in_array($data->status, ['pending', 'approved_by_chairman'])) {
            $data->delete();
            return redirect()->back()->with('success', 'Pengajuan Pinjaman sudah di hapus');
        } else {
            return redirect()->back()->with('error', 'Pengajuan Pinjaman tidak dapat di hapus sudah dalam status' . $data->status);
        }
    }
    public function destroyPengurus(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = Loan::find($id);
        if (in_array($data->status, ['pending', 'approved_by_chairman'])) {
            $data->delete();
            return redirect()->route('pinjaman.pengajuan')->with('success', 'Pengajuan Pinjaman sudah di hapus');
        } else {
            return redirect()->back()->with('error', 'Pengajuan Pinjaman tidak dapat di hapus sudah dalam status' . $data->status);
        }
    }

    public function pengajuan()
    {
        $pinjaman_proses = Loan::whereIn('status', ['pending', 'approved_by_chairman', 'disbursed_by_treasurer', 'Acknowledged'])->get();

        $pinjaman_pending = Loan::where('status', 'pending')->get();

        return view('user.program.kas.pengajuan.pinjaman', compact('pinjaman_proses', 'pinjaman_pending'));
    }
    public function show_confirm($id)
    {
        //Untuk konfirmasi delete
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $id = Crypt::decrypt($id);
        $pinjaman = Loan::findOrFail($id);
        return view('user.program.kas.konfirmasi.pinjaman', compact('pinjaman'));
    }
    public function approved(Request $request, string $id)
    {
        //Untuk konfirmasi delete
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $id = Crypt::decrypt($id);
        $request->validate([

            'status' => 'required',
        ]);

        $dataAnggaran = Anggaran::where('name', 'Dana Pinjam')->first();
        // Cek apakah Saldo cukup berdasarkan anggaran

        $saldo_akhir_request =  AnggaranSaldo::where('type', $dataAnggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran

        if (
            $saldo_akhir_request->saldo <  $request->amount
        ) {
            return redirect()->back()->with('error', 'Saldo untuk ' . $dataAnggaran->name . ' Kurang dari pengajuan.');
        }
        // -------------------------------------------

        // cek untuk nominal Max sesuai kesepakatan
        $nominal_max = AnggaranSetting::where('anggaran_id', $dataAnggaran->id)->where('label_anggaran', 'Alokasi Anggaran Max')->first();
        if ($request->amount > $nominal_max->catatan_anggaran) {
            return back()->with('error', 'Nominal yang di ajukan melebihi batas max yang telah di sepakati');
        }
        // -------------------------------------------

        // Ambil data pinjaman terbaru dari data_warga_id tertentu
        $latestLoan = Loan::where('data_warga_id', $request->data_warga_id)
            ->latest()
            ->first();
        // Cek jika sudah ada pengajuan pinjaman sebelumnya
        if ($latestLoan) {
            // Jika status belum lunas atau bukan 'paid in full', beri alert
            // if ($latestLoan->status !== 'paid in full') {
            //     return back()->with('error', 'Pengajuan pinjaman tidak dapat dilanjutkan. Status pengajuan terakhir masih: ' . $latestLoan->status);
            // }

            // Cek pembayaran terakhir di LoanRepayment untuk pinjaman ini
            $lastRepayment = LoanRepayment::where('loan_id', $latestLoan->id)
                ->latest('payment_date')
                ->first();

            if ($lastRepayment) {
                $loanCreationDate = Carbon::parse($latestLoan->created_at);
                $lastPaymentDate = Carbon::parse($lastRepayment->payment_date);

                // Cek jika pinjaman selesai dalam waktu kurang dari satu bulan
                if ($lastPaymentDate->diffInDays($loanCreationDate) < 30) {
                    // Cek apakah satu minggu sudah berlalu sejak pembayaran terakhir
                    if ($lastPaymentDate->addWeek()->isFuture()) {
                        return back()->with('error', 'Pengajuan baru tidak dapat dilakukan. Harap tunggu satu minggu sejak pembayaran selesai pada ' . $lastRepayment->payment_date->format('d-m-Y'));
                    }
                } else if ($lastPaymentDate->addWeeks(2)->isFuture()) {
                    return back()->with(
                        'error',
                        'Pengajuan baru tidak dapat dilakukan. Harap tunggu dua minggu sejak pembayaran terakhir pada ' . $lastRepayment->payment_date->format('d-m-Y')
                    );
                }
            } else {
                // Jika tidak ada riwayat pembayaran, maka ambil waktu pengajuan pinjaman
                $loanCreationDate = Carbon::parse($latestLoan->created_at);
                if ($latestLoan->status == 'paid in full') {
                    // Cek waktu satu minggu sejak tanggal pengajuan pinjaman terakhir
                    if ($loanCreationDate->addWeek()->isFuture()) {
                        return back()->with('error', 'Pengajuan baru tidak dapat dilakukan. Harap tunggu satu minggu sejak pengajuan terakhir pada ' . $loanCreationDate->format('d-m-Y'));
                    }
                }
            }
        }

        DB::beginTransaction();

        try {
            $dateTime = now();

            $data = Loan::findOrFail($id);
            $data->status = $request->status;
            $data->approved_by = Auth::user()->data_warga_id;
            $data->approved_date = $dateTime;

            $data->update();

            // -----------------------
            // Mengambil nomor telepon Ketua Untuk Laporan
            $bendahara = User::whereHas('role', function ($query) {
                $query->where('name', 'Bendahara');
            })->with('dataWarga')->first();

            $phoneNumberPengurus = $bendahara->dataWarga->no_hp ?? null;

            // Data untuk pesan
            $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            $link = "https://keluargamahaya.com/confirm/pinjaman/{$encryptedId}";

            // Membuat pesan WhatsApp
            $messagePengurus = "*Pengajuan Pinjaman Disetujui*\n";
            $messagePengurus .= "Halo {$bendahara->dataWarga->name},\n\n";
            $messagePengurus .= "Pengajuan pinjaman berikut telah disetujui oleh {$data->ketua->name} dan sekarang dapat dilanjutkan ke tahap pencairan:\n\n";
            $messagePengurus .= "- *Kode Anggaran*: {$data->code}\n";
            $messagePengurus .= "- *Tanggal Pengajuan*: {$data->created_at->format('d M Y')}\n";
            $messagePengurus .= "- *Nama Anggaran*: {$data->anggaran->name}\n";
            $messagePengurus .= "- *Di Input*: {$data->sekretaris->name}\n";
            $messagePengurus .= "- *Nominal*: Rp" . number_format($data->loan_amount, 0, ',', '.') . "\n\n";
            $messagePengurus .= "- *Di Konformasi*: {$data->ketua->name}\n";
            $messagePengurus .= "- *Pada Tanggal*: {$data->approved_date->format('d M Y')}\n\n";
            $messagePengurus .= "Silakan klik link berikut untuk melanjutkan proses pencairan:\n";
            $messagePengurus .= $link . "\n\n";
            $messagePengurus .= "*Salam hormat,*\n";
            $messagePengurus .= "*Sistem Kas Keluarga*";


            // URL gambar dari direktori storage
            $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

            $recipientEmailPengurus = $bendahara->dataWarga->email;
            $recipientNamePengurus = $bendahara->dataWarga->name;
            $status = "Sudah di setujui, menunggu pencairan";
            // Data untuk email pengurus
            $bodyMessagePengurus = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messagePengurus);
            $actionUrlPengurus = $link;

            // Mengirim email bendahara
            Mail::to($recipientEmailPengurus)->send(new Notification($recipientNamePengurus, $bodyMessagePengurus, $status, $actionUrlPengurus));

            // Mengirim pesan ke Pengurus
            $responsePengurus = $this->fonnteService->sendWhatsAppMessage($phoneNumberPengurus, $messagePengurus, $imageUrl);

            DB::commit();
            // Cek hasil pengiriman
            if (
                (isset($responsePengurus['status']) && $responsePengurus['status'] == 'success')
            ) {
                return back()->with('success', 'Data tersimpan, Notifikasi berhasil dikirim ke Bendahara!');
            }

            return back()->with('error', 'Data tersimpan, Gagal mengirim notifikasi');

            //Jika mengaktifkan notif wa komen yang di bawa
            // DB::commit();

            // return redirect()->back()->with('success', 'Terimakasih sudah menyetujui anggaran ini, data akan masuk ke bendahara untuk di cairkan ,notifikasi off');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pemasukan.' . $e->getMessage());
        }
    }

    public function disbursed(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'amount' => 'required',
            'status' => 'required',
            'disbursement_receipt_path' => 'required',
        ]);

        $dataAnggaran = Anggaran::where('name', 'Dana Pinjam')->first();
        // Cek apakah Saldo cukup berdasarkan anggaran

        $saldo_akhir_request =  AnggaranSaldo::where('type', $dataAnggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran

        if ($saldo_akhir_request->saldo <  $request->amount) {
            return redirect()->back()->with('error', 'Saldo untuk ' . $dataAnggaran->name . ' Kurang dari pengajuan.');
        }
        // -------------------------------------------

        // cek untuk nominal Max sesuai kesepakatan
        $nominal_max = AnggaranSetting::where('anggaran_id', $dataAnggaran->id)->where('label_anggaran', 'Alokasi Anggaran Max')->first();
        if ($request->amount > $nominal_max->catatan_anggaran) {
            return back()->with('error', 'Nominal yang di ajukan melebihi batas max yang telah di sepakati');
        }
        // -------------------------------------------

        // Ambil data pinjaman terbaru dari data_warga_id tertentu
        $latestLoan = Loan::where('data_warga_id', $request->data_warga_id)
            ->latest()
            ->first();
        // Cek jika sudah ada pengajuan pinjaman sebelumnya
        if ($latestLoan) {
            // Jika status belum lunas atau bukan 'paid in full', beri alert
            // if ($latestLoan->status !== 'paid in full') {
            //     return back()->with('error', 'Pengajuan pinjaman tidak dapat dilanjutkan. Status pengajuan terakhir masih: ' . $latestLoan->status);
            // }

            // Cek pembayaran terakhir di LoanRepayment untuk pinjaman ini
            $lastRepayment = LoanRepayment::where('loan_id', $latestLoan->id)
                ->latest('payment_date')
                ->first();

            if ($lastRepayment) {
                $loanCreationDate = Carbon::parse($latestLoan->created_at);
                $lastPaymentDate = Carbon::parse($lastRepayment->payment_date);

                // Cek jika pinjaman selesai dalam waktu kurang dari satu bulan
                if ($lastPaymentDate->diffInDays($loanCreationDate) < 30) {
                    // Cek apakah satu minggu sudah berlalu sejak pembayaran terakhir
                    if ($lastPaymentDate->addWeek()->isFuture()) {
                        return back()->with('error', 'Pengajuan baru tidak dapat dilakukan. Harap tunggu satu minggu sejak pembayaran selesai pada ' . $lastRepayment->payment_date->format('d-m-Y'));
                    }
                } else if ($lastPaymentDate->addWeeks(2)->isFuture()) {
                    return back()->with(
                        'error',
                        'Pengajuan baru tidak dapat dilakukan. Harap tunggu dua minggu sejak pembayaran terakhir pada ' . $lastRepayment->payment_date->format('d-m-Y')
                    );
                }
            } else {
                // Jika tidak ada riwayat pembayaran, maka ambil waktu pengajuan pinjaman
                $loanCreationDate = Carbon::parse($latestLoan->created_at);
                if ($latestLoan->status == 'paid in full') {
                    // Cek waktu satu minggu sejak tanggal pengajuan pinjaman terakhir
                    if ($loanCreationDate->addWeek()->isFuture()) {
                        return back()->with('error', 'Pengajuan baru tidak dapat dilakukan. Harap tunggu satu minggu sejak pengajuan terakhir pada ' . $loanCreationDate->format('d-m-Y'));
                    }
                }
            }
        }

        DB::beginTransaction();

        try {
            // Mengambil waktu saat ini
            $dateTime = now();

            $data = Loan::findOrFail($id);
            // Ambil nilai catatan_anggaran dari tabel anggaran_settings untuk menentukan deadline
            $anggaranSetting = DB::table('anggaran_settings')
                ->where('label_anggaran', 'Max Pinjaman (Bulan)')
                ->first();
            if (!$anggaranSetting) {
                return back()->withErrors('Pengaturan anggaran untuk "Max Pinjaman (Bulan)" tidak ditemukan.');
            }
            // Ambil nilai durasi pinjaman dalam bulan dari catatan_anggaran
            $loanDurationInMonths = (int)$anggaranSetting->catatan_anggaran;
            // Hitung deadline_date berdasarkan disbursed_date + durasi pinjaman
            $deadlineDate = Carbon::parse($dateTime)->addMonths($loanDurationInMonths);

            $keterangan = $data->description . "<hr> <b> Keterangan Bendahara </b> : <br>" . $request->description;
            $data->description = $keterangan;

            $data->loan_amount = $request->amount;
            $data->status = $request->status;
            $data->disbursed_by = Auth::user()->data_warga_id;
            $data->disbursed_date = $dateTime;
            $data->deadline_date = $deadlineDate;
            // Cek apakah file profile_picture di-upload
            // if ($request->hasFile('disbursement_receipt_path')) {
            //     $file = $request->file('disbursement_receipt_path');
            //     $path = $file->store(
            //         'kas/pengeluaran/pinjam',
            //         'public'
            //     ); // Simpan gambar ke direktori public
            //     $data->disbursement_receipt_path = $path;
            // }


            if ($request->hasFile('disbursement_receipt_path')) {
                $file = $request->file('disbursement_receipt_path');
                $filename = 'Kas-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/kas/pengeluaran/pinjaman'), $filename);  // Simpan gambar ke folder public/storage/kas/pengeluaran
                $data->disbursement_receipt_path = "storage/kas/pengeluaran/pinjaman/$filename";  // Simpan path gambar ke database
            }

            $data->update();
            // -------------------------------------
            // Mengambil data pengaju (pengguna yang menginput)
            $pengaju = DataWarga::find($data->submitted_by);

            // Data Warga
            $data_warga = DataWarga::find($data->data_warga_id);
            $phoneNumberWarga = $data_warga->no_hp;

            $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            $link = "https://keluargamahaya.com/pinjaman/{$encryptedId}";

            // Pesan untuk Warga
            $messageWarga = "*Pengajuan Pinjaman Anda Disetujui dan Dicairkan*\n";
            $messageWarga .= "Halo {$data_warga->name},\n\n";
            $messageWarga .= "Kami informasikan bahwa pengajuan pinjaman Anda telah disetujui dan dana telah dicairkan oleh bendahara {$data->bendahara->name}. Berikut adalah detailnya:\n\n";
            $messageWarga .= "- *Kode Pinjaman*: {$data->code}\n";
            $messageWarga .= "- *Tanggal Pencairan*: {$data->disbursed_date->format('d M Y')}\n";
            $messageWarga .= "- *Nama Peminjam*: {$data_warga->name}\n";
            $messageWarga .= "- *Nominal*: Rp" . number_format($data->loan_amount, 0, ',', '.') . "\n";
            $messageWarga .= "- *Jatuh Tempo*: {$data->deadline_date->format('d M Y')}\n\n";
            $messageWarga .= "Mohon segera cek saldo di rekening Anda untuk memastikan dana telah masuk atau ambil sesuai kesepakatan \n\n";
            $messageWarga .= "Setelah menerima dana, mohon segera konfirmasi bahwa uang telah diterima dengan menghubungi kami melalui sistem atau langsung kepada pengurus.\n\n";
            $messageWarga .=  $link . "\n";
            $messageWarga .= "Terima kasih atas perhatian Anda.\n\n";
            $messageWarga .= "*Salam hormat,*\n";
            $messageWarga .= "*Sistem Kas Keluarga*";


            // mengirim ke email 
            $recipientEmail = $data_warga->email;
            $recipientName = $data_warga->name;
            // Ganti tanda bintang dengan HTML <strong>
            $bodyMessage = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messageWarga);
            $status = "Sudah di Cairkan";

            $actionUrl = $link;

            // Mengambil nomor telepon Ketua Untuk Laporan
            $ketua = User::whereHas('role', function ($query) {
                $query->where('name', 'Ketua');
            })->with('dataWarga')->first();

            $phoneNumberPengurus = $ketua->dataWarga->no_hp ?? null;

            // Pesan untuk Ketua
            $messageKetua = "*Laporan Pencairan Pinjaman*\n";
            $messageKetua .= "Halo {$ketua->dataWarga->name},\n\n";
            $messageKetua .= "Berikut adalah laporan pencairan pinjaman yang telah diproses oleh bendahara {$data->bendahara->name}:\n\n";
            $messageKetua .= "- *Kode Pinjaman*: {$data->code}\n";
            $messageKetua .= "- *Tanggal Pencairan*: {$data->disbursed_date->format('d M Y')}\n";
            $messageKetua .= "- *Nama Peminjam*: {$data_warga->name}\n";
            $messageKetua .= "- *Nominal*: Rp" . number_format($data->loan_amount, 0, ',', '.') . "\n";
            $messageKetua .= "- *Jatuh Tempo*: {$data->deadline_date->format('d M Y')}\n\n";
            $messageKetua .= "Pencairan ini telah berhasil diproses dan dana telah diberikan kepada pengaju.\n";
            $messageKetua .= "Saat ini, pencairan menunggu konfirmasi dari pengaju bahwa dana telah diterima.\n\n";
            $messageKetua .= "Silakan pantau proses selanjutnya jika diperlukan.\n\n";
            $messageKetua .= "*Salam hormat,*\n";
            $messageKetua .= "*Sistem Kas Keluarga*";



            // URL gambar dari direktori storage
            $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

            $recipientEmailPengurus = $ketua->dataWarga->email;
            $recipientNamePengurus = $ketua->dataWarga->name;
            // Data untuk email pengurus
            $bodyMessagePengurus = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messageKetua);
            $actionUrlPengurus = "https://keluargamahaya.com/pinjaman/{$encryptedId}";

            // Mengirim notifikasi email ke anggota
            Mail::to($recipientEmail)->send(new Notification($recipientName, $bodyMessage, $status, $actionUrl));

            // Mengirim email bendahara
            Mail::to($recipientEmailPengurus)->send(new Notification($recipientNamePengurus, $bodyMessagePengurus, $status, $actionUrlPengurus));

            // Mengirim pesan ke Warga
            $responseWarga = $this->fonnteService->sendWhatsAppMessage($phoneNumberWarga, $messageWarga, $imageUrl);

            // Mengirim pesan ke Pengurus
            $responsePengurus = $this->fonnteService->sendWhatsAppMessage($phoneNumberPengurus, $messageKetua, $imageUrl);

            DB::commit();
            // Cek hasil pengiriman
            if (
                (isset($responseWarga['status']) && $responseWarga['status'] == 'success') &&
                (isset($responsePengurus['status']) && $responsePengurus['status'] == 'success')
            ) {
                return back()->with('success', 'Data tersimpan, Notifikasi berhasil dikirim ke Warga dan Ketua!');
            }

            return back()->with('error', 'Data tersimpan, Gagal mengirim notifikasi');

            // DB::commit();
            // return redirect()->back()->with('success', 'Data Pinjaman berhasil di keluarkan, tidak ada notifikasi');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data pengeluaran.' . $e->getMessage());
        }
    }

    public function acknowledged(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);

        DB::beginTransaction();

        try {
            // Mengambil waktu saat ini
            $dateTime = now();

            $data = Loan::findOrFail($id);

            $data->status = $request->status;

            $data->update();
            // -------------------------------------

            // Cek apakah data ini dalam pinjaman ke 2 atau tunggal
            $pinjamanKeDua = LoanExtension::where('new_loan_id', $data->id)->where('status', 'approved')->first();
            if ($pinjamanKeDua) {

                $anggaranPinjaman = Anggaran::where('name', 'Dana Pinjam')->first();
                $anggaranKas = Anggaran::where('name', 'Dana Kas')->first();
                // Cek apakah Saldo cukup berdasarkan anggaran
                $saldo_akhir_pinjaman =  AnggaranSaldo::where('type', $anggaranPinjaman->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
                $saldo_akhir_kas =  AnggaranSaldo::where('type', $anggaranKas->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
                // mengambil Id Saldo di Pinjaman sebelumnya 
                $cek_kode = Loan::findOrFail($pinjamanKeDua->loan_id);
                $cek_id_saldo = Saldo::where('code', $cek_kode->code)->first();
                // Ambil nilai catatan_anggaran dari tabel anggaran_settings untuk menentukan deadline
                $kasihSayang = AnggaranSetting::where('anggaran_id', $anggaranPinjaman->id)
                    ->where('label_anggaran', 'Uang Kasih Sayang')
                    ->first();
                $saldo_pinjaman = new AnggaranSaldo();
                $saldo_pinjaman->type = $anggaranPinjaman->name;
                $saldo_pinjaman->percentage = 0;
                $saldo_pinjaman->amount = '-' . $kasihSayang->catatan_anggaran;
                $saldo_pinjaman->saldo = $saldo_akhir_pinjaman->saldo - $kasihSayang->catatan_anggaran;
                $saldo_pinjaman->saldo_id = $cek_id_saldo->id; //mengambil id dari model saldo di atas
                $saldo_pinjaman->save();

                $saldo_kas = new AnggaranSaldo();
                $saldo_kas->type = $anggaranKas->name;
                $saldo_kas->percentage = 0;
                $saldo_kas->amount = $kasihSayang->catatan_anggaran;
                $saldo_kas->saldo = $saldo_akhir_kas->saldo + $kasihSayang->catatan_anggaran;
                $saldo_kas->saldo_id = $cek_id_saldo->id; //mengambil id dari model saldo di atas
                $saldo_kas->save();
            } else {
                $saldo_terbaru = Saldo::latest()->first();
                $saldo = new Saldo();
                $saldo->code = $data->code;
                $saldo->amount = '-' . $data->loan_amount;
                $saldo->atm_balance = $saldo_terbaru->atm_balance - $data->loan_amount;
                $saldo->total_balance = $saldo_terbaru->total_balance - $data->loan_amount;
                $saldo->ending_balance = $saldo_terbaru->total_balance;
                $saldo->cash_outside = $saldo_terbaru->cash_outside;

                $saldo->save();
                // -------------------------------------------

                $dataAnggaran = Anggaran::where('name', 'Dana Pinjam')->first();
                // Cek apakah Saldo cukup berdasarkan anggaran
                $saldo_akhir_request =  AnggaranSaldo::where('type', $dataAnggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran

                // Hitung alokasi dana berdasarkan catatan_anggaran sebagai persentase
                $percenAmount = ($data->loan_amount / $saldo_akhir_request->saldo) * 100;
                $saldo_anggaran = new AnggaranSaldo();

                $saldo_anggaran->type = $dataAnggaran->name;
                $saldo_anggaran->percentage = $percenAmount;
                $saldo_anggaran->amount = '-' . $data->loan_amount;
                $saldo_anggaran->saldo = $saldo_akhir_request->saldo - $data->loan_amount;
                $saldo_anggaran->saldo_id = $saldo->id; //mengambil id dari model saldo di atas

                $saldo_anggaran->save();
            }
            // Mengambil data pengaju (pengguna yang menginput)
            $pengaju = DataWarga::find(Auth::user()->data_warga_id);
            // Mengambil nomor telepon Ketua Untuk Laporan
            $ketua = User::whereHas('role', function ($query) {
                $query->where('name', 'Ketua');
            })->with('dataWarga')->first();

            $phoneNumberPengurus = $ketua->dataWarga->no_hp ?? null;

            // Data untuk pesan
            $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            $link = "https://keluargamahaya.com/pinjaman/{$encryptedId}";

            // Membuat pesan WhatsApp
            $messageKetua = "*Konfirmasi Penerimaan Dana Pinjaman*\n";
            $messageKetua .= "Halo {$ketua->dataWarga->name},\n\n";
            $messageKetua .= "Kami informasikan bahwa {$pengaju->name} telah mengonfirmasi bahwa dana pinjaman telah diterima. Berikut adalah detail pengajuan:\n\n";
            $messageKetua .= "- *Kode Pinjaman*: {$data->code}\n";
            $messageKetua .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
            $messageKetua .= "- *Nama Warga*: {$pengaju->name}\n";
            $messageKetua .= "- *Di Input*: {$data->sekretaris->name}\n";
            $messageKetua .= "- *Nominal*: Rp" . number_format($data->loan_amount, 0, ',', '.') . "\n";
            $messageKetua .= "\nTerima kasih atas perhatian dan kerjasama Anda.\n\n";
            $messageKetua .= "*Salam hormat,*\n";
            $messageKetua .= "*Sistem Kas Keluarga*";


            // URL gambar dari direktori storage
            $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

            $recipientEmailPengurus = $ketua->dataWarga->email;
            $recipientNamePengurus = $ketua->dataWarga->name;
            $status = "Sudah di Terima";
            // Data untuk email pengurus
            $bodyMessagePengurus = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messageKetua);
            $actionUrlPengurus = $link;

            // Mengirim email bendahara
            Mail::to($recipientEmailPengurus)->send(new Notification($recipientNamePengurus, $bodyMessagePengurus, $status, $actionUrlPengurus));

            // Mengirim pesan ke Pengurus
            $responsePengurus = $this->fonnteService->sendWhatsAppMessage($phoneNumberPengurus, $messageKetua, $imageUrl);

            DB::commit();
            // Cek hasil pengiriman
            if (
                (isset($responsePengurus['status']) && $responsePengurus['status'] == 'success')
            ) {
                return back()->with('success', 'Terimakasih sudah sudah mengkonfirmasi, Semoga bermanfaat.');
            }

            return back()->with('error', 'Data tersimpan, Gagal mengirim notifikasi');

            // Jik nitifikasi di aktifkan return yang ini di hapus
            // DB::commit();

            // return redirect()->back()->with('success', 'Terimakasih sudah sudah mengkonfirmasi, Semoga bermanfaat., notif tidak ada');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan, Mohon beritahu pengurus untuk error ini.' . $e->getMessage());
        }
    }
    public function laporan()
    {
        $pinjaman_proses = Loan::where('status', '!=', 'Paid in Full')->get();
        $pinjaman_selesai = Loan::where('status', 'Paid in Full')->get();



        return view('user.program.kas.laporan.pinjaman', compact('pinjaman_selesai', 'pinjaman_proses'));
    }
}

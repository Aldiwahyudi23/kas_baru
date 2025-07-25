<?php

namespace App\Http\Controllers\User\Kas;

use App\Http\Controllers\Controller;
use App\Mail\Notification;
use App\Models\AccessNotification;
use App\Models\Anggaran;
use App\Models\AnggaranSaldo;
use App\Models\AnggaranSetting;
use App\Models\DataNotification;
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

class BayarPinjamanController extends Controller

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

        $program = Program::where('name', 'Kas Keluarga')->first();
        // Mengecek data pembayran kas yang masih proses
        $cek_kasPayment = loanRepayment::where('data_warga_id', Auth::user()->id)->where('status', 'process')->count();
        // mengambil data kas Anggota
        $data_kasAnggota = loanRepayment::where('data_warga_id', Auth::user()->id)
            ->where('status', 'confirmed')
            ->get();
        $pembayaran_proses = loanRepayment::where('data_warga_id', Auth::user()->id)
            ->where('status', 'process')
            ->first();
        $layout_form = LayoutsForm::first();


        return view('user.program.kas.pembayaran.kas', compact('program', 'cek_kasPayment', 'data_kasAnggota', 'layout_form', 'pembayaran_proses'));
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
        $request->validate(
            [
                'amount' => 'required',
                'payment_method' => 'required',
                'description' => 'required',
            ],
            [
                'amount.required' => 'Nominal Harus di isi',
                'payment_method.required' => 'Pembayaran Harus di isi',
                'description.required' => 'Keterangan Harus di isi',
            ]
        );

        // Mengecek apakah sudah ada pengajuan kas yang sedang diproses
        $cek_pembayaran = loanRepayment::where('status', 'process')->where('data_warga_id', $request->data_warga_id)->count();
        if ($cek_pembayaran >= 1) {
            return redirect()->back()->with('error', 'Pembayaran gagal dikirim. Sudah ada Pembayaran pinjaman yang sedang diproses.');
        }

        $cek_pinjaman = Loan::findOrFail($request->loan_id);
        if ($cek_pinjaman->status == "Paid in Full") {
            return redirect()->back()->with('error', 'Pembayaran gagal Pinjaman sudah Lunas, silahkan cek data dan hubungi pengurus.');
        }

        DB::beginTransaction();

        try {

            // Mengambil waktu saat ini
            $dateTime = now();

            // Format tanggal dan waktu
            $formattedDate = $dateTime->format('dmy'); // Dapatkan format DDMMYY
            $formattedTime = $dateTime->format('His'); // Dapatkan format HHMMSS

            // Menghitung jumlah admin saat ini dan menambahkan 1 untuk urutan
            $bayarPinjaman = loanRepayment::count() + 1;

            // Membuat kode kas
            $code = 'BP-' . $formattedDate . $formattedTime . str_pad($bayarPinjaman, 1, '0', STR_PAD_LEFT);
            // Format akhir: ADM-DDMMYYHHMMSS1

            // menentukan nilai is_deposite sesuai metode pembayran
            $deposit = $request->payment_method === 'cash' ? false : true; // Tunai harus disetorkan, transfer otomatis dianggap deposited


            $data = new loanRepayment();
            $data->code = $code;
            $data->loan_id = $request->loan_id;
            $data->data_warga_id = $request->data_warga_id;
            $data->amount = $request->amount;
            $data->payment_date = $dateTime;
            $data->payment_method = $request->payment_method;
            $data->description = $request->description;
            $data->submitted_by = Auth::user()->data_warga_id;
            // $data->confirmed_by = $request->confirmed_by;
            $data->status = "process";
            // $data->confirmation_date = $request->confirmation_date;
            $data->is_deposited = $deposit;
            // Cek apakah file profile_picture di-upload


            if ($request->hasFile('transfer_receipt_path')) {
                $file = $request->file('transfer_receipt_path');
                $filename = 'Kas-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/kas/bayarPinjaman'), $filename);  // Simpan gambar ke folder public/storage/kas/bayarPinjaman
                $data->transfer_receipt_path = "storage/kas/bayarPinjaman/$filename";  // Simpan path gambar ke database
            }
            $data->save();

            $notif = DataNotification::where('name', 'Bayar Pinjaman')
                ->where('type', 'Pengajuan')
                ->first();

            // ==========================Notif Anggota=======================================

            // Mengambil data pengaju (pengguna yang menginput)
            $pengaju = DataWarga::find(Auth::user()->data_warga_id);

            // Data Warga
            $data_warga = DataWarga::find($request->data_warga_id);
            $phoneNumberWarga = $data_warga->no_hp;
            // URL gambar dari direktori storage
            $imageUrl = '';

            // Pesan untuk Warga
            $messageWarga = "*Pembayaran Pinjaman Berhasil*\n";
            $messageWarga .= "Selamat {$data_warga->name},  pembayaran pinjaman Anda telah berhasil kami terima dan sedang dalam proses peninjauan oleh pengurus.\n\n";
            $messageWarga .= "Berikut adalah detail pembayaran Anda:\n";
            $messageWarga .= "- *Kode*: {$code}\n";
            $messageWarga .= "- *Tanggal Pembayaran*: {$data->payment_date}\n";
            $messageWarga .= "- *Nama*: {$data_warga->name}\n";
            $messageWarga .= "- *Di Input*: {$pengaju->name}\n";
            $messageWarga .= "- *Nominal*: Rp" . number_format($request->amount, 0, ',', '.') . "\n";
            $messageWarga .= "- *Keterangan*: {$request->description}\n\n";
            $messageWarga .= "Mohon menunggu konfirmasi dari pengurus. Jika ada pertanyaan, silakan hubungi pengurus melalui kontak resmi.\n\n";
            $messageWarga .= "*Terima kasih telah kerjasama untuk pembayaran pinjaman.*\n";
            $messageWarga .= "*Salam,*\n";
            $messageWarga .= "*Pengurus Kas Keluarga*";

            // mengirim ke email 
            $recipientEmail = $data_warga->email;
            $recipientName = $data_warga->name;
            // Ganti tanda bintang dengan HTML <strong>
            $bodyMessage = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messageWarga);
            $status = $data->status;
            $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            $actionUrl = "https://keluargamahaya.com/bayar-pinjaman/{$encryptedId}";

            if ($notif->wa_notification  && $notif->anggota && !empty($phoneNumberWarga)) {
                // Mengirim pesan ke Warga
                $responseWarga = $this->fonnteService->sendWhatsAppMessage($phoneNumberWarga, $messageWarga, $imageUrl);
            }
            if ($notif->email_notification && $notif->anggota && !empty($recipientEmail)) {
                // Mengirim notifikasi email ke anggota
                Mail::to($recipientEmail)->send(new Notification($recipientName, $bodyMessage, $status, $actionUrl));
            }

            // ============================Notif untuk pengurus=========================================================

            // Mengambil data warga berdasarkan acceess Notif
            $notifPengurus = AccessNotification::where('notification_id', $notif->id)->where('is_active', true)->get();

            foreach ($notifPengurus as $notif_pengurus) {

                $phoneNumberPengurus = $notif_pengurus->Warga->no_hp ?? null;
                $encryptedIdpengurus = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
                $actionUrlPengurus = "https://keluargamahaya.com/confirm/bayar-pinjaman/{$encryptedIdpengurus}";

                // Pesan untuk Pengurus
                $messagePengurus = "*Notifikasi Pembayaran Pinjaman Baru*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name}.\n\n";
                $messagePengurus .= "Telah diterima pembayaran pinjaman yang memerlukan konfirmasi Anda.\n\n";
                $messagePengurus .= "Berikut adalah detail pembayaran:\n";
                $messagePengurus .= "- *Kode*: {$code}\n";
                $messagePengurus .= "- *Tanggal Pembayaran*: {$data->payment_date}\n";
                $messagePengurus .= "- *Nama*: {$data_warga->name}\n";
                $messagePengurus .= "- *Di Input*: {$pengaju->name}\n";
                $messagePengurus .= "- *Nominal*: Rp" . number_format($request->amount, 0, ',', '.') . "\n";
                $messagePengurus .= "- *Keterangan*: {$request->description}\n\n";
                $messagePengurus .= "Silakan cek dan konfirmasi pembayaran ini melalui link berikut:\n";
                $messagePengurus .= "- *Link Konfirmasi*: " . $actionUrlPengurus . "\n\n";
                $messagePengurus .= "*Harap segera melakukan konfirmasi untuk memastikan status pembayaran.*\n\n";
                $messagePengurus .= "*Salam,*\n";
                $messagePengurus .= "*Sistem Kas Keluarga*";

                // URL gambar dari direktori storage
                $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

                $recipientEmailPengurus = $notif_pengurus->Warga->email;
                $recipientNamePengurus = $notif_pengurus->Warga->name;
                // Data untuk email pengurus
                $bodyMessagePengurus = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messagePengurus);

                if ($notif->email_notification && $notif->pengurus) {
                    // Mengirim email notif_pengurus
                    Mail::to($recipientEmailPengurus)->send(new Notification($recipientNamePengurus, $bodyMessagePengurus, $status, $actionUrlPengurus));
                }
                if ($notif->wa_notification && $notif->pengurus) {
                    // Mengirim pesan ke Pengurus
                    $responsePengurus = $this->fonnteService->sendWhatsAppMessage($phoneNumberPengurus, $messagePengurus, $imageUrl);
                }
            }
            DB::commit();
            // Cek hasil pengiriman
            // Evaluasi keberhasilan pengiriman
            $wargaSuccess = isset($responseWarga['status']) && $responseWarga['status'] === 'success';
            $pengurusSuccess = isset($responsePengurus['status']) && $responsePengurus['status'] === 'success';

            // Berikan feedback berdasarkan hasil pengiriman
            if ($wargaSuccess && $pengurusSuccess) {
                return back()->with('success', 'Data tersimpan, Notifikasi berhasil dikirim ke Warga dan Pengurus!');
            } elseif ($wargaSuccess) {
                return back()->with('success', 'Data tersimpan, Notifikasi berhasil dikirim ke Warga, tetapi gagal ke Pengurus.');
            } elseif ($pengurusSuccess) {
                return back()->with('success', 'Data tersimpan, Notifikasi berhasil dikirim ke Pengurus, tetapi gagal ke Warga.');
            } else {
                return back()->with('warning', 'Data tersimpan, tetapi Notifikasi tidak terkirim ke Warga maupun Pengurus!');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat pembayaran.' . $e->getMessage());
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
        $bayarPinjaman = loanRepayment::findOrFail($id);

        return view('user.program.kas.detail.show_bayarPinjaman', compact('bayarPinjaman'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = Crypt::decrypt($id);
        $bayarPinjaman = loanRepayment::findOrFail($id);
        if ($bayarPinjaman->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $bayarPinjaman->status);
        }
        return view('user.program.kas.edit.bayarPinjaman', compact('bayarPinjaman'));
    }
    public function editPengurus(string $id)
    {
        $id = Crypt::decrypt($id);
        $bayarPinjaman = loanRepayment::findOrFail($id);
        if ($bayarPinjaman->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $bayarPinjaman->status);
        }
        return view('user.program.kas.edit.pengurus.bayarPinjaman', compact('bayarPinjaman'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = crypt::decrypt($id);
        $request->validate(
            [
                'amount' => 'required',
                'payment_method' => 'required',
                'description' => 'required',
            ],
            [
                'amount.required' => 'Nominal Harus di isi',
                'payment_method.required' => 'Pembayaran Harus di isi',
                'description.required' => 'Keterangan Harus di isi',
            ]
        );
        $bayarPinjaman = loanRepayment::findOrFail($id);
        if ($bayarPinjaman->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $bayarPinjaman->status);
        }

        $data = loanRepayment::findOrFail($id);
        $data->data_warga_id = $request->data_warga_id;
        $data->amount = $request->amount;
        $data->payment_method = $request->payment_method;
        $data->description = $request->description;
        // Cek apakah file profile_picture di-upload
        // if ($request->hasFile('transfer_receipt_path')) {
        //     $file = $request->file('transfer_receipt_path');
        //     $path = $file->store(
        //         'kas/pemasukan',
        //         'public'
        //     ); // Simpan gambar ke direktori public
        //     $data->transfer_receipt_path = $path;
        // }


        if ($request->hasFile('transfer_receipt_path')) {
            $file = $request->file('transfer_receipt_path');
            $filename = 'Kas-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/kas/bayarPinjaman'), $filename);  // Simpan gambar ke folder public/storage/kas/bayarPinjaman
            $data->transfer_receipt_path = "storage/kas/bayarPinjaman/$filename";  // Simpan path gambar ke database
        }

        $data->update();

        return redirect()->route('bayar-pinjaman.show', Crypt::encrypt($id))->with('success', 'Berhasil di rubah');
    }
    public function updatePengurus(Request $request, string $id)
    {
        $id = crypt::decrypt($id);
        $request->validate(
            [
                'amount' => 'required',
                'payment_method' => 'required',
                'description' => 'required',
            ],
            [
                'amount.required' => 'Nominal Harus di isi',
                'payment_method.required' => 'Pembayaran Harus di isi',
                'description.required' => 'Keterangan Harus di isi',
            ]
        );
        $bayarPinjaman = loanRepayment::findOrFail($id);
        if ($bayarPinjaman->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $bayarPinjaman->status);
        }

        $data = loanRepayment::findOrFail($id);
        $data->data_warga_id = $request->data_warga_id;
        $data->amount = $request->amount;
        $data->payment_method = $request->payment_method;
        $data->description = $request->description;

        if ($request->hasFile('transfer_receipt_path')) {
            $file = $request->file('transfer_receipt_path');
            $filename = 'Kas-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/kas/bayarPinjaman'), $filename);  // Simpan gambar ke folder public/storage/kas/bayarPinjaman
            $data->transfer_receipt_path = "storage/kas/bayarPinjaman/$filename";  // Simpan path gambar ke database
        }

        $data->update();

        return redirect()->route('bayar-pinjaman.show.confirm', Crypt::encrypt($id))->with('success', 'Berhasil di rubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = loanRepayment::find($id);
        if ($data->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $data->status);
        } else {
            $data->delete();

            return redirect()->route('bayar-pinjaman.pembayaran', Crypt::encrypt($data->loan_id))->with('success', 'Pembayaran sudah di hapus');
        }
    }
    public function destroyPengurus(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = loanRepayment::find($id);
        if ($data->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $data->status);
        } else {
            $data->delete();

            return redirect()->route('bayar-pinjaman.pengajuan')->with('success', 'Pembayaran sudah di hapus');
        }
    }

    public function pembayaran(string $id)
    {
        $id = Crypt::decrypt($id);
        $pinjaman = Loan::findOrFail($id);
        $bayarPinjaman = loanRepayment::where('loan_id', $id)->get();
        // Ambil tanggal pembayaran terakhir
        $lastRepayment = LoanRepayment::where('loan_id', $pinjaman->id)->where('status', 'confirmed')
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
        // Cek jika selisih antara pembayaran terakhir dan pengajuan baru kurang dari sebulan
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
        // mengecek pengajuan ke 2
        $cek_pengajuan = LoanExtension::where('loan_id', $pinjaman->id)->where('status',  'pending')->latest('created_at')->first();
        $cek_pinjaman_2 = LoanExtension::where('new_loan_id', $pinjaman->id)->where('status',  'approved')->latest('created_at')->first();

        $layout_form = LayoutsForm::first();
        $cek_pembayaran = loanRepayment::where('loan_id', $id)->where('status', '!=', 'confirmed')->first();

        return view('user.program.kas.pembayaran.bayarPinjaman', compact('pinjaman', 'cek_pembayaran', 'layout_form', 'bayarPinjaman', 'waktuPembayaran', 'waktuDitentukan', 'hitungWaktu', 'cek_pengajuan', 'cek_pinjaman_2'));
    }

    public function pengajuan()
    {
        $bayarPinjaman_proses = loanRepayment::where('status', 'process')->get();
        $bayarPinjaman_pending = loanRepayment::where('status', 'pending')->get();
        $bayarPinjaman_reject = loanRepayment::where('status', 'reject')->get();

        return view('user.program.kas.pengajuan.bayarPinjaman', compact('bayarPinjaman_proses', 'bayarPinjaman_pending', 'bayarPinjaman_reject'));
    }

    public function show_confirm($id)
    {
        //Untuk konfirmasi delete
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $id = Crypt::decrypt($id);
        $pembayarPinjaman = loanRepayment::findOrFail($id);
        $pinjaman = Loan::findOrFail($pembayarPinjaman->loan_id);
        $bayarPinjaman = loanRepayment::where('loan_id', $pinjaman->id)->get();
         // Ambil tanggal pembayaran terakhir
        $lastRepayment = LoanRepayment::where('loan_id', $pinjaman->id)->where('status', 'confirmed')
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
        // Cek jika selisih antara pembayaran terakhir dan pengajuan baru kurang dari sebulan
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
        // mengecek pengajuan ke 2
        $cek_pengajuan = LoanExtension::where('loan_id', $pinjaman->id)->where('status',  'pending')->latest('created_at')->first();
        $cek_pinjaman_2 = LoanExtension::where('new_loan_id', $pinjaman->id)->where('status',  'approved')->latest('created_at')->first();

        $layout_form = LayoutsForm::first();
        $cek_pembayaran = loanRepayment::where('loan_id', $id)->where('status', '!=', 'confirmed')->first();


        return view('user.program.kas.konfirmasi.bayarPinjaman', compact('pembayarPinjaman','pinjaman', 'cek_pembayaran', 'layout_form', 'bayarPinjaman', 'waktuPembayaran', 'waktuDitentukan', 'hitungWaktu', 'cek_pengajuan', 'cek_pinjaman_2'));
    }

    public function confirm(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'data_warga_id' => 'required',
            'amount' => 'required',
            'payment_method' => 'required',
            'description' => 'required',
            'submitted_by' => 'required',
            'status' => 'required',
            'is_deposited' => 'required',
        ]);
        $dataAnggaran = Anggaran::where('name', 'Dana Pinjam')->first();
        // Cek apakah Saldo cukup berdasarkan anggaran
        $saldo_akhir_request =  AnggaranSaldo::where('type', 'Dana Pinjam')->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran

        // jika pilihan status nya pending maka berhenti sampai sini
        if ($request->status == "pending") {
            // Mengambil waktu saat ini
            $dateTime = Carbon::now();

            $data = loanRepayment::findOrFail($id);
            $data->confirmed_by = Auth::user()->data_warga_id;
            $data->status = $request->status;
            $data->confirmation_date = $dateTime;

            $data->update();
            return redirect()->back()->with('success', 'Pembayaran kas berhasil Pending');
        }

        DB::beginTransaction();

        try {

            // Ambil pengajuan dengan row-level locking untuk mencegah race condition
            $pengajuan = loanRepayment::where('id', $id)->lockForUpdate()->first();

            // Validasi apakah pengajuan sudah disetujui
            if ($pengajuan->status === 'confirmed') {
                DB::rollBack();
                return back()->with('error', 'Pengajuan sudah di Konfirmasi ');
            }

            // Mengambil waktu saat ini
            $dateTime = Carbon::now();

            $data = loanRepayment::findOrFail($id);
            $data->data_warga_id = $request->data_warga_id;
            $data->amount = $request->amount;
            $data->payment_method = $request->payment_method;
            $data->description = $request->description;
            $data->submitted_by = $request->submitted_by;
            $data->confirmed_by = Auth::user()->data_warga_id;
            $data->status = $request->status;
            $data->confirmation_date = $dateTime;
            $data->is_deposited = $request->is_deposited;

            $data->update();

            // ----------------------------------------
            $loan = Loan::findOrFail($data->loan_id); // Ambil data loan berdasarkan ID
            // Hitung sisa saldo setelah pembayaran
            $cek_sisa = $loan->remaining_balance - $request->amount;
            // Ambil tanggal pembayaran terakhir
            $lastRepayment = LoanRepayment::where('loan_id', $loan->id)
                ->latest('payment_date')
                ->first();
            // Tanggal pembuatan pinjaman terbaru
            $loanCreationDate = Carbon::parse($loan->created_at);
            // Tanggal pembayaran terakhir
            $lastPaymentDate = Carbon::parse($lastRepayment->payment_date);
            // Cek jika selisih antara pembayaran terakhir dan pengajuan baru kurang dari sebulan
            $daysDifference = $loanCreationDate->diffInDays($lastPaymentDate);
            // mengecek untuk data yang telah di atau runtuk pembayaran yang tanpa lebih
            $pembayaranTanpaLebih = AnggaranSetting::where('label_anggaran', 'Pembayaran tanpa lebih (hari)')
                ->where('anggaran_id', $loan->anggaran_id)
                ->first();
            $tanpaLebih = intval($pembayaranTanpaLebih->catatan_anggaran);
            $waktuDitentukan = $tanpaLebih;

            if ($cek_sisa < 0) {
                // Jika pembayaran melebihi saldo yang tersisa
                $loan->overpayment_balance += abs($cek_sisa); // Tambahkan ke overpayment
                $loan->remaining_balance = 0; // Set remaining balance ke 0
                $loan->status = 'paid in full'; // Ubah status menjadi paid in full
            } elseif ($cek_sisa == 0) {
                // Jika saldo pas habis setelah pembayaran
                $loan->remaining_balance = 0;
                if ($daysDifference < $waktuDitentukan) {
                    $loan->status = 'paid in full'; // Ubah status menjadi paid in full
                } else {
                    $loan->status = 'In Repayment'; // Ubah status menjadi paid in full
                }
            } else {
                // Jika saldo masih tersisa setelah pembayaran
                $loan->remaining_balance = $cek_sisa;
                $loan->status = 'In Repayment'; // Ubah status menjadi paid in full
            }

            // Simpan perubahan pada loan
            $loan->update();

            // -------------------------------------------------

            $saldo_terbaru = Saldo::latest()->first();
            $saldo = new Saldo();
            $saldo->code = $data->code;
            $saldo->amount = $data->amount;
            if ($request->payment_method === "transfer") {
                $atm = ($saldo_terbaru->atm_balance ?? 0) + $data->amount;
                $out = ($saldo_terbaru->cash_outside ?? 0);
            } else if ($request->payment_method === "cash") {
                $atm = ($saldo_terbaru->atm_balance ?? 0);
                $out = ($saldo_terbaru->cash_outside ?? 0) + $data->amount;
            };
            $saldo->atm_balance = $atm;
            $saldo->total_balance = ($saldo_terbaru->total_balance ?? 0) + $data->amount;
            $saldo->ending_balance = ($saldo_terbaru->total_balance ?? 0);
            $saldo->cash_outside = $out;

            $saldo->save();

            // -------------------------------------------
            $dataAnggaran = Anggaran::where('name', 'Dana Pinjam')->first();
            // Cek apakah Saldo cukup berdasarkan anggaran
            $saldo_akhir_request =  AnggaranSaldo::where('type', $dataAnggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran

            if ($cek_sisa < 0) {
                $jumlah = $data->amount - abs($cek_sisa);
            } else {
                $jumlah = $data->amount;
            }

            // Hitung alokasi dana berdasarkan catatan_anggaran sebagai persentase
            $percenAmount = ($jumlah / $loan->loan_amount) * 100;
            $saldo_anggaran = new AnggaranSaldo();
            $saldo_anggaran->type = $dataAnggaran->name;
            $saldo_anggaran->percentage = $percenAmount;
            $saldo_anggaran->amount =  $jumlah;
            $saldo_anggaran->saldo = $saldo_akhir_request->saldo + $jumlah;
            $saldo_anggaran->saldo_id = $saldo->id; //mengambil id dari model saldo di atas
            $saldo_anggaran->save();


            // mengecek untuk data yang telah di atau runtuk pembayaran yang tanpa lebih
            $lebihPinjaman = AnggaranSetting::where('label_anggaran', 'Alokasi Lebih Pinjaman')
                ->where('anggaran_id', $loan->anggaran_id)
                ->first();
            // Jika ada overpayment, masukkan ke Dana Kas
            $percenAmount = (abs($cek_sisa) / $loan->loan_amount) * 100;
            // $saldo_akhir_kas =  AnggaranSaldo::where('type', 'Dana Kas')->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
            $saldo_akhir_kas =  AnggaranSaldo::where('type', $lebihPinjaman->catatan_anggaran)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
            if ($cek_sisa < 0) {
                $saldoAnggaranKas = new AnggaranSaldo();
                $saldoAnggaranKas->type = $lebihPinjaman->catatan_anggaran;
                $saldoAnggaranKas->percentage = $percenAmount;
                $saldoAnggaranKas->amount += abs($cek_sisa); // Masukkan nominal overpayment
                $saldoAnggaranKas->saldo = $saldo_akhir_kas->saldo + abs($cek_sisa);
                $saldoAnggaranKas->saldo_id = $saldo->id; // ID saldo dari model Saldo
                $saldoAnggaranKas->save();
            }

            $notif = DataNotification::where('name', 'Bayar Pinjaman')
                ->where('type', 'Konfirmasi')
                ->first();

            // ==========================Notif Anggota=======================================


            // Mengambil data pengaju (pengguna yang menginput)
            $pengaju = DataWarga::find($request->submitted_by);

            // Data Warga
            $data_warga = DataWarga::find($request->data_warga_id);
            $phoneNumberWarga = $data_warga->no_hp;
            // URL gambar dari direktori storage
            $imageUrl = '';

            // Pesan untuk Warga
            $messageWarga = "*Pembayaran Pinjaman Terkonfirmasi*\n";
            $messageWarga .= "Selamat {$data_warga->name}, pembayaran Pinjaman Anda telah berhasil dikonfirmasi oleh " . Auth::user()->name . " \n\n";
            $messageWarga .= "Berikut adalah detail pembayaran Anda:\n";
            $messageWarga .= "- *Kode* : {$request->code}\n";
            $messageWarga .= "- *Tanggal Pembayaran* : {$data->payment_date}\n";
            $messageWarga .= "- *Nama* : {$data_warga->name}\n";
            $messageWarga .= "- *Di Input Oleh* : {$pengaju->name}\n";
            $messageWarga .= "- *Nominal* : Rp" . number_format($request->amount, 0, ',', '.') . "\n\n";

            $messageWarga .= "Berikut adalah detail pinjaman Anda:\n";
            $messageWarga .= "- *Kode Pinjaman* : {$loan->code}\n";
            $messageWarga .= "- *Status Pinjaman* : " . ($loan->status === 'paid in full' ? 'Lunas' : 'Belum Lunas') . "\n";
            $messageWarga .= "- *Jumlah Pinjaman* : Rp" . number_format($loan->loan_amount, 0, ',', '.') . "\n";
            $messageWarga .= "- *Sisa Pinjaman* : Rp" . number_format($loan->remaining_balance, 0, ',', '.') . "\n\n";
            if ($loan->overpayment_balance > 1) {
                $messageWarga .= "- *Lebih* : Rp" . number_format(
                    $loan->overpayment_balance,
                    0,
                    ',',
                    '.'
                ) . "\n\n";
            }
            $messageWarga .= "Pembayaran terakhir yang telah masuk:\n";

            // Mengambil pembayaran dari tabel LoanPayment berdasarkan loan_id
            $payments = LoanRepayment::where('loan_id', $loan->id)->get();

            if ($payments->isNotEmpty()) {
                foreach ($payments as $payment) {
                    $messageWarga .= "- Rp" . number_format($payment->amount, 0, ',', '.') . " pada tanggal " . $payment->created_at->format('d-m-Y') . "\n";
                }
            } else {
                $messageWarga .= "Belum ada pembayaran yang tercatat.\n";
            }

            $messageWarga .= "\nTerima kasih telah memenuhi kewajiban pembayaran pinjaman. Jika Anda memiliki pertanyaan lebih lanjut, silakan hubungi pengurus melalui kontak resmi.\n\n";
            $messageWarga .= "*Semoga hari Anda menyenangkan!*\n\n";
            $messageWarga .= "*Salam hangat,*\n";
            $messageWarga .= "*Pengurus Kas Keluarga*";



            // mengirim ke email 
            $recipientEmail = $data_warga->email;
            $recipientName = $data_warga->name;
            // Ganti tanda bintang dengan HTML <strong>
            $bodyMessage = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messageWarga);
            $status = $data->status;
            $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            $actionUrl = "https://keluargamahaya.com/bayar-pinjaman/{$encryptedId}";

            if ($notif->wa_notification  && $notif->anggota && !empty($phoneNumberWarga)) {
                // Mengirim pesan ke Warga
                $responseWarga = $this->fonnteService->sendWhatsAppMessage($phoneNumberWarga, $messageWarga, $imageUrl);
            }
            if ($notif->email_notification && $notif->anggota && !empty($recipientEmail)) {
                // Mengirim notifikasi email ke anggota
                Mail::to($recipientEmail)->send(new Notification($recipientName, $bodyMessage, $status, $actionUrl));
            }

            // ============================Notif untuk pengurus=========================================================

            $notifPengurus = AccessNotification::where('notification_id', $notif->id)->where('is_active', true)->get();
            foreach ($notifPengurus as $notif_pengurus) {

                $phoneNumberPengurus = $notif_pengurus->Warga->no_hp ?? null;

                // Pesan untuk Ketua
                $messagePengurus = "*Laporan Pembayaran Pinjaman Terkonfirmasi*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name}\n\n";
                $messagePengurus .= "Berikut adalah laporan pembayaran Pinjaman yang telah berhasil dikonfirmasi oleh  " . Auth::user()->name . " \n\n";
                $messagePengurus .= "- *Kode* : {$request->code}\n";
                $messagePengurus .= "- *Tanggal Pembayaran* : {$data->payment_date}\n";
                $messagePengurus .= "- *Nama* : {$data_warga->name}\n";
                $messagePengurus .= "- *Di Input Oleh* : {$pengaju->name}\n";
                $messagePengurus .= "- *Nominal* : Rp" . number_format($request->amount, 0, ',', '.') . "\n\n";

                $messagePengurus .= "Berikut adalah detail pinjaman nya :\n";
                $messagePengurus .= "- *Kode Pinjaman* : {$loan->code}\n";
                $messagePengurus .= "- *Status Pinjaman* : " . ($loan->status === 'paid in full' ? 'Lunas' : 'Belum Lunas') . "\n";
                $messagePengurus .= "- *Jumlah Pinjaman* : Rp" . number_format($loan->loan_amount, 0, ',', '.') . "\n";
                $messagePengurus .= "- *Sisa Pinjaman* : Rp" . number_format($loan->remaining_balance, 0, ',', '.') . "\n";
                if ($loan->overpayment_balance > 1) {
                    $messagePengurus .= "- *Lebih* : Rp" . number_format($loan->overpayment_balance, 0, ',', '.') . "\n\n";
                }
                $messagePengurus .= "Pembayaran terakhir yang telah masuk:\n";

                // Mengambil pembayaran dari tabel LoanPayment berdasarkan loan_id
                $payments = LoanRepayment::where('loan_id', $loan->id)->get();

                if ($payments->isNotEmpty()) {
                    foreach ($payments as $payment) {
                        $messagePengurus .= "- Rp" . number_format($payment->amount, 0, ',', '.') . " pada tanggal " . $payment->created_at->format('d-m-Y') . "\n";
                    }
                } else {
                    $messagePengurus .= "Belum ada pembayaran yang tercatat.\n";
                }

                $messagePengurus .= "Pembayaran ini telah diproses dan dikonfirmasi oleh pengurus.\n\n";
                $messagePengurus .= "*Salam hormat,*\n";
                $messagePengurus .= "*Sistem Kas Keluarga*";


                // URL gambar dari direktori storage
                $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

                $recipientEmailPengurus = $notif_pengurus->Warga->email;
                $recipientNamePengurus = $notif_pengurus->Warga->name;
                // Data untuk email pengurus
                $bodyMessagePengurus = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messagePengurus);
                $encryptedIdPengurus = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
                $actionUrlPengurus = "https://keluargamahaya.com/bayar-pinjaman/{$encryptedIdPengurus}";

                if ($notif->email_notification && $notif->pengurus) {
                    // Mengirim email notif_pengurus
                    Mail::to($recipientEmailPengurus)->send(new Notification($recipientNamePengurus, $bodyMessagePengurus, $status, $actionUrlPengurus));
                }
                if ($notif->wa_notification && $notif->pengurus) {
                    // Mengirim pesan ke Pengurus
                    $responsePengurus = $this->fonnteService->sendWhatsAppMessage($phoneNumberPengurus, $messagePengurus, $imageUrl);
                }
            }
            DB::commit();
            // Cek hasil pengiriman
            // Evaluasi keberhasilan pengiriman
            $wargaSuccess = isset($responseWarga['status']) && $responseWarga['status'] === 'success';
            $pengurusSuccess = isset($responsePengurus['status']) && $responsePengurus['status'] === 'success';

            // Berikan feedback berdasarkan hasil pengiriman
            if ($wargaSuccess && $pengurusSuccess) {
                return back()->with('success', 'Data terkonfirmasi, Notifikasi berhasil dikirim ke Warga dan Pengurus!');
            } elseif ($wargaSuccess) {
                return back()->with('success', 'Data terkonfirmasi, Notifikasi berhasil dikirim ke Warga, tetapi gagal ke Pengurus.');
            } elseif ($pengurusSuccess) {
                return back()->with('success', 'Data terkonfirmasi, Notifikasi berhasil dikirim ke Pengurus, tetapi gagal ke Warga.');
            } else {
                return back()->with('warning', 'Data terkonfirmasi, tetapi Notifikasi tidak terkirim ke Warga maupun Pengurus!');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan.' . $e->getMessage());
        }
    }
}

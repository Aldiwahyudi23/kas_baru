<?php

namespace App\Http\Controllers\User\Kas;

use App\Events\SaldoUpdated;
use App\Http\Controllers\Controller;
use App\Mail\Notification;
use App\Models\AccessNotification;
use App\Models\AccessProgram;
use App\Models\AnggaranSaldo;
use App\Models\AnggaranSetting;
use App\Models\DataNotification;
use App\Models\DataWarga;
use App\Models\KasPayment;
use App\Models\LayoutsForm;
use App\Models\Program;
use App\Models\ProgramSetting;
use App\Models\Role;
use App\Models\Saldo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class PemasukanController extends Controller

{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)

    {

        //Untuk konfirmasi delete
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $program = Program::where('name', 'Kas Keluarga')->first();
        // Mengecek data pembayran kas yang masih proses
        $cek_kasPayment = KasPayment::where('data_warga_id', Auth::user()->data_warga_id)->where('status', 'process')->count();
        // mengambil data kas Anggota
        $data_kasAnggota = KasPayment::where('data_warga_id', Auth::user()->data_warga_id)
            ->where('status', 'confirmed')
            ->get();
        $pembayaran_proses = KasPayment::where('data_warga_id', Auth::user()->data_warga_id)
            ->where('status', 'process')
            ->first();
        $layout_form = LayoutsForm::first();

        $program = Program::where('name', 'Kas Keluarga')->first();
        $access = AccessProgram::where('program_id', $program->id)->get();


        $filter_tahun = $request->get('filter_tahun', Carbon::now()->year);

        // Ambil data dari database berdasarkan tahun
        $data_kasAnggota = KasPayment::whereYear('created_at', $filter_tahun)
            ->get();

        // Buat array untuk menyusun data per bulan
        $monthlyData = collect();
        foreach (range(1, Carbon::now()->month) as $month) {
            $monthlyData->put($month, [
                'month_name' => Carbon::create()->month($month)->translatedFormat('F'),
                'payments' => $data_kasAnggota->filter(function ($item) use ($month) {
                    return $item->created_at->month == $month;
                }),
            ]);
        }

        // Mendapatkan tahun yang tersedia dari data
        $available_years = KasPayment::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year');
        // =====================menghitung pembayaran yang kosong=========================
        // // Periode awal (Januari 2025)
        // $startDate = Carbon::create(2024, 8, 1);
        // $currentDate = Carbon::now();

        // // Hitung total bulan antara periode
        // $totalMonths = $startDate->diffInMonths($currentDate) + 1; // +1 untuk menyertakan bulan saat ini

        // // Ambil data bulan dari KasPayment
        // $pembayaranBulan = KasPayment::where('data_warga_id', Auth::user()->data_warga_id)
        //     ->whereBetween('created_at', [$startDate, $currentDate])
        //     ->get()
        //     ->map(function ($payment) {
        //         return Carbon::parse($payment->created_at)->format('Y-m'); // Format tahun-bulan
        //     })
        //     ->unique()
        //     ->toArray();

        // // Buat daftar semua bulan dari periode
        // $allMonths = collect();
        // for ($i = 0; $i < $totalMonths; $i++) {
        //     $allMonths->push($startDate->copy()->addMonths($i)->format('Y-m'));
        // }

        // // Identifikasi bulan kosong
        // $bulanKosong = $allMonths->diff($pembayaranBulan)->values();

        // // Hitung sisa pembayaran
        // $kasPerBulan = 50000; // Jumlah kas per bulan
        // $sisaPembayaran = $bulanKosong->count() * $kasPerBulan;

        // Tambahkan , 'bulanKosong', 'sisaPembayaran' di compak di bawah 

        return view('user.program.kas.pembayaran.kas', compact('program', 'cek_kasPayment', 'data_kasAnggota', 'layout_form', 'pembayaran_proses', 'access', 'monthlyData', 'available_years', 'filter_tahun'));
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

        // Untuk mengecek nominal sesuai nu di tentukan
        $programItems = ProgramSetting::where('label_program', 'nominal')->first();
        // JIka nominal yang di masukan kurang dari nominal yang di tentukan
        if ($request->amount < $programItems->catatan_program) {
            return redirect()->back()->with('error',  'Nominal yang di masukan kurang dari kesepakatan, ( ' . $programItems->catatan_program . ' )');
        }
        // Mengecek apakah sudah ada pengajuan kas yang sedang diproses
        $cek_kasPayment = KasPayment::where('status', 'process')->where('data_warga_id', Auth::user()->data_warga_id)->count();
        if ($cek_kasPayment >= 1) {
            return redirect()->back()->with('error', 'Pengajuan gagal dikirim. Sudah ada pengajuan kas yang sedang diproses.');
        }

        DB::beginTransaction();

        try {

            // Mengambil waktu saat ini
            $dateTime = now();

            // Format tanggal dan waktu
            $formattedDate = $dateTime->format('dmy'); // Dapatkan format DDMMYY
            $formattedTime = $dateTime->format('His'); // Dapatkan format HHMMSS

            // Menghitung jumlah admin saat ini dan menambahkan 1 untuk urutan
            $kasCount = KasPayment::count() + 1;

            // Membuat kode kas
            $code = 'KAS-' . $formattedDate . $formattedTime . str_pad($kasCount, 1, '0', STR_PAD_LEFT);
            // Format akhir: ADM-DDMMYYHHMMSS1

            // menentukan nilai is_deposite sesuai metode pembayran
            $deposit = $request->payment_method === 'cash' ? false : true; // Tunai harus disetorkan, transfer otomatis dianggap deposited
            if ($request->data_warga_id) {
                $warga = $request->data_warga_id;
            } else {
                $warga = Auth::user()->data_warga_id;
            }

            $data = new KasPayment();
            $data->code = $code;
            $data->data_warga_id = $warga;
            $data->amount = $request->amount;
            $data->payment_date = $dateTime;
            $data->payment_method = $request->payment_method;
            $data->description = $request->description;
            $data->submitted_by = Auth::user()->data_warga_id;
            // $data->confirmed_by = $request->confirmed_by;
            $data->status = "process";
            $data->is_deposited = $deposit;
            // $data->confirmation_date = $request->confirmation_date;
            // $data->is_deposited = $request->is_deposited;
            // Cek apakah file profile_picture di-upload
            if ($request->hasFile('transfer_receipt_path')) {
                $file = $request->file('transfer_receipt_path');
                $filename = 'Kas-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/kas/pemasukan'), $filename);  // Simpan gambar ke folder public/storage/kas/pemasukan
                $data->transfer_receipt_path = "storage/kas/pemasukan/$filename";  // Simpan path gambar ke database
            }

            $data->save();

            $notif = DataNotification::where('name', 'Kas Payment')
                ->where('type', 'Pengajuan')
                ->first();

            // ==========================Notif Anggota=======================================
            // Mengambil data pengaju (pengguna yang menginput)
            $pengaju = DataWarga::find(Auth::user()->data_warga_id);

            // Data Warga
            $data_warga = DataWarga::find($warga);
            $phoneNumberWarga = $data_warga->no_hp;
            // URL gambar dari direktori storage
            $imageUrl = '';

            // Pesan untuk Warga
            $messageWarga = "*Pembayaran Kas Berhasil*\n";
            $messageWarga .= "Selamat {$data_warga->name},  pembayaran kas Anda telah berhasil kami terima dan sedang dalam proses peninjauan oleh pengurus.\n\n";
            $messageWarga .= "Berikut adalah detail pembayaran Anda:\n";
            $messageWarga .= "- *Kode*: {$code}\n";
            $messageWarga .= "- *Tanggal Pembayaran*: {$data->payment_date}\n";
            $messageWarga .= "- *Nama*: {$data_warga->name}\n";
            $messageWarga .= "- *Di Input*: {$pengaju->name}\n";
            $messageWarga .= "- *Nominal*: Rp" . number_format($request->amount, 0, ',', '.') . "\n";
            $messageWarga .= "- *Keterangan*: {$request->description}\n\n";
            $messageWarga .= "Mohon menunggu konfirmasi dari pengurus. Jika ada pertanyaan, silakan hubungi pengurus melalui kontak resmi.\n\n";

            $messageWarga .= "*Terima kasih telah memenuhi kewajiban pembayaran kas.*\n";
            $messageWarga .= "*Salam,*\n";
            $messageWarga .= "*Pengurus Kas Keluarga*";

            // mengirim ke email 
            $recipientEmail = $data_warga->email;
            $recipientName = $data_warga->name;
            // Ganti tanda bintang dengan HTML <strong>
            $bodyMessage = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messageWarga);
            $status = $data->status;

            $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            $actionUrl = "https://keluargamahaya.com/kas/{$encryptedId}";

            if ($notif->wa_notification  && $notif->anggota) {
                // Mengirim pesan ke Warga
                $responseWarga = $this->fonnteService->sendWhatsAppMessage($phoneNumberWarga, $messageWarga, $imageUrl);
            }
            if ($notif->email_notification && $notif->anggota) {
                // Mengirim notifikasi email ke anggota
                Mail::to($recipientEmail)->send(new Notification($recipientName, $bodyMessage, $status, $actionUrl));
            }

            // ============================Notif untuk pengurus=========================================================

            // Mengambil data warga berdasarkan acceess Notif
            $notifPengurus = AccessNotification::where('notification_id', $notif->id)->where('is_active', true)->get();

            foreach ($notifPengurus as $notif_pengurus) {

                $phoneNumberPengurus = $notif_pengurus->Warga->no_hp ?? null;
                $encryptedIdpengurus = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
                $actionUrlPengurus = "https://keluargamahaya.com/confirm/kas/{$encryptedIdpengurus}";

                // Membuat pesan WhatsApp untuk Pengurus
                $messagePengurus = "*Notifikasi Pembayaran Kas Baru*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name}.\n\n";
                $messagePengurus .= "Telah diterima pembayaran kas yang memerlukan konfirmasi Anda.\n\n";
                $messagePengurus .= "Berikut adalah detail pembayaran:\n";
                $messagePengurus .= "- *Kode*: {$data->code}\n";
                $messagePengurus .= "- *Tanggal Pembayaran*: {$data->payment_date}\n";
                $messagePengurus .= "- *Nama Pembayar*: {$data->data_warga->name}\n";
                $messagePengurus .= "- *Di Input oleh*: {$data->submitted->name}\n";
                $messagePengurus .= "- *Nominal*: Rp" . number_format($data->amount, 0, ',', '.') . "\n";
                $messagePengurus .= "- *Keterangan*: {$data->description}\n\n";
                $messagePengurus .= "Silakan cek dan konfirmasi pembayaran ini melalui link berikut:\n";
                $messagePengurus .= "- *Link Konfirmasi*: {$actionUrlPengurus}\n\n";
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

            // Jik nitifikasi di aktifkan return yang ini di hapus
            // DB::commit();
            // return back()->with('success', 'Data tersimpan, Notifikasi tidak ada !');
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
        $id = Crypt::decrypt($id);
        $kas_payment = KasPayment::findOrFail($id);
        return view('user.program.kas.detail.show_kas', compact('kas_payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = Crypt::decrypt($id);
        $kas_payment = KasPayment::findOrFail($id);
        if ($kas_payment->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $kas_payment->status);
        }
        return view('user.program.kas.edit.kas', compact('kas_payment'));
    }
    public function editPengurus(string $id)
    {
        $id = Crypt::decrypt($id);
        $kas_payment = KasPayment::findOrFail($id);
        if ($kas_payment->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $kas_payment->status);
        }
        return view('user.program.kas.edit.pengurus.kas', compact('kas_payment'));
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
        $cek_kas = KasPayment::findOrFail($id);
        if ($cek_kas->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $cek_kas->status);
        }
        $data = KasPayment::findOrFail($id);
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
            $file->move(public_path('storage/kas/pemasukan'), $filename);  // Simpan gambar ke folder public/storage/kas/pemasukan
            $data->transfer_receipt_path = "storage/kas/pemasukan/$filename";  // Simpan path gambar ke database
        }

        $data->update();

        return redirect()->back()->with('success', 'Berhasil di rubah');
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
        $cek_kas = KasPayment::findOrFail($id);
        if ($cek_kas->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $cek_kas->status);
        }
        $data = KasPayment::findOrFail($id);
        $data->data_warga_id = $request->data_warga_id;
        $data->amount = $request->amount;
        $data->payment_method = $request->payment_method;
        $data->description = $request->description;
        // ====Sementara di Nonaktifkan di hosting smylink tidak di ijinkan
        // Cek apakah file profile_picture di-upload
        // if ($request->hasFile('transfer_receipt_path')) {
        //     $file = $request->file('transfer_receipt_path');
        //     $path = $file->store(
        //         'kas/pemasukan',
        //         'public'
        //     ); // Simpan gambar ke direktori public
        //     $data->transfer_receipt_path = $path;
        // }

        // Jika ada file gambar, simpan gambar
        if ($request->hasFile('transfer_receipt_path')) {
            $file = $request->file('transfer_receipt_path');
            $filename = 'Kas-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/kas/pemasukan'), $filename);  // Simpan gambar ke folder public/storage/kas/pemasukan
            $data->transfer_receipt_path = "storage/kas/pemasukan/$filename";  // Simpan path gambar ke database
        }

        $data->update();

        return redirect()->route('kas.show.confirm', Crypt::encrypt($id))->with('success', 'Berhasil di rubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = KasPayment::find($id);
        if ($data->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di hapus karena sudah dalam status ' . $data->status);
        } else {
            $data->delete();

            return redirect()->back()->with('success', 'Pembayaran sudah di hapus');
        }
    }
    public function destroyPengurus(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = KasPayment::find($id);
        if ($data->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di hapus karena sudah dalam status ' . $data->status);
        } else {
            $data->delete();

            return redirect()->route('kas.pengajuan')->with('success', 'Pembayaran sudah di hapus');
        }
    }

    public function pengajuan()
    {
        $kasPayment_proses = KasPayment::where('status', 'process')->get();
        $kasPayment_pending = KasPayment::where('status', 'pending')->get();
        $kasPayment_reject = KasPayment::where('status', 'reject')->get();

        return view('user.program.kas.pengajuan.kas', compact('kasPayment_proses', 'kasPayment_pending', 'kasPayment_reject'));
    }

    public function show_confirm($id)
    {
        //Untuk konfirmasi delete
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $id = Crypt::decrypt($id);
        $kas_payment = KasPayment::findOrFail($id);
        return view('user.program.kas.konfirmasi.kas', compact('kas_payment'));
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
        // 1. Ambil semua anggaran dengan label "persentase"
        $anggaranItems = AnggaranSetting::where('label_anggaran', 'persentase');
        // 2. Hitung total persentase
        $totalPercentage = $anggaranItems->sum('catatan_anggaran');

        // 3. Jika total persentase melebihi 100%, batalkan proses dan tampilkan pesan error
        if ($totalPercentage > 100) {
            return redirect()->back()->with('error', 'Total persentase alokasi ' . $totalPercentage . ' melebihi 100%. Harap perbaiki nilai persentase anggaran.');
        }
        if ($totalPercentage < 100) {
            return redirect()->back()->with('error',  'Total persentase alokasi ' . $totalPercentage . ' kurang dari 100%. Harap perbaiki nilai persentase anggaran.');
        }

        // jika pilihan status nya pending maka berhenti sampai sini
        if ($request->status == "pending") {
            // Mengambil waktu saat ini
            $dateTime = Carbon::now();

            $data = KasPayment::findOrFail($id);
            $data->confirmed_by = Auth::user()->data_warga_id;
            $data->status = $request->status;
            $data->confirmation_date = $dateTime;

            $data->update();
            return redirect()->back()->with('success', 'Pembayaran kas berhasil Pending');
        }

        DB::beginTransaction();

        try {

            // Ambil pengajuan dengan row-level locking untuk mencegah race condition
            $pengajuan = KasPayment::where('id', $id)->lockForUpdate()->first();

            // Validasi apakah pengajuan sudah disetujui
            if ($pengajuan->status === 'confirmed') {
                DB::rollBack();
                return back()->with('error', 'Pengajuan sudah di Konfirmasi ');
            }

            // Mengambil waktu saat ini
            $dateTime = Carbon::now();

            $data = KasPayment::findOrFail($id);
            $data->data_warga_id = $request->data_warga_id;
            $data->amount = $request->amount;
            // $data->payment_date = $request->payment_date;
            $data->payment_method = $request->payment_method;
            $data->description = $request->description;
            $data->submitted_by = $request->submitted_by;
            $data->confirmed_by = Auth::user()->data_warga_id;
            $data->status = $request->status;
            $data->confirmation_date = $dateTime;
            $data->is_deposited = $request->is_deposited;
            // Cek apakah file profile_picture di-upload
            // if ($request->hasFile('transfer_receipt_path')) {
            //     $file = $request->file('transfer_receipt_path');
            //     $path = $file->store(
            //         'kas/pemasukan',
            //         'public'
            //     ); // Simpan gambar ke direktori public
            //     $data->transfer_receipt_path = $path;
            // }

            $data->update();
            // -------------------------------------------------
            if ($request->status == 'confirmed') {
                $saldo_terbaru = Saldo::latest()->first();
                $saldos = new Saldo();
                $saldos->code = $data->code;
                $saldos->amount = $data->amount;
                if ($request->payment_method === "transfer") {
                    $atm = ($saldo_terbaru->atm_balance ?? 0) + $data->amount;
                    $out = ($saldo_terbaru->cash_outside ?? 0);
                } else if ($request->payment_method === "cash") {
                    $atm = ($saldo_terbaru->atm_balance ?? 0);
                    $out = ($saldo_terbaru->cash_outside ?? 0) + $data->amount;
                };
                $saldos->atm_balance = $atm;
                $saldos->total_balance = ($saldo_terbaru->total_balance ?? 0) + $data->amount;
                $saldos->ending_balance = ($saldo_terbaru->total_balance ?? 0);
                $saldos->cash_outside = $out;

                $saldos->save();

                // -------------------------------------------


                foreach ($anggaranItems->get() as $anggaran) {
                    $anggaran_saldo_terakhir =  AnggaranSaldo::where('type', $anggaran->anggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
                    // Hitung alokasi dana berdasarkan catatan_anggaran sebagai persentase
                    $allocatedAmount = $request->amount * ($anggaran->catatan_anggaran / 100);
                    $saldo_anggaran = new AnggaranSaldo();
                    $saldo_anggaran->saldo_id = $saldos->id; //mengambil id dari model saldo di atas
                    $saldo_anggaran->type = $anggaran->anggaran->name;
                    $saldo_anggaran->percentage = $anggaran->catatan_anggaran;
                    $saldo_anggaran->amount = $allocatedAmount;
                    $saldo_anggaran->saldo = ($anggaran_saldo_terakhir->saldo ?? 0) + $allocatedAmount;

                    $saldo_anggaran->save();
                }

                $notif = DataNotification::where('name', 'Kas Payment')
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
                $messageWarga = "*Pembayaran Kas Terkonfirmasi*\n";
                $messageWarga .= "Selamat {$data_warga->name}, pembayaran kas Anda telah berhasil dikonfirmasi oleh " . Auth::user()->name . " \n\n";
                $messageWarga .= "Berikut adalah detail pembayaran Anda:\n";
                $messageWarga .= "- *Kode*: {$request->code}\n";
                $messageWarga .= "- *Tanggal Pembayaran*: {$data->payment_date}\n";
                $messageWarga .= "- *Nama*: {$data_warga->name}\n";
                $messageWarga .= "- *Di Input Oleh*: {$pengaju->name}\n";
                $messageWarga .= "- *Nominal*: Rp" . number_format($request->amount, 0, ',', '.') . "\n";
                $messageWarga .= "- *Keterangan*: {$request->description}\n\n";
                $messageWarga .= "Terima kasih telah memenuhi kewajiban pembayaran kas. Jika Anda memiliki pertanyaan lebih lanjut, silakan hubungi pengurus melalui kontak resmi.\n\n";
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
                $actionUrl = "https://keluargamahaya.com/show/{$encryptedId}";


                if ($notif->wa_notification  && $notif->anggota) {
                    // Mengirim pesan ke Warga
                    $responseWarga = $this->fonnteService->sendWhatsAppMessage($phoneNumberWarga, $messageWarga, $imageUrl);
                }
                if ($notif->email_notification && $notif->anggota) {
                    // Mengirim notifikasi email ke anggota
                    Mail::to($recipientEmail)->send(new Notification($recipientName, $bodyMessage, $status, $actionUrl));
                }

                // ============================Notif untuk pengurus=========================================================


                // Mengambil nomor telepon Ketua Untuk Laporan
                $notifPengurus = AccessNotification::where('notification_id', $notif->id)->where('is_active', true)->get();
                foreach ($notifPengurus as $notif_pengurus) {

                    $phoneNumberPengurus = $notif_pengurus->Warga->no_hp ?? null;

                    // Pesan untuk Ketua
                    $messagePengurus = "*Laporan Pembayaran Kas Terkonfirmasi*\n";
                    $messagePengurus .= "Halo {$notif_pengurus->Warga->name}\n\n";
                    $messagePengurus .= "Berikut adalah laporan pembayaran kas yang telah berhasil dikonfirmasi oleh  " . Auth::user()->name . " \n\n";
                    $messagePengurus .= "- *Kode*: {$request->code}\n";
                    $messagePengurus .= "- *Tanggal Pembayaran*: {$data->payment_date}\n";
                    $messagePengurus .= "- *Nama Warga*: {$data_warga->name}\n";
                    $messagePengurus .= "- *Di Input Oleh*: {$pengaju->name}\n";
                    $messagePengurus .= "- *Nominal*: Rp" . number_format($request->amount, 0, ',', '.') . "\n";
                    $messagePengurus .= "- *Keterangan*: {$request->description}\n\n";
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
                    $actionUrlPengurus = "https://keluargamahaya.com/show/{$encryptedIdPengurus}";

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
            } else {
                return redirect()->back()->with('info', 'Pembayaran kas belum masuk data');
            }
            // DB::commit();

            // return redirect()->back()->with('success', 'Pembayaran kas berhasil');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pemasukan.' . $e->getMessage());
        }
    }
}

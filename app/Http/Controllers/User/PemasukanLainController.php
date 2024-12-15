<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\Notification;
use App\Models\AccessNotification;
use App\Models\AccessProgram;
use App\Models\Anggaran;
use App\Models\AnggaranSaldo;
use App\Models\DataNotification;
use App\Models\DataWarga;
use App\Models\LayoutsForm;
use App\Models\OtherIncomes;
use App\Models\Saldo;
use App\Models\User;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PemasukanLainController extends Controller
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

        // Mengecek data pembayran kas yang masih proses
        $cek_income = OtherIncomes::where('status', 'process')->count();
        // mengambil data kas Anggota
        $data_income = OtherIncomes::where('status', 'confirmed')
            ->get();
        $pengajuan = OtherIncomes::where('status', 'process')
            ->first();
        $layout_form = LayoutsForm::first();

        $anggaran = Anggaran::all();

        return view('user.pemasukanLain.index', compact('anggaran', 'cek_income', 'data_income', 'pengajuan', 'layout_form'));
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
                'anggaran_id' => 'required',
            ],
            [
                'amount.required' => 'Nominal Harus di isi',
                'payment_method.required' => 'Pembayaran Harus di isi',
                'description.required' => 'Keterangan Harus di isi',
                'anggaran_id.required' => 'Anggaean Harus di isi',
            ]
        );

        // Mengecek apakah sudah ada pengajuan kas yang sedang diproses
        $cek_income = OtherIncomes::where('status', 'process')->where('anggaran_id', $request->anggaran_id)->count();
        if ($cek_income >= 1) {
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
            $incomeCount = OtherIncomes::count() + 1;

            // Membuat kode kas
            $code = 'In-' . $formattedDate . $formattedTime . str_pad($incomeCount, 1, '0', STR_PAD_LEFT);
            // Format akhir: ADM-DDMMYYHHMMSS1

            // menentukan nilai is_deposite sesuai metode pembayran
            $deposit = $request->payment_method === 'cash' ? false : true; // Tunai harus disetorkan, transfer otomatis dianggap deposited

            $data = new OtherIncomes();
            $data->code = $code;
            $data->anggaran_id = $request->anggaran_id;
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
                $filename = 'in-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/kas/pemasukanLain'), $filename);  // Simpan gambar ke folder public/storage/kas/pemasukan
                $data->transfer_receipt_path = "storage/kas/pemasukanLain/$filename";  // Simpan path gambar ke database
            }

            $data->save();

            $notif = DataNotification::where('name', 'Pemasukan')
                ->where('type', 'Pengajuan')
                ->first();
            // ============================Notif untuk pengurus=========================================================

            // Mengambil nomor telepon Ketua Untuk Laporan
            $notifPengurus = AccessNotification::where('notification_id', $notif->id)->where('is_active', true)->get();
            foreach ($notifPengurus as $notif_pengurus) {

                $phoneNumberPengurus = $notif_pengurus->Warga->no_hp ?? null;

                $anggaran = Anggaran::find($request->anggaran_id);
                $penginput = DataWarga::find(Auth::user()->data_warga_id);

                // Data untuk pesan
                $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
                $link = "https://keluargamahaya.com/confirm/other-incomes/{$encryptedId}";

                // Membuat pesan WhatsApp
                $messagePengurus = "*Persetujuan Pemasukan Lainnya Diperlukan*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name},\n\n";
                $messagePengurus .= "Terdapat pengajuan pemasukan lainnya yang memerlukan persetujuan Anda sebelum masuk ke data. Berikut detail pengajuannya:\n\n";
                $messagePengurus .= "- *Kode* : {$code}\n";
                $messagePengurus .= "- *Nama Anggaran* : {$anggaran->name}\n";
                $messagePengurus .= "- *Tanggal Pengajuan* : {$dateTime}\n";
                $messagePengurus .= "- *Di Input* : {$penginput->name}\n";
                $messagePengurus .= "- *Nominal* : Rp" . number_format($request->amount, 0, ',', '.') . "\n\n";
                $messagePengurus .= "Silakan klik link berikut untuk memberikan persetujuan:\n";
                $messagePengurus .= $link . "\n\n";
                $messagePengurus .= "*Salam hormat,*\n";
                $messagePengurus .= "*Sistem Kas Keluarga*";


                // URL gambar dari direktori storage
                $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

                $recipientEmailPengurus = $notif_pengurus->Warga->email;
                $recipientNamePengurus = $notif_pengurus->Warga->name;
                $status = "Menunggu persetujuan Ketua";
                // Data untuk email pengurus
                $bodyMessagePengurus = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messagePengurus);
                $actionUrlPengurus = $link;

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
            $pengurusSuccess = isset($responsePengurus['status']) && $responsePengurus['status'] == 'success';

            // Berikan feedback berdasarkan hasil pengiriman
            if ($pengurusSuccess) {
                return back()->with('success', 'Data tersimpan, Notifikasi berhasil dikirim ke Pengurus!');
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
        $id = Crypt::decrypt($id);
        $income = OtherIncomes::findOrFail($id);
        return view('user.pemasukanLain.show', compact('income'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = Crypt::decrypt($id);
        $income = OtherIncomes::findOrFail($id);
        if ($income->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $income->status);
        }
        $anggaran = Anggaran::all();

        return view('user.pemasukanLain.form.edit', compact('income', 'anggaran'));
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
                'anggaran_id' => 'required',
            ],
            [
                'amount.required' => 'Nominal Harus di isi',
                'payment_method.required' => 'Pembayaran Harus di isi',
                'description.required' => 'Keterangan Harus di isi',
                'anggaran_id.required' => 'Anggaran Harus di isi',
            ]
        );
        $cek_income = OtherIncomes::findOrFail($id);
        if ($cek_income->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di update karena sudah dalam status ' . $cek_income->status);
        }
        $data = OtherIncomes::findOrFail($id);
        $data->anggaran_id = $request->anggaran_id;
        $data->amount = $request->amount;
        $data->payment_method = $request->payment_method;
        $data->description = $request->description;

        if ($request->hasFile('transfer_receipt_path')) {
            $file = $request->file('transfer_receipt_path');
            $filename = 'in-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/kas/pemasukanLain'), $filename);  // Simpan gambar ke folder public/storage/kas/pemasukan
            $data->transfer_receipt_path = "storage/kas/pemasukanLain/$filename";  // Simpan path gambar ke database
        }

        $data->update();

        return redirect()->back()->with('success', 'Berhasil di rubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = OtherIncomes::find($id);
        if ($data->status != "process") {
            return redirect()->back()->with('error', 'Pengajuan tidak bisa di hapus karena sudah dalam status ' . $data->status);
        } else {
            $data->delete();

            return redirect()->back()->with('success', 'Pembayaran sudah di hapus');
        }
    }

    public function pengajuan()
    {
        $income_proses = OtherIncomes::where('status', 'process')->get();
        $income_pending = OtherIncomes::where('status', 'pending')->get();
        $income_reject = OtherIncomes::where('status', 'reject')->get();

        return view('user.pemasukanLain.pengajuan', compact('income_proses', 'income_pending', 'income_reject'));
    }

    public function show_confirm($id)
    {
        //Untuk konfirmasi delete
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $id = Crypt::decrypt($id);
        $income = OtherIncomes::findOrFail($id);
        return view('user.pemasukanLain.konfirmasi', compact('income'));
    }

    public function confirm(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'anggaran_id' => 'required',
            'amount' => 'required',
            'payment_method' => 'required',
            'description' => 'required',
            'submitted_by' => 'required',
            'status' => 'required',
            'is_deposited' => 'required',
        ]);

        // jika pilihan status nya pending maka berhenti sampai sini
        if ($request->status == "pending") {
            // Mengambil waktu saat ini
            $dateTime = Carbon::now();

            $data = OtherIncomes::findOrFail($id);
            $data->confirmed_by = Auth::user()->data_warga_id;
            $data->status = $request->status;
            $data->confirmation_date = $dateTime;

            $data->update();
            return redirect()->back()->with('success', 'Pembayaran kas berhasil Pending');
        }

        DB::beginTransaction();

        try {

            // Mengambil waktu saat ini
            $dateTime = Carbon::now();

            $data = OtherIncomes::findOrFail($id);
            $data->anggaran_id = $request->anggaran_id;
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
                $anggaran = Anggaran::find($request->anggaran_id);
                // Cek apakah Saldo cukup berdasarkan anggaran
                if ($anggaran->name === "Dana Usaha" || $anggaran->name === "Dana Acara" || $anggaran->name === "Dana Kas") {
                    $saldo_akhir_request =  "Dana Kas";
                } else {
                    $saldo_akhir_request = $anggaran->name;
                }

                $anggaran_saldo_terakhir =  AnggaranSaldo::where('type', $saldo_akhir_request)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
                // Hitung alokasi dana berdasarkan catatan_anggaran sebagai persentase
                $persen = ($request->amount / $anggaran_saldo_terakhir->saldo) * 100;
                $saldo_anggaran = new AnggaranSaldo();
                $saldo_anggaran->saldo_id = $saldos->id; //mengambil id dari model saldo di atas
                $saldo_anggaran->type = $saldo_akhir_request;
                $saldo_anggaran->percentage = $persen;
                $saldo_anggaran->amount = $request->amount;
                $saldo_anggaran->saldo = ($anggaran_saldo_terakhir->saldo ?? 0) + $request->amount;

                $saldo_anggaran->save();

                // ------------------------------------------------------------
                $notif = DataNotification::where('name', 'Pemasukan')
                    ->where('type', 'Konfirmasi')
                    ->first();

                // ============================Notif untuk pengurus=========================================================


                // Mengambil data warga yang mengikuti program "Kas Keluarga"
                $access_program_kas = AccessProgram::whereHas('program', function ($query) {
                    $query->where('name', 'Kas Keluarga');
                })->get();

                // URL gambar dari direktori storage
                $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

                // Data untuk link
                $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
                $link = "https://keluargamahaya.com/other-income/{$encryptedId}";

                // Mengirim pesan ke setiap nomor
                foreach ($access_program_kas as $access) {
                    $phoneNumberPengurus = $access->dataWarga->no_hp; // Nomor telepon
                    $name = $access->dataWarga->name;   // Nama warga
                    $email = $access->dataWarga->email;   // Nama warga

                    // Membuat pesan khusus untuk masing-masing warga
                    $messagePengurus = "*Pemasukan Lain Lain*\n";
                    $messagePengurus .= "Halo {$name},\n\n";
                    $messagePengurus .= "Kami informasikan ada Pemasukan Lain selain dari Iuran KAS:\n\n";
                    $messagePengurus .= "- *Kode* : {$data->code}\n";
                    $messagePengurus .= "- *Tanggal* : {$data->created_at}\n";
                    $messagePengurus .= "- *Di Input Oleh* : {$data->submitted->name}\n";
                    $messagePengurus .= "- *Nominal* : Rp " . number_format($data->amount, 0, ',', '.') . "\n\n";
                    $messagePengurus .= "- *Di Konfirmasi* : {$data->confirmed->name}\n";
                    $messagePengurus .= "- *Pada Tanggal* : {$data->confirmation_date}\n\n";
                    $messagePengurus .= "Pemasukan ini masuk ke anggaran  {$data->anggaran->name}.\n";
                    $messagePengurus .= "Terima kasih atas kerjasama dan dukungan Anda dalam proses ini.\n\n";
                    $messagePengurus .= "Silakan klik link berikut untuk info selanjutnya:\n";
                    $messagePengurus .= $link . "\n\n";
                    $messagePengurus .= "*Salam hormat,*\n";
                    $messagePengurus .= "*Sistem Kas Keluarga*";

                    // Untuk mengirim email
                    $recipientEmailPengurus = $email;
                    $recipientNamePengurus = $name;
                    $status = "Selesai";
                    // Data untuk email pengurus
                    $bodyMessage = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messagePengurus);
                    $actionUrl = $link;

                    if ($notif->email_notification && $notif->program) {
                        // Mengirim email notif_program
                        Mail::to($recipientEmailPengurus)->send(new Notification($recipientNamePengurus, $bodyMessagePengurus, $status, $actionUrlPengurus));
                    }
                    if ($notif->wa_notification && $notif->program) {
                        // Mengirim pesan ke Pengurus
                        $responsePengurus = $this->fonnteService->sendWhatsAppMessage($phoneNumberPengurus, $messagePengurus, $imageUrl);
                    }
                }

                DB::commit();

                $pengurusSuccess = isset($responsePengurus['status']) && $responsePengurus['status'] == 'success';
                if ($pengurusSuccess) {
                    return back()->with('success', 'Data Berhasil di simpan, Notifikasi berhasil dikirim ke Bendahara!');
                } else {
                    return back()->with('warning', 'Data Berhasil di simpan, tetapi Notifikasi tidak terkirim ke Bendahara !');
                }
                // jika notifikasi email dan wa aktif maka yang di bawah di komen
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

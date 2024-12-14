<?php

namespace App\Http\Controllers\User\Kas;

use App\Http\Controllers\Controller;
use App\Mail\Notification;
use App\Models\AccessProgram;
use App\Models\Anggaran;
use App\Models\AnggaranSaldo;
use App\Models\AnggaranSetting;
use App\Models\CashExpenditures;
use App\Models\DataWarga;
use App\Models\Saldo;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PengeluaranController extends Controller
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

        $pengeluaran = CashExpenditures::all();
        // mengambil data kas Anggota
        $data_pengeluaran = CashExpenditures::where('status', 'Acknowledged')
            ->get();
        $pengeluaran_proses = CashExpenditures::whereNot('status', 'Acknowledged')
            ->first();

        return view('user.program.kas.pengeluaran', compact('pengeluaran', 'pengeluaran_proses', 'data_pengeluaran'));
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
            'anggaran_id' => 'required',
            'amount' => 'required',
            'description' => 'required',
        ]);

        $dataAnggaran = Anggaran::Find($request->anggaran_id);
        // Cek apakah Saldo cukup berdasarkan anggaran
        if ($dataAnggaran->name === "Dana Usaha" || $dataAnggaran->name === "Dana Acara" || $dataAnggaran->name === "Dana Kas") {
            $saldo_akhir_request =  AnggaranSaldo::where('type', 'Dana Kas')->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
        } else {
            $saldo_akhir_request =  AnggaranSaldo::where('type', $dataAnggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
        }
        if ($saldo_akhir_request->saldo <  $request->amount) {
            return redirect()->back()->with('error', 'Saldo untuk ' . $dataAnggaran->name . ' Kurang dari pengajuan.');
        }
        // cek apakah ada pengajuan yang stsus nya masih proses
        $cek_pengajuan = CashExpenditures::where('anggaran_id', $request->anggaran_id)->where('status', '!=', 'Acknowledged')->count();
        if ($cek_pengajuan > 0) {
            return redirect()->back()->with('error', 'Pengajuan pengeluaran sudah ada data yang masuk');
        }


        DB::beginTransaction();

        try {
            // Mengambil waktu saat ini
            $dateTime = now();

            // Format tanggal dan waktu
            $formattedDate = $dateTime->format('dmy'); // Dapatkan format DDMMYY
            $formattedTime = $dateTime->format('His'); // Dapatkan format HHMMSS

            // Menghitung jumlah admin saat ini dan menambahkan 1 untuk urutan
            $kasCount = CashExpenditures::count() + 1;

            // Membuat kode kas
            $code = $dataAnggaran->code_anggaran . '-' . $formattedDate . $formattedTime . str_pad($kasCount, 1, '0', STR_PAD_LEFT);
            // Format akhir: ADM-DDMMYYHHMMSS1
            $data = new CashExpenditures();
            $data->code = $code;
            $data->anggaran_id = $request->anggaran_id;
            $data->amount = $request->amount;
            $data->description = $request->description;
            $data->status = 'approved_by_chairman';
            $data->submitted_by = Auth::user()->data_warga_id;

            $data->save();

            // ------------------------------------------

            // Mengambil data pengaju (pengguna yang menginput)
            $pengaju = DataWarga::find(Auth::user()->data_warga_id);
            // Mengambil nomor telepon Ketua Untuk Laporan
            $ketua = User::whereHas('role', function ($query) {
                $query->where('name', 'Ketua');
            })->with('dataWarga')->first();

            $phoneNumberPengurus = $ketua->dataWarga->no_hp ?? null;
            // mengambil data anggaran berdasarkan anggaran_id
            $anggaran = Anggaran::findOrFail($request->anggaran_id);

            // Data untuk pesan
            $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            $link = "https://keluargamahaya.com/confirm/pengeluaran/{$encryptedId}";

            // Membuat pesan WhatsApp
            $messageKetua = "*Persetujuan Anggaran Diperlukan*\n";
            $messageKetua .= "Halo {$ketua->dataWarga->name},\n\n";
            $messageKetua .= "Terdapat pengajuan anggaran yang memerlukan persetujuan Anda sebelum dapat dicairkan oleh Bendahara. Berikut detail pengajuannya:\n\n";
            $messageKetua .= "- *Kode Anggaran*: {$data->code}\n";
            $messageKetua .= "- *Tanggal Pengajuan*: {$data->created_at->format('d M Y')}\n";
            $messageKetua .= "- *Nama Anggaran*: {$anggaran->name}\n";
            $messageKetua .= "- *Di Input*: {$pengaju->name}\n";
            $messageKetua .= "- *Nominal*: Rp" . number_format($data->amount, 0, ',', '.') . "\n\n";
            $messageKetua .= "Silakan klik link berikut untuk memberikan persetujuan:\n";
            $messageKetua .= $link . "\n\n";
            $messageKetua .= "*Salam hormat,*\n";
            $messageKetua .= "*Sistem Kas Keluarga*";


            // URL gambar dari direktori storage
            $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

            $recipientEmailPengurus = $ketua->dataWarga->email;
            $recipientNamePengurus = $ketua->dataWarga->name;
            $status = "Menunggu persetujuan Ketua";
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
                return back()->with('success', 'Data tersimpan, Notifikasi berhasil dikirim ke Warga dan Pengurus!');
            }

            return back()->with('error', 'Data tersimpan, Gagal mengirim notifikasi');

            // // Jik nitifikasi di aktifkan return yang ini di hapus

            // DB::commit();

            // return redirect()->back()->with('success', 'Data Pengeluaran berhasil di keluarkan, Notifikasi tidak aktif');
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
        $id = Crypt::decrypt($id);
        $pengeluaran = CashExpenditures::findOrFail($id);
        return view('user.program.kas.detail.show_pengeluaran', compact('pengeluaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = CashExpenditures::findOrFail($id);
        if (in_array($data->status, ['Acknowledged', 'disbursed_by_treasurer'])) {
            return redirect()->back()->with('error', 'Pengajuan Pinjaman tidak dapat di hapus sudah dalam status' . $data->status);
        }
        $anggaran = Anggaran::all();
        // Ambil status CashExpenditures untuk setiap anggaran
        foreach ($anggaran as $item) {
            $cashExpenditure = CashExpenditures::where('anggaran_id', $item->id)
                ->where('status', '!=', 'Acknowledged')
                ->first();

            $anggaranStatus[$item->id] = $cashExpenditure
                ? $cashExpenditure->status // Status jika ada selain 'Acknowledged'
                : null; // Tidak ada CashExpenditures atau semua 'Acknowledged'
        }
        $pengeluaran = CashExpenditures::findOrFail($id);
        return view('user.program.kas.edit.pengurus.pengeluaran', compact('pengeluaran', 'anggaran', 'anggaranStatus'));
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
                'anggaran_id' => 'required',
                'description' => 'required',
            ],
            [
                'amount.required' => 'Nominal Harus di isi',
                'anggaran_id.required' => 'Pilih anggaran Harus di isi',
                'description.required' => 'Keterangan Harus di isi',
            ]
        );

        $cek = CashExpenditures::findOrFail($id);

        if (in_array($cek->status, ['Acknowledged', 'disbursed_by_treasurer'])) {
            return redirect()->back()->with('error', 'Pengajuan Pinjaman tidak dapat di edit sudah dalam status ' . $cek->status);
        }
        $dataAnggaran = Anggaran::Find($request->anggaran_id);
        // Cek apakah Saldo cukup berdasarkan anggaran
        if ($dataAnggaran->name === "Dana Usaha" || $dataAnggaran->name === "Dana Acara" || $dataAnggaran->name === "Dana Kas") {
            $saldo_akhir_request =  AnggaranSaldo::where('type', 'Dana Kas')->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
        } else {
            $saldo_akhir_request =  AnggaranSaldo::where('type', $dataAnggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
        }

        if ($saldo_akhir_request->saldo <  $request->amount) {
            return redirect()->back()->with('error', 'Saldo untuk ' . $dataAnggaran->name . ' Kurang dari pengajuan.');
        }

        $data = CashExpenditures::findOrFail($id);
        $data->anggaran_id = $request->anggaran_id;
        $data->amount = $request->amount;
        $data->description = $request->description;

        $data->update();

        return redirect()->route('pengeluaran.show.confirm', Crypt::encrypt($id))->with('success', 'Berhasil di rubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = CashExpenditures::find($id);
        if (in_array($data->status, ['Acknowledged', 'disbursed_by_treasurer'])) {
            return redirect()->back()->with('error', 'Pengajuan Pinjaman tidak dapat di hapus sudah dalam status ' . $data->status);
        }
        $data->delete();
        return redirect()->route('pengeluaran.pengajuan')->with('success', 'Pembayaran sudah di hapus');
    }

    public function pengajuan()
    {
        $pengeluaran_proses = CashExpenditures::where('status', '!=', 'Acknowledged')->get();
        $pengeluaran_pending = CashExpenditures::where('status', 'pending')->get();

        return view('user.program.kas.pengajuan.pengeluaran', compact('pengeluaran_proses', 'pengeluaran_pending'));
    }
    public function show_confirm($id)
    {
        //Untuk konfirmasi delete
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $id = Crypt::decrypt($id);
        $pengeluaran = CashExpenditures::findOrFail($id);
        return view('user.program.kas.konfirmasi.pengeluaran', compact('pengeluaran'));
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
        DB::beginTransaction();

        try {
            $dateTime = now();

            $data = CashExpenditures::findOrFail($id);
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
            $link = "https://keluargamahaya.com/confirm/pengeluaran/{$encryptedId}";

            // Membuat pesan WhatsApp
            $messagePengurus = "*Pengajuan Anggaran Disetujui*\n";
            $messagePengurus .= "Halo {$bendahara->dataWarga->name},\n\n";
            $messagePengurus .= "Pengajuan anggaran berikut telah disetujui oleh {$data->ketua->name} dan sekarang dapat dilanjutkan ke tahap pencairan:\n\n";
            $messagePengurus .= "- *Kode Anggaran*: {$data->code}\n";
            $messagePengurus .= "- *Tanggal Pengajuan*: {$data->created_at->format('d M Y')}\n";
            $messagePengurus .= "- *Nama Anggaran*: {$data->anggaran->name}\n";
            $messagePengurus .= "- *Di Input*: {$data->sekretaris->name}\n";
            $messagePengurus .= "- *Nominal*: Rp" . number_format($data->amount, 0, ',', '.') . "\n\n";
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

            'status' => 'required',
            'amount' => 'required',
            'anggaran_id' => 'required',
            'receipt_path' => 'required',
        ]);

        $dataAnggaran = Anggaran::Find($request->anggaran_id);
        // Cek apakah Saldo cukup berdasarkan anggaran
        if ($dataAnggaran->name === "Dana Usaha" || $dataAnggaran->name === "Dana Acara" || $dataAnggaran->name === "Dana Kas") {
            $saldo_akhir_request =  AnggaranSaldo::where('type', 'Dana Kas')->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
        } else {
            $saldo_akhir_request =  AnggaranSaldo::where('type', $dataAnggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
        }

        if (!$saldo_akhir_request || !$saldo_akhir_request->saldo) {
            // Jika tidak ada data saldo atau saldo kosong, redirect dengan pesan error
            return redirect()->back()->with('error', 'Saldo tidak tersedia atau tidak ada nilai untuk anggaran ini.');
        }

        if ($saldo_akhir_request->saldo <  $request->amount) {
            return redirect()->back()->with('error', 'Saldo untuk ' . $dataAnggaran->name . ' Kurang dari pengajuan.');
        }

        DB::beginTransaction();

        try {
            // Mengambil waktu saat ini
            $dateTime = now();

            $data = CashExpenditures::findOrFail($id);
            $keterangan = $data->description . "<br> <p>Keterangan Bendahara :</p>" . $request->description;
            $data->description = $keterangan;
            $data->status = $request->status;
            $data->disbursed_by = Auth::user()->data_warga_id;
            $data->disbursed_date = $dateTime;
            // Cek apakah file profile_picture di-upload
            // if ($request->hasFile('receipt_path')) {
            //     $file = $request->file('receipt_path');
            //     $path = $file->store(
            //         'kas/pengeluaran',
            //         'public'
            //     ); // Simpan gambar ke direktori public
            //     $data->receipt_path = $path;
            // }

            if ($request->hasFile('receipt_path')) {
                $file = $request->file('receipt_path');
                $filename = 'Kas-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/kas/pengeluaran'), $filename);  // Simpan gambar ke folder public/storage/kas/pengeluaran
                $data->receipt_path = "storage/kas/pengeluaran/$filename";  // Simpan path gambar ke database
            }

            $data->update();
            // -------------------------------------

            $saldo_terbaru = Saldo::latest()->first();
            $saldo = new Saldo();
            $saldo->code = $data->code;
            $saldo->amount = '-' . $data->amount;
            $saldo->atm_balance = $saldo_terbaru->atm_balance - $data->amount;
            $saldo->total_balance = $saldo_terbaru->total_balance - $data->amount;
            $saldo->ending_balance = $saldo_terbaru->total_balance;
            $saldo->cash_outside = $saldo_terbaru->cash_outside;

            $saldo->save();
            // -------------------------------------------

            // Hitung alokasi dana berdasarkan catatan_anggaran sebagai persentase
            $percenAmount = ($request->amount / $saldo_akhir_request->saldo) * 100;
            $saldo_anggaran = new AnggaranSaldo();

            $saldo_anggaran->type = $dataAnggaran->name;
            $saldo_anggaran->percentage = $percenAmount;
            $saldo_anggaran->amount = '-' . $request->amount;
            $saldo_anggaran->saldo = $saldo_akhir_request->saldo - $request->amount;
            $saldo_anggaran->saldo_id = $saldo->id; //mengambil id dari model saldo di atas

            $saldo_anggaran->save();


            // Mengambil data warga yang mengikuti program "Kas Keluarga"
            $access_program_kas = AccessProgram::whereHas('program', function ($query) {
                $query->where('name', 'Kas Keluarga');
            })->get();

            // URL gambar dari direktori storage
            $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

            // Data untuk link
            $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            $link = "https://keluargamahaya.com/pengeluaran/{$encryptedId}";

            // Mengirim pesan ke setiap nomor
            foreach ($access_program_kas as $access) {
                $number = $access->dataWarga->no_hp; // Nomor telepon
                $name = $access->dataWarga->name;   // Nama warga
                $email = $access->dataWarga->email;   // Nama warga

                // Membuat pesan khusus untuk masing-masing warga
                $message = "*Anggaran Telah Dikeluarkan*\n";
                $message .= "Halo {$name},\n\n";
                $message .= "Kami informasikan bahwa anggaran berikut telah berhasil dikeluarkan dan proses pencairan telah selesai:\n\n";
                $message .= "- *Kode Anggaran*: {$data->code}\n";
                $message .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
                $message .= "- *Nama Anggaran*: {$data->anggaran->name}\n";
                $message .= "- *Di Input Oleh*: {$data->sekretaris->name}\n";
                $message .= "- *Nominal*: Rp" . number_format($data->amount, 0, ',', '.') . "\n\n";
                $message .= "- *Di Konfirmasi*: {$data->ketua->name}\n";
                $message .= "- *Pada Tanggal*: {$data->approved_date}\n\n";
                $message .= "- *Dikeluarkan Oleh*: {$data->bendahara->name}\n";
                $message .= "- *Pada Tanggal*: {$data->disbursed_date}\n\n";
                $message .= "Terima kasih atas kerjasama dan dukungan Anda dalam proses ini.\n\n";
                $message .= "Silakan klik link berikut untuk info selanjutnya:\n";
                $message .= $link . "\n\n";
                $message .= "*Salam hormat,*\n";
                $message .= "*Sistem Kas Keluarga*";

                // Untuk mengirim email
                $recipientEmail = $email;
                $recipientName = $name;
                $status = "Selesai";
                // Data untuk email pengurus
                $bodyMessage = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $message);
                $actionUrl = $link;

                // Mengirim email bendahara
                Mail::to($recipientEmail)->send(new Notification($recipientName, $bodyMessage, $status, $actionUrl));

                // Mengirim pesan ke nomor warga
                $response = $this->fonnteService->sendWhatsAppMessage($number, $message, $imageUrl);
            }

            DB::commit();

            if (isset($response['status']) && $response['status'] == 'success') {
                return back()->with('success', 'Data berhasil di simpan, Notifikasi berhasil dikirim!');
            }
            return back()->with('error', 'Data tersimpan, Gagal mengirim notifikasi');

            // //jika notifikasi email dan wa aktif maka yang di bawah di komen
            // DB::commit();
            // return redirect()->back()->with('success', 'Data Pengeluaran berhasil di keluarkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data pengeluaran.');
        }
    }
}

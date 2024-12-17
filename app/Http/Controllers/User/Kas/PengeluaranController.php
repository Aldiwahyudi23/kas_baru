<?php

namespace App\Http\Controllers\User\Kas;

use App\Http\Controllers\Controller;
use App\Mail\Notification;
use App\Models\AccessNotification;
use App\Models\AccessProgram;
use App\Models\Anggaran;
use App\Models\AnggaranSaldo;
use App\Models\AnggaranSetting;
use App\Models\CashExpenditures;
use App\Models\DataNotification;
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
            $notif = DataNotification::where('name', 'Pengeluaran')
                ->where('type', 'Pengajuan')
                ->first();

            // ============================Notif untuk pengurus=========================================================

            // Mengambil nomor telepon Ketua Untuk Laporan
            $notifPengurus = AccessNotification::where('notification_id', $notif->id)->where('is_active', true)->get();
            foreach ($notifPengurus as $notif_pengurus) {

                // Mengambil data pengaju (pengguna yang menginput)
                $pengaju = DataWarga::find(Auth::user()->data_warga_id);
                $phoneNumberPengurus = $notif_pengurus->Warga->no_hp ?? null;
                // mengambil data anggaran berdasarkan anggaran_id
                $anggaran = Anggaran::findOrFail($request->anggaran_id);

                // Data untuk pesan
                $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
                $link = "https://keluargamahaya.com/confirm/pengeluaran/{$encryptedId}";

                // Membuat pesan WhatsApp
                $messagePengurus = "*Persetujuan Pengeluaran Anggaran Diperlukan*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name},\n\n";
                $messagePengurus .= "Terdapat pengajuan Pengeluaran anggaran yang memerlukan persetujuan Anda sebelum dapat dicairkan oleh Bendahara. Berikut detail pengajuannya:\n\n";
                $messagePengurus .= "- *Kode Anggaran*: {$data->code}\n";
                $messagePengurus .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
                $messagePengurus .= "- *Nama Anggaran*: {$anggaran->name}\n";
                $messagePengurus .= "- *Di Input*: {$pengaju->name}\n";
                $messagePengurus .= "- *Nominal*: Rp" . number_format($data->amount, 0, ',', '.') . "\n\n";
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
            $pengurusSuccess = isset($responsePengurus['status']) && $responsePengurus['status'] == 'success';
            if ($pengurusSuccess) {
                return back()->with('success', 'Data tersimpan, Notifikasi berhasil dikirim ke Warga dan Pengurus !');
            } else {
                return back()->with('warning', 'Data tersimpan, tetapi Notifikasi tidak terkirim ke Bendahara !');
            }
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

            // Ambil pengajuan dengan row-level locking untuk mencegah race condition
            $pengajuan = CashExpenditures::where('id', $id)->lockForUpdate()->first();

            // Validasi apakah pengajuan sudah disetujui
            if ($pengajuan->status === 'disbursed_by_treasurer') {
                DB::rollBack();
                return back()->with('error', 'Pengajuan sudah di Konfirmasi ');
            }

            $dateTime = now();

            $data = CashExpenditures::findOrFail($id);
            $data->status = $request->status;
            $data->approved_by = Auth::user()->data_warga_id;
            $data->approved_date = $dateTime;

            $data->update();

            // ------------------------------------------------------------
            $notif = DataNotification::where('name', 'Pengeluaran')
                ->where('type', 'Konfirmasi')
                ->first();

            // ============================Notif untuk pengurus=========================================================

            // Mengambil nomor telepon Ketua Untuk Laporan
            $notifPengurus = AccessNotification::where('notification_id', $notif->id)->where('is_active', true)->get();
            foreach ($notifPengurus as $notif_pengurus) {

                $phoneNumberPengurus = $notif_pengurus->Warga->no_hp ?? null;

                // Data untuk pesan
                $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
                $link = "https://keluargamahaya.com/confirm/pengeluaran/{$encryptedId}";

                // Membuat pesan WhatsApp
                $messagePengurus = "*Pengajuan Pengeluaran Anggaran Disetujui*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name},\n\n";
                $messagePengurus .= "Pengajuan Pengeluaran anggaran berikut telah disetujui oleh {$data->ketua->name} dan sekarang dapat dilanjutkan ke tahap pencairan:\n\n";
                $messagePengurus .= "- *Kode Anggaran*: {$data->code}\n";
                $messagePengurus .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
                $messagePengurus .= "- *Nama Anggaran*: {$data->anggaran->name}\n";
                $messagePengurus .= "- *Di Input*: {$data->sekretaris->name}\n";
                $messagePengurus .= "- *Nominal*: Rp" . number_format($data->amount, 0, ',', '.') . "\n\n";
                $messagePengurus .= "- *Di Konformasi*: {$data->ketua->name}\n";
                $messagePengurus .= "- *Pada Tanggal*: {$data->approved_date}\n\n";
                $messagePengurus .= "Silakan klik link berikut untuk melanjutkan proses pencairan:\n";
                $messagePengurus .= $link . "\n\n";
                $messagePengurus .= "*Salam hormat,*\n";
                $messagePengurus .= "*Sistem Kas Keluarga*";


                // URL gambar dari direktori storage
                $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

                $recipientEmailPengurus = $notif_pengurus->Warga->email;
                $recipientNamePengurus = $notif_pengurus->Warga->name;
                $status = "Sudah di setujui, menunggu pencairan";
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
            $pengurusSuccess = isset($responsePengurus['status']) && $responsePengurus['status'] == 'success';
            if ($pengurusSuccess) {
                return back()->with('success', 'Data Terkonfirmasi, Notifikasi berhasil dikirim ke Bendahara!');
            } else {
                return back()->with('warning', 'Data terkonfirmasi, tetapi Notifikasi tidak terkirim ke Bendahara !');
            }
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

            // Ambil pengajuan dengan row-level locking untuk mencegah race condition
            $pengajuan = CashExpenditures::where('id', $id)->lockForUpdate()->first();

            // Validasi apakah pengajuan sudah disetujui
            if ($pengajuan->status === 'Acknowledged') {
                DB::rollBack();
                return back()->with('error', 'Pengajuan sudah di Konfirmasi ');
            }
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

            // ------------------------------------------------------------
            $notif = DataNotification::where('name', 'Pengeluaran')
                ->where('type', 'Pencairan')
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
            $link = "https://keluargamahaya.com/pengeluaran/{$encryptedId}";

            // Mengirim pesan ke setiap nomor
            foreach ($access_program_kas as $access) {
                $phoneNumberPengurus = $access->dataWarga->no_hp; // Nomor telepon
                $name = $access->dataWarga->name;   // Nama warga
                $email = $access->dataWarga->email;   // Nama warga

                // Membuat pesan khusus untuk masing-masing warga
                $messagePengurus = "*Pengeluaran Anggaran Telah Dikeluarkan*\n";
                $messagePengurus .= "Halo {$name},\n\n";
                $messagePengurus .= "Kami informasikan bahwa Pengeluaran anggaran berikut telah berhasil dikeluarkan dan proses pencairan telah selesai:\n\n";
                $messagePengurus .= "- *Kode Anggaran*: {$data->code}\n";
                $messagePengurus .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
                $messagePengurus .= "- *Nama Anggaran*: {$data->anggaran->name}\n";
                $messagePengurus .= "- *Di Input Oleh*: {$data->sekretaris->name}\n";
                $messagePengurus .= "- *Nominal*: Rp" . number_format($data->amount, 0, ',', '.') . "\n\n";
                $messagePengurus .= "- *Di Konfirmasi*: {$data->ketua->name}\n";
                $messagePengurus .= "- *Pada Tanggal*: {$data->approved_date}\n\n";
                $messagePengurus .= "- *Dikeluarkan Oleh*: {$data->bendahara->name}\n";
                $messagePengurus .= "- *Pada Tanggal*: {$data->disbursed_date}\n\n";
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
                $bodyMessagePengurus = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messagePengurus);
                $actionUrlPengurus = $link;

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
            // Berikan feedback berdasarkan hasil pengiriman
            $pengurusSuccess = isset($responsePengurus['status']) && $responsePengurus['status'] == 'success';
            if ($pengurusSuccess) {
                return back()->with('success', 'Data Berhasil di simpan, Notifikasi berhasil dikirim ke Bendahara!');
            } else {
                return back()->with('warning', 'Data Berhasil di simpan, tetapi Notifikasi tidak terkirim ke Bendahara !');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data pengeluaran.');
        }
    }
}

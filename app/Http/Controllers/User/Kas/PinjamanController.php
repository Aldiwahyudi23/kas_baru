<?php

namespace App\Http\Controllers\User\Kas;

use App\Http\Controllers\Controller;
use App\Mail\Notification;
use App\Models\AccessNotification;
use App\Models\AccessProgram;
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
        // $access = AccessProgram::where('program_id', $program->id)->get();
        $access = DataWarga::all();

        $saldo_pinjam = AnggaranSaldo::where('type', 'Dana Pinjam')->latest()->first();
        $saldo_proses = Loan::whereIn('status', ['pending', 'approved_by_chairman', 'disbursed_by_treasurer',])->sum('loan_amount');
        $saldo_terpakai = Loan::whereIn('status', ['Acknowledged', 'In Repayment'])->sum('remaining_balance');

        return view('user.program.kas.pinjaman', compact('layout_form', 'pinjaman', 'anggaran', 'pinjaman_proses', 'access', 'pinjaman_tersambung', 'saldo_pinjam', 'saldo_proses', 'saldo_terpakai'));
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
        $saldo_proses = Loan::whereIn('status', ['pending', 'approved_by_chairman', 'disbursed_by_treasurer',])->sum('loan_amount');
        $sisa_saldo = $saldo_akhir_request->saldo - $saldo_proses;

        if (!$saldo_akhir_request || !$saldo_akhir_request->saldo) {
            // Jika tidak ada data saldo atau saldo kosong, redirect dengan pesan error
            return redirect()->back()->with('error', 'Saldo tidak tersedia atau tidak ada nilai untuk anggaran ini.');
        }
        if ($sisa_saldo <  $request->amount) {
            return redirect()->back()->with('error', 'Saldo untuk ' . $dataAnggaran->name . ' Kurang dari pengajuan.');
        }
        // -------------------------------------------

        // cek untuk nominal Max sesuai kesepakatan
        $nominal_max = AnggaranSetting::where('anggaran_id', $dataAnggaran->id)->where('label_anggaran', 'Alokasi Anggaran Max')->first();
        if ($request->amount > $nominal_max->catatan_anggaran) {
            return back()->with('error', 'Nominal yang di ajukan melebihi batas max yang telah di sepakati');
        }
        // -------------------------------------------
        if ($request->data_warga_id) {
            $cek_warga = $request->data_warga_id;
        } else {
            $cek_warga = Auth::user()->data_warga_id;
        }
        // Ambil data pinjaman terbaru dari data_warga_id tertentu
        $latestLoan = Loan::where('data_warga_id', $cek_warga)
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
                $BatasPinjaman2 = AnggaranSetting::where('label_anggaran', 'Batas Setelah Pinjaman 2 (Minggu)')
                    ->where('anggaran_id', $dataAnggaran->id)
                    ->first();
                $normal = AnggaranSetting::where('label_anggaran', 'Batas Normal (Hari)')
                    ->where('anggaran_id', $dataAnggaran->id)
                    ->first();

                $weeksToAdd = intval($kurangSebulan->catatan_anggaran);
                $weeksPinjaman2 = intval($BatasPinjaman2->catatan_anggaran);

                $tanpaLebih = intval($pembayaranTanpaLebih->catatan_anggaran);

                // Hitung tanggal pengajuan berikutnya berdasarkan aturan waktu tunggu
                $nextEligibleDate = $lastPaymentDate->copy()->addWeeks($weeksToAdd);

                // Cek jika selisih antara pembayaran terakhir dan pengajuan baru kurang dari sebulan
                $daysDifference = $loanCreationDate->diffInDays($lastPaymentDate);

                $cekpinjaman2 = LoanExtension::where(
                    'new_loan_id',
                    $latestLoan->id
                )
                    ->latest()->first();
                // Hitung tanggal pengajuan berikutnya berdasarkan aturan waktu tunggu
                $nextEligiblePinjaman2 = $lastPaymentDate->copy()->addWeeks($weeksPinjaman2);


                if ($daysDifference < $tanpaLebih && now()->lessThan($nextEligibleDate)) {
                    return back()->with('error', 'Pengajuan baru tidak dapat dilakukan. Coba lagi pada tanggal ' . $nextEligibleDate);
                } elseif ($cekpinjaman2) {
                    if (now()->lessThan($nextEligiblePinjaman2)) {
                        return back()->with('error', 'Pengajuan baru tidak dapat dilakukan. Coba lagi pada tanggal ' . $nextEligiblePinjaman2);
                    }
                } else {
                    if ($normal) {
                        $weeksNormal = intval($normal->catatan_anggaran);
                        $nextEligibleNormal = $lastPaymentDate->copy()->addDays($weeksNormal);
                        if (now()->lessThan($nextEligibleNormal)) {
                            return back()->with('error', 'Pengajuan baru tidak dapat dilakukan. Coba lagi pada tanggal ' . $nextEligibleNormal);
                        }
                    }
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
            $notif = DataNotification::where('name', 'Pinjaman')
                ->where('type', 'Pengajuan')
                ->first();

            // ==========================Notif Anggota=======================================

            // Mengambil data pengaju (pengguna yang menginput)

            $pengaju = DataWarga::find($data->submitted_by);
            // mengambil data anggaran berdasarkan anggaran_id
            $anggaran = Anggaran::findOrFail($dataAnggaran->id);
            // Data Warga
            $data_warga = DataWarga::find($warga);
            $phoneNumberWarga = $data_warga->no_hp;
            // URL gambar dari direktori storage
            $imageUrl = '';

            $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            $linkWarga = "https://keluargamahaya.com/pinjaman/{$encryptedId}";

            // Pesan untuk Warga
            $messageWarga = "*Pengajuan Pinjaman Anda Telah Diterima dan Menunggu Konfirmasi*\n";
            $messageWarga .= "Halo {$data_warga->name},\n\n";
            $messageWarga .= "Kami senang memberitahukan bahwa pengajuan pinjaman Anda telah kami terima dan saat ini sedang dalam proses konfirmasi oleh pengurus. Berikut detail pengajuan Anda:\n\n";
            $messageWarga .= "- *Kode Pinjaman*: {$code}\n";
            $messageWarga .= "- *Nama Anggaran*: {$anggaran->name}\n";
            $messageWarga .= "- *Tanggal Pengajuan*: {$dateTime}\n";
            $messageWarga .= "- *Nama Pengaju*: {$data_warga->name}\n";
            $messageWarga .= "- *Diinput Oleh*: {$pengaju->name}\n";
            $messageWarga .= "- *Nominal*: Rp" . number_format($request->amount, 0, ',', '.') . "\n\n";
            $messageWarga .= "Mohon untuk menunggu proses konfirmasi dari pengurus kami. Setelah dana dicairkan, Anda akan mendapatkan pemberitahuan lanjutan.\n\n";
            $messageWarga .= "Apabila terdapat pertanyaan, silakan hubungi kami melalui sistem atau pengurus langsung.\n\n";
            $messageWarga .= "*Terima kasih atas kepercayaan Anda, Pantau prosesnya klik link di bawah.*\n";
            $messageWarga .= $linkWarga . "\n\n";
            $messageWarga .= "*Salam hangat,*\n";
            $messageWarga .= "*Sistem Kas Keluarga*";


            // mengirim ke email 
            $recipientEmail = $data_warga->email;
            $recipientName = $data_warga->name;
            // Ganti tanda bintang dengan HTML <strong>
            $bodyMessage = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messageWarga);
            $status = $data->status;
            $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            $actionUrl = $linkWarga;

            if ($notif->wa_notification && $notif->anggota && !empty($phoneNumberWarga)) {
                // Mengirim pesan ke Warga jika nomor WhatsApp ada
                $responseWarga = $this->fonnteService->sendWhatsAppMessage($phoneNumberWarga, $messageWarga, $imageUrl);
            }

            if (
                $notif->email_notification && $notif->anggota && !empty($recipientEmail)
            ) {
                // Mengirim notifikasi email ke anggota jika email ada
                Mail::to($recipientEmail)->send(new Notification($recipientName, $bodyMessage, $status, $actionUrl));
            }
            // ============================Notif untuk pengurus=========================================================


            // Mengambil nomor telepon Ketua Untuk Laporan
            $notifPengurus = AccessNotification::where('notification_id', $notif->id)->where('is_active', true)->get();

            foreach ($notifPengurus as $notif_pengurus) {

                $phoneNumberPengurus = $notif_pengurus->Warga->no_hp ?? null;
                // Data untuk pesan
                $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
                $link = "https://keluargamahaya.com/confirm/pinjaman/{$encryptedId}";

                // Membuat pesan WhatsApp
                $messagePengurus = "*Persetujuan Pinjaman Diperlukan*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name},\n\n";
                $messagePengurus .= "Terdapat pengajuan Pinjamn yang memerlukan persetujuan Anda sebelum dapat dicairkan oleh Bendahara. Berikut detail pengajuannya:\n\n";
                $messagePengurus .= "- *Kode Pinjaman*: {$code}\n";
                $messagePengurus .= "- *Nama Anggaran*: {$anggaran->name}\n";
                $messagePengurus .= "- *Tanggal Pengajuan*: {$dateTime}\n";
                $messagePengurus .= "- *Nama*: {$data_warga->name}\n";
                $messagePengurus .= "- *Di Input*: {$pengaju->name}\n";
                $messagePengurus .= "- *Nominal*: Rp" . number_format($request->amount, 0, ',', '.') . "\n\n";
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
            $wargaSuccess = isset($responseWarga['status']) && $responseWarga['status'] == 'success';
            $pengurusSuccess = isset($responsePengurus['status']) && $responsePengurus['status'] == 'success';

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
                        return back()->with('error', 'Pengajuan baru tidak dapat dilakukan. Harap tunggu satu minggu sejak pembayaran selesai pada ' . $lastRepayment->payment_date);
                    }
                } else if ($lastPaymentDate->addWeeks(2)->isFuture()) {
                    return back()->with(
                        'error',
                        'Pengajuan baru tidak dapat dilakukan. Harap tunggu dua minggu sejak pembayaran terakhir pada ' . $lastRepayment->payment_date
                    );
                }
            } else {
                // Jika tidak ada riwayat pembayaran, maka ambil waktu pengajuan pinjaman
                $loanCreationDate = Carbon::parse($latestLoan->created_at);
                if ($latestLoan->status == 'paid in full') {
                    // Cek waktu satu minggu sejak tanggal pengajuan pinjaman terakhir
                    if ($loanCreationDate->addWeek()->isFuture()) {
                        return back()->with('error', 'Pengajuan baru tidak dapat dilakukan. Harap tunggu satu minggu sejak pengajuan terakhir pada ' . $loanCreationDate);
                    }
                }
            }
        }

        DB::beginTransaction();

        try {

            // Ambil pengajuan dengan row-level locking untuk mencegah race condition
            $pengajuan = Loan::where('id', $id)->lockForUpdate()->first();

            // Validasi apakah pengajuan sudah disetujui
            if ($pengajuan->status === 'approved_by_chairman') {
                DB::rollBack();
                return back()->with('error', 'Pengajuan sudah di Konfirmasi ');
            }

            $dateTime = now();

            $data = Loan::findOrFail($id);
            $data->status = $request->status;
            $data->approved_by = Auth::user()->data_warga_id;
            $data->approved_date = $dateTime;

            $data->update();

            // ------------------------------------------------------------
            $notif = DataNotification::where('name', 'Pinjaman')
                ->where('type', 'Konfirmasi')
                ->first();

            // ============================Notif untuk pengurus=========================================================

            // Mengambil nomor telepon Ketua Untuk Laporan
            $notifPengurus = AccessNotification::where('notification_id', $notif->id)->where('is_active', true)->get();
            foreach ($notifPengurus as $notif_pengurus) {

                $phoneNumberPengurus = $notif_pengurus->Warga->no_hp ?? null;
                // Data untuk pesan
                $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
                $link = "https://keluargamahaya.com/confirm/pinjaman/{$encryptedId}";

                // Membuat pesan WhatsApp
                $messagePengurus = "*Pengajuan Pinjaman Disetujui*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name},\n\n";
                $messagePengurus .= "Pengajuan pinjaman berikut telah disetujui oleh {$data->ketua->name} dan sekarang dapat dilanjutkan ke tahap pencairan:\n\n";
                $messagePengurus .= "- *Kode Pinjaman*: {$data->code}\n";
                $messagePengurus .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
                $messagePengurus .= "- *Nama Anggaran*: {$data->anggaran->name}\n";
                $messagePengurus .= "- *Di Input*: {$data->sekretaris->name}\n";
                $messagePengurus .= "- *Nominal*: Rp" . number_format($data->loan_amount, 0, ',', '.') . "\n\n";
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
                        return back()->with('error', 'Pengajuan baru tidak dapat dilakukan. Harap tunggu satu minggu sejak pembayaran selesai pada ' . $lastRepayment->payment_date);
                    }
                } else if ($lastPaymentDate->addWeeks(2)->isFuture()) {
                    return back()->with(
                        'error',
                        'Pengajuan baru tidak dapat dilakukan. Harap tunggu dua minggu sejak pembayaran terakhir pada ' . $lastRepayment->payment_date
                    );
                }
            } else {
                // Jika tidak ada riwayat pembayaran, maka ambil waktu pengajuan pinjaman
                $loanCreationDate = Carbon::parse($latestLoan->created_at);
                if ($latestLoan->status == 'paid in full') {
                    // Cek waktu satu minggu sejak tanggal pengajuan pinjaman terakhir
                    if ($loanCreationDate->addWeek()->isFuture()) {
                        return back()->with('error', 'Pengajuan baru tidak dapat dilakukan. Harap tunggu satu minggu sejak pengajuan terakhir pada ' . $loanCreationDate);
                    }
                }
            }
        }

        DB::beginTransaction();

        try {

            // Ambil pengajuan dengan row-level locking untuk mencegah race condition
            $pengajuan = Loan::where('id', $id)->lockForUpdate()->first();

            // Validasi apakah pengajuan sudah disetujui
            if ($pengajuan->status === 'disbursed_by_treasurer') {
                DB::rollBack();
                return back()->with('error', 'Pengajuan sudah di Cairkan ');
            }
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

            if ($request->hasFile('disbursement_receipt_path')) {
                $file = $request->file('disbursement_receipt_path');
                $filename = 'Kas-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/kas/pengeluaran/pinjaman'), $filename);  // Simpan gambar ke folder public/storage/kas/pengeluaran
                $data->disbursement_receipt_path = "storage/kas/pengeluaran/pinjaman/$filename";  // Simpan path gambar ke database
            }

            $data->update();

            // -------------------------------------
            $notif = DataNotification::where('name', 'Pinjaman')
                ->where('type', 'Pencairan')
                ->first();

            // ==========================Notif Anggota=======================================

            // Data Warga
            $data_warga = DataWarga::find($data->data_warga_id);
            $phoneNumberWarga = $data_warga->no_hp;
            // URL gambar dari direktori storage
            $imageUrl = '';
            $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            $link = "https://keluargamahaya.com/pinjaman/{$encryptedId}";

            // Pesan untuk Warga
            $messageWarga = "*Pinjaman Anda Disetujui dan Dicairkan*\n";
            $messageWarga .= "Halo {$data_warga->name},\n\n";
            $messageWarga .= "Kami informasikan bahwa pengajuan pinjaman Anda telah disetujui dan dana telah dicairkan oleh bendahara {$data->bendahara->name}. Berikut adalah detailnya:\n\n";
            $messageWarga .= "- *Kode Pinjaman*: {$data->code}\n";
            $messageWarga .= "- *Tanggal Pencairan*: {$data->disbursed_date}\n";
            $messageWarga .= "- *Nama Peminjam*: {$data_warga->name}\n";
            $messageWarga .= "- *Nominal*: Rp" . number_format($data->loan_amount, 0, ',', '.') . "\n";
            $messageWarga .= "- *Jatuh Tempo*: {$data->deadline_date}\n\n";
            $messageWarga .= "Mohon segera cek saldo di rekening Anda untuk memastikan dana telah masuk atau ambil sesuai kesepakatan \n\n";
            $messageWarga .= "Setelah menerima dana, mohon segera konfirmasi bahwa uang telah diterima dengan menghubungi kami melalui sistem atau langsung kepada pengurus.\n\n";
            $messageWarga .=  $link . "\n";
            $messageWarga .= "Terima kasih atas perhatian Anda.\n\n";
            $messageWarga .= "*Salam hormat,*\n";
            $messageWarga .= "*Sistem Kas Keluarga*";



            $messagePenjelasan = "*Penjelasan Terkait Pinjaman Keluarga*\n";
            $messagePenjelasan .= "Halo {$data_warga->name},\n\n";
            $messagePenjelasan .= "Kami ingin menegaskan bahwa *Pinjaman Keluarga* bukan layanan komersial atau lembaga pinjaman, melainkan inisiatif untuk membantu keluarga.\n\n";
            $messagePenjelasan .= "Waktu 3 bulan diberikan agar pembayaran dapat dilakukan bertahap dan tidak menumpuk di akhir. Jika pembayaran dilakukan hanya di akhir periode, ada risiko menjadi beban tambahan ketika situasi kurang mendukung.\n\n";
            $messagePenjelasan .= "Kami harap Anda dapat mencicil sesuai kemampuan agar tujuan utama pinjaman ini, yaitu membantu keluarga, tetap tercapai tanpa menimbulkan kesan negatif.\n\n";
            $messagePenjelasan .= "Selama Pinjaman Aktif maka setiap hari ke 60, 30, 14, 7, 3, 1, sebelum jatuh tempo akan muncul pemeritahuan secara Otomatis.\n\n";
            $messagePenjelasan .= "Terima kasih atas pengertian dan kerjasamanya.\n\n";
            $messagePenjelasan .= "*Salam hangat,*\n";
            $messagePenjelasan .= "*Sistem Kas Keluarga*";



            // mengirim ke email 
            $recipientEmail = $data_warga->email;
            $recipientName = $data_warga->name;
            // Ganti tanda bintang dengan HTML <strong>
            $bodyMessage = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messageWarga);
            $status = "Sudah di Cairkan";

            $actionUrl = $link;

            if ($notif->wa_notification && $notif->anggota && !empty($phoneNumberWarga)) {
                // Mengirim pesan ke Warga jika nomor WhatsApp ada
                $responseWarga = $this->fonnteService->sendWhatsAppMessage($phoneNumberWarga, $messageWarga, $imageUrl);

                $responseWargaPenjelasan = $this->fonnteService->sendWhatsAppMessage($phoneNumberWarga, $messagePenjelasan, $imageUrl);
            }

            if ($notif->email_notification && $notif->anggota && !empty($recipientEmail)) {
                // Mengirim notifikasi email ke anggota jika email ada
                Mail::to($recipientEmail)->send(new Notification($recipientName, $bodyMessage, $status, $actionUrl));
            }


            // ============================Notif untuk pengurus=========================================================

            // Mengambil nomor telepon Ketua Untuk Laporan
            $notifPengurus = AccessNotification::where('notification_id', $notif->id)->where('is_active', true)->get();
            foreach ($notifPengurus as $notif_pengurus) {

                $phoneNumberPengurus = $notif_pengurus->Warga->no_hp ?? null;

                // Pesan untuk Ketua
                $messagePengurus = "*Laporan Pencairan Pinjaman*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name},\n\n";
                $messagePengurus .= "Berikut adalah laporan pencairan pinjaman yang telah diproses oleh bendahara {$data->bendahara->name}:\n\n";
                $messagePengurus .= "- *Kode Pinjaman*: {$data->code}\n";
                $messagePengurus .= "- *Tanggal Pencairan*: {$data->disbursed_date}\n";
                $messagePengurus .= "- *Nama Peminjam*: {$data_warga->name}\n";
                $messagePengurus .= "- *Nominal*: Rp" . number_format($data->loan_amount, 0, ',', '.') . "\n";
                $messagePengurus .= "- *Jatuh Tempo*: {$data->deadline_date}\n\n";
                $messagePengurus .= "Pencairan ini telah berhasil diproses dan dana telah diberikan kepada pengaju.\n";
                $messagePengurus .= "Saat ini, pencairan menunggu konfirmasi dari pengaju bahwa dana telah diterima.\n\n";
                $messagePengurus .= "Silakan pantau proses selanjutnya jika diperlukan.\n\n";
                $messagePengurus .= "*Salam hormat,*\n";
                $messagePengurus .= "*Sistem Kas Keluarga*";



                // URL gambar dari direktori storage
                $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

                $recipientEmailPengurus = $notif_pengurus->Warga->email;
                $recipientNamePengurus = $notif_pengurus->Warga->name;
                // Data untuk email pengurus
                $bodyMessagePengurus = preg_replace('/\*(.*?)\*/', '<b>$1</b>', $messagePengurus);
                $actionUrlPengurus = "https://keluargamahaya.com/pinjaman/{$encryptedId}";

                if ($notif->email_notification && $notif->pengurus) {
                    // Mengirim email notif_pengurus
                    Mail::to($recipientEmailPengurus)->send(new Notification($recipientNamePengurus, $bodyMessagePengurus, $status, $actionUrlPengurus));
                }
                if ($notif->wa_notification && $notif->pengurus) {
                    // Mengirim pesan ke Pengurus
                    $responsePengurus = $this->fonnteService->sendWhatsAppMessage($phoneNumberPengurus, $messagePengurus, $imageUrl);
                }
            }
            // Cek hasil pengiriman
            // Evaluasi keberhasilan pengiriman
            $wargaSuccess = isset($responseWarga['status']) && $responseWarga['status'] == 'success';
            $wargaPenjelasanSuccess = isset($responseWargaPenjelasan['status']) && $responseWargaPenjelasan['status'] == 'success';
            $pengurusSuccess = isset($responsePengurus['status']) && $responsePengurus['status'] == 'success';
            DB::commit();

            // Berikan feedback berdasarkan hasil pengiriman
            if ($wargaSuccess && $pengurusSuccess) {
                return back()->with('success', 'Pencairan Berhasil, Notifikasi berhasil dikirim ke Warga dan Pengurus!');
            } elseif ($wargaSuccess) {
                return back()->with('success', 'Pencairan Berhasil, Notifikasi berhasil dikirim ke Warga, tetapi gagal ke Pengurus.');
            } elseif ($pengurusSuccess) {
                return back()->with('success', 'Pencairan Berhasil, Notifikasi berhasil dikirim ke Pengurus, tetapi gagal ke Warga.');
            } else {
                return back()->with('warning', 'Pencairan Berhasil, tetapi Notifikasi tidak terkirim ke Warga maupun Pengurus!');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.' . $e->getMessage());
        }
    }

    public function acknowledged(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);

        DB::beginTransaction();

        try {

            // Ambil pengajuan dengan row-level locking untuk mencegah race condition
            $pengajuan = Loan::where('id', $id)->lockForUpdate()->first();

            // Validasi apakah pengajuan sudah disetujui
            if ($pengajuan->status === 'Acknowledged') {
                DB::rollBack();
                return back()->with('error', 'Pengajuan sudah di Konfirmasi ');
            }
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


            $notif = DataNotification::where('name', 'Pinjaman')
                ->where('type', 'Diterima')
                ->first();
            // ============================Notif untuk pengurus=========================================================

            // Mengambil nomor telepon Ketua Untuk Laporan
            $notifPengurus = AccessNotification::where('notification_id', $notif->id)->where('is_active', true)->get();
            foreach ($notifPengurus as $notif_pengurus) {

                // Mengambil data pengaju (pengguna yang menginput)
                $terima = DataWarga::find(Auth::user()->data_warga_id);
                $phoneNumberPengurus = $notif_pengurus->Warga->no_hp ?? null;

                // Data untuk pesan
                $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
                $link = "https://keluargamahaya.com/pinjaman/{$encryptedId}";

                // Membuat pesan WhatsApp
                $messagePengurus = "*Dana Pinjaman Sudah Diterima*\n";
                $messagePengurus .= "Halo {$notif_pengurus->Warga->name},\n\n";
                $messagePengurus .= "Kami informasikan bahwa {$terima->name} telah mengonfirmasi bahwa dana pinjaman telah diterima. Berikut adalah detail pengajuan:\n\n";
                $messagePengurus .= "- *Kode Pinjaman*: {$data->code}\n";
                $messagePengurus .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
                $messagePengurus .= "- *Nama Warga*: {$data->warga->name}\n";
                $messagePengurus .= "- *Di Input*: {$data->sekretaris->name}\n";
                $messagePengurus .= "- *Nominal*: Rp" . number_format($data->loan_amount, 0, ',', '.') . "\n";
                $messagePengurus .= "Terima kasih atas perhatian dan kerjasama Anda.\n\n";
                $messagePengurus .= "*Salam hormat,*\n";
                $messagePengurus .= "*Sistem Kas Keluarga*";

                // URL gambar dari direktori storage
                $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

                $recipientEmailPengurus = $notif_pengurus->Warga->email;
                $recipientNamePengurus = $notif_pengurus->Warga->name;
                $status = "Sudah di Terima";
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
                return back()->with('success', 'Terima kasih sudah mengkonfirmasi, semoga bermanfaat');
            } else {
                return back()->with('warning', 'sudah mengkonfirmasi, tetapi Notifikasi tidak terkirim ke Pengurus!');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan, Mohon beritahu pengurus untuk error ini.' . $e->getMessage());
        }
    }
    public function laporan()
    {
        $pinjaman_proses = Loan::where('status', '!=', 'Paid in Full')->get()->map(function ($transaction) {
            $deadlineDate = Carbon::parse($transaction->deadline_date);
            $currentDate = Carbon::now();

            // Hitung sisa waktu
            $transaction->remaining_time = round(
                $currentDate->diffInDays($deadlineDate)
            );

            return $transaction;
        });
        $pinjaman_selesai = Loan::where('status', 'Paid in Full')->get();

        return view('user.program.kas.laporan.pinjaman', compact('pinjaman_selesai', 'pinjaman_proses'));
    }
}

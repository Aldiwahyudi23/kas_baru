<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\Notification;
use App\Models\DataWarga;
use App\Models\Deposit;
use App\Models\DepositDetail;
use App\Models\KasPayment;
use App\Models\Konter\TransaksiKonter;
use App\Models\loanRepayment;
use App\Models\Saldo;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SetorTunaiController extends Controller
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
        $kasPayments = KasPayment::where('is_deposited', false)->where('deposit_id', Null)->where('status', 'confirmed')->get();
        $loanRepayments = loanRepayment::where('is_deposited', false)->where('deposit_id', Null)->where('status', 'confirmed')->get();
        $konters = TransaksiKonter::where('is_deposited', false)->where('deposit_id', Null)->where('status', 'Selesai')->get();

        $data_deposit = Deposit::all();

        return view('user.setor_tunai.index', compact('kasPayments', 'loanRepayments', 'data_deposit', 'konters'));
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
            'selected_ids' => 'required|array',
            'total_kas' => 'required|numeric',
            'total_loan' => 'required|numeric',
            'total_all' => 'required|numeric',
            'description' => 'required|string|max:1000',
            'photo' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // Mengambil waktu saat ini
        $dateTime = now();
        // Format tanggal dan waktu
        $formattedDate = $dateTime->format('dmy'); // Dapatkan format DDMMYY
        $formattedTime = $dateTime->format('His'); // Dapatkan format HHMMSS
        // Menghitung jumlah admin saat ini dan menambahkan 1 untuk urutan
        $depo = Deposit::count() + 1;
        // Membuat kode kas
        $code = 'ST-' . $formattedDate . $formattedTime . str_pad($depo, 1, '0', STR_PAD_LEFT);
        // Format akhir: ADM-DDMMYYHHMMSS1

        // Simpan Foto
        $filePath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'ST-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/kas/deposit'), $filename);  // Simpan gambar ke folder public/storage/kas/pemasukan
            $filePath = "storage/kas/deposit/$filename";  // Simpan path gambar ke database
        }

        // Tambahkan rincian ke deskripsi
        $fullDescription = $request->description . "<br><br>";
        $fullDescription .= "Rincian: <br>";
        $fullDescription .= "- Total Kas: Rp" . number_format($request->total_kas, 0, ',', '.') . "<br>";
        $fullDescription .= "- Total Pinjaman: Rp" . number_format($request->total_loan, 0, ',', '.') . "<br>";
        $fullDescription .= "- Total Konter: Rp" . number_format($request->total_konter, 0, ',', '.') . "\n";
        // $fullDescription .= "- Total Tabungan: Rp" . number_format($tabunganTotal, 0, ',', '.') . "\n";

        DB::beginTransaction();
        try {
            // Simpan ke Deposit
            $deposit = new Deposit();
            $deposit->code = $code;
            $deposit->submitted_by = Auth::user()->data_warga_id;
            $deposit->amount = $request->total_all;
            $deposit->status = "pending";
            $deposit->description = $fullDescription;
            $deposit->receipt_path = $filePath;

            $deposit->save();

            // Update Transaksi
            foreach ($request->selected_ids as $id) {
                [$type, $recordId] = explode('-', $id);

                switch ($type) {
                    case 'kas':
                        KasPayment::where('id', $recordId)
                            ->update([
                                'deposit_id' => $deposit->id
                            ]);
                        break;

                    case 'loan':
                        LoanRepayment::where('id', $recordId)
                            ->update([
                                'deposit_id' => $deposit->id
                            ]);
                        break;
                    case 'konter':
                        TransaksiKonter::where('id', $recordId)
                            ->update([
                                'deposit_id' => $deposit->id
                            ]);
                        break;
                }
            }

            // Simpan ke Deposit Details
            foreach ($request->selected_ids as $id) {
                [$type, $transactionId] = explode('-', $id);

                $data = new DepositDetail();
                $data->deposit_id = $deposit->id;
                $data->transaction_type = $type;
                $data->transaction_id = $transactionId;
                $data->save();
            }

            // // Mengambil data pengaju (pengguna yang menginput)
            // $pengaju = DataWarga::find(Auth::user()->data_warga_id);
            // // Mengambil nomor telepon Ketua Untuk Laporan
            // $ketua = User::whereHas('role', function ($query) {
            //     $query->where('name', 'Ketua');
            // })->with('dataWarga')->first();

            // $phoneNumberPengurus = $ketua->dataWarga->no_hp ?? null;

            // // Data untuk pesan
            // $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            // $link = "https://keluargamahaya.com/confirm/pengeluaran/{$encryptedId}";

            // // Membuat pesan WhatsApp
            // $messageKetua = "*Persetujuan Setor Tunai*\n";
            // $messageKetua .= "Halo {$ketua->dataWarga->name},\n\n";
            // $messageKetua .= "Terdapat pengajuan Setor Tunai yang sudah di input, Perlu di Konfirmasi agar masuk data. Berikut detail pengajuannya:\n\n";
            // $messageKetua .= "- *Kode* : {$code}\n";
            // $messageKetua .= "- *Tanggal Deposit*: {$$dateTime}\n";
            // $messageKetua .= "- *Type*: Setor Tunai\n";
            // $messageKetua .= "- *Penyetor*: {$pengaju->name}\n";
            // $messageKetua .= "- *Nominal*: Rp" . number_format($request->total_all, 0, ',', '.') . "\n\n";
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

            // // Jik nitifikasi di aktifkan return yang ini di hapus

            DB::commit();
            return redirect()->route('setor-tunai.index')->with('success', 'Setor tunai berhasil disimpan, menunggu di konfirmasi');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan.' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $id = Crypt::decrypt($id);
        $deposit = Deposit::findOrFail($id);
        $data_kas = KasPayment::where('deposit_id', $deposit->id)->get();
        $data_loanRepayment = loanRepayment::where('deposit_id', $deposit->id)->get();
        // Tampah data lain yang terhubung

        return view('user.setor_tunai.detail', compact('deposit', 'data_kas', 'data_loanRepayment'));
    }
    public function detail_reject(string $id)
    {
        $id = Crypt::decrypt($id);
        $deposit = Deposit::findOrFail($id);
        $data_kas = KasPayment::where('deposit_id', $deposit->id)->get();
        $data_loanRepayment = loanRepayment::where('deposit_id', $deposit->id)->get();
        $data_konter = TransaksiKonter::where('deposit_id', $deposit->id)->get();
        // Tampah data lain yang terhubung
        $depositDetail = Deposit::with('details')->findOrFail($id);
        $kasData = [];
        $loanData = [];
        $konterData = [];

        // Perbarui Status dan kumpulkan data
        foreach ($deposit->details as $detail) {
            if ($detail->transaction_type === 'kas') {
                $kas = KasPayment::where('id', $detail->transaction_id)->first();
                if ($kas) {
                    $kasData[] = $kas; // Tambahkan data ke array
                }
            } elseif ($detail->transaction_type === 'loan') {
                $loan = LoanRepayment::where('id', $detail->transaction_id)->first();
                if ($loan) {
                    $loanData[] = $loan; // Tambahkan data ke array
                }
            } elseif ($detail->transaction_type === 'konter') {
                $konter = TransaksiKonter::where('id', $detail->transaction_id)->first();
                if ($konter) {
                    $konterData[] = $konter; // Tambahkan data ke array
                }
            }
        }

        return view('user.setor_tunai.detail_reject', compact(
            'deposit',
            'data_kas',
            'data_loanRepayment',
            'data_konter',
            'kasData',
            'loanData',
            'konterData',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function pengajuan()
    {
        $deposit = Deposit::where('status', 'pending')->get();
        $deposit_reject = Deposit::where('status', 'rejected')->get();

        return view('user.setor_tunai.pengajuan', compact('deposit', 'deposit_reject'));
    }
    public function show_confirm(String $id)
    {
        $id = Crypt::decrypt($id);
        $deposit = Deposit::findOrFail($id);
        $data_kas = KasPayment::where('deposit_id', $deposit->id)->get();
        $data_loanRepayment = loanRepayment::where('deposit_id', $deposit->id)->get();
        $data_konter = TransaksiKonter::where('deposit_id', $deposit->id)->get();
        // Tampah data lain yang terhubung

        return view('user.setor_tunai.konfirmasi', compact('deposit', 'data_kas', 'data_loanRepayment', 'data_konter'));
    }

    // sementara di balik dengan yang bawag
    public function confirm(Request $request, String $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'amount' => 'required',
            'status' => 'required'
        ]);
        $kas = KasPayment::where('deposit_id', $id)->where('is_deposited', false)->get();
        $loan = loanRepayment::where('deposit_id', $id)->where('is_deposited', false)->get();
        $konter = TransaksiKonter::where('deposit_id', $id)->where('is_deposited', false)->get();

        // Periksa apakah kedua koleksi kosong
        if ($kas->isEmpty() && $loan->isEmpty()&& $konter->isEmpty() ) {
            return redirect()->back()->with('error', 'Data Setor Tunai kosong, tidak bisa dilanjutkan.');
        }


        DB::beginTransaction();
        try {
            // Merubah data deposite
            $deposit = Deposit::findOrFail($id);
            $deposit->status = $request->status;
            $deposit->confirmed_by = Auth::user()->data_warga_id;
            $deposit->confirmation_date = now();

            $deposit->update();


            // -----------------------
            if ($request->status == "rejected") {
                // Mengubah semua data menjadi true

                foreach ($kas as $data) {
                    $setor_kas = KasPayment::find($data->id);
                    $setor_kas->deposit_id = Null;
                    $setor_kas->update();
                }

                foreach ($loan as $data) {
                    $setor_kas = loanRepayment::find($data->id);
                    $setor_kas->deposit_id = Null;
                    $setor_kas->update();
                }

                foreach ($konter as $data) {
                    $setor_konter = loanRepayment::find($data->id);
                    $setor_konter->deposit_id = Null;
                    $setor_konter->update();
                }

                DB::commit();
                return redirect()->back()->with('success', 'Setor tunai di reject data akan kembali ke awal, namun data setor tunai masih ada');
            } else if ($request->status == "confirmed") {
                // Mengubah semua data menjadi true
                foreach ($kas as $data) {
                    $setor_kas = KasPayment::find($data->id);
                    $setor_kas->is_deposited = true;
                    $setor_kas->update();
                }

                foreach ($loan as $data) {
                    $setor_kas = loanRepayment::find($data->id);
                    $setor_kas->is_deposited = true;
                    $setor_kas->update();
                }

                foreach ($konter as $data) {
                    $setor_konter = loanRepayment::find($data->id);
                    $setor_konter->deposit_id = true;
                    $setor_konter->update();
                }


                // pemindahan data dari nominal uang di luar ke atm
                $saldo_terbaru = Saldo::latest()->first();
                $saldo = new Saldo();
                $saldo->code = $deposit->code;
                $saldo->amount = $request->amount;
                $saldo->atm_balance = $saldo_terbaru->atm_balance + $request->amount;
                $saldo->total_balance = $saldo_terbaru->total_balance;
                $saldo->ending_balance = $saldo_terbaru->total_balance;
                $saldo->cash_outside = $saldo_terbaru->cash_outside - $request->amount;

                $saldo->save();

                DB::commit();
                return redirect()->back()->with('success', 'Setor tunai sudah di setujui atos merubah data');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengkonfirmasi.' . $e->getMessage());
        }
    }
}

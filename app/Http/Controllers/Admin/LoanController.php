<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessProgram;
use App\Models\ActivityLog;
use App\Models\Anggaran;
use App\Models\AnggaranSaldo;
use App\Models\AnggaranSetting;
use App\Models\CashExpenditures;
use App\Models\DataWarga;
use App\Models\Loan;
use App\Models\loanRepayment;
use App\Models\Program;
use App\Models\Role;
use App\Models\Saldo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Untuk Konfirmasi delet
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $cek_anggaran_pinjam = Anggaran::where('name', 'Dana Pinjam')->first();
        $pinjaman_selesai = Loan::where('status', "paid in full")->get();
        $pinjaman_proses = Loan::where('status', '!=',  "paid in full")->get();
        $data_warga = DataWarga::all();

        // Ambil ID berdasarkan nama role
        $roles = Role::whereIn('name', ['Ketua', 'Bendahara', 'Sekretaris'])->pluck('id')->toArray();
        // Ambil data user berdasarkan role_id yang sesuai
        $pengurus_user = User::whereIn('role_id', $roles)->get();

        // Ambil program dengan nama "Kas"
        // $programKas = Program::where('name', 'Kas Keluarga')->first();
        // $accessProgram = AccessProgram::where('program_id', $programKas->id)->pluck('data_warga_id');
        // $users = User::whereIn('data_warga_id', $accessProgram)->get();

        $user = User::all();

        return view('admin.program.kas.pinjaman.index', compact('pinjaman_selesai', 'pinjaman_proses', 'cek_anggaran_pinjam', 'data_warga', 'pengurus_user', 'user'));
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
            'data_warga_id' => 'required',
            'remaining_balance' => 'required',
            'overpayment_balance' => 'required',
            'loan_amount' => 'required',
            'description' => 'required',
            'status' => 'required',
            'submitted_by' => 'required',
            'approved_by' => 'required',
            'disbursed_by' => 'required',
            'approved_date' => 'required',
            'disbursed_date' => 'required',
            'disbursement_receipt_path' => 'required',
        ]);

        $dataAnggaran = Anggaran::where('name', 'Dana Pinjam')->first();
        // Cek apakah Saldo cukup berdasarkan anggaran

        $saldo_akhir_request =  AnggaranSaldo::where('type', $dataAnggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran

        if ($saldo_akhir_request->saldo <  $request->loan_amount) {
            return redirect()->back()->with('error', 'Saldo untuk ' . $dataAnggaran->name . ' Kurang dari pengajuan.');
        }
        // -------------------------------------------

        // cek untuk nominal Max sesuai kesepakatan
        $nominal_max = AnggaranSetting::where('anggaran_id', $dataAnggaran->id)->where('label_anggaran', 'Alokasi Anggaran Max')->first();
        if ($request->loan_amount > $nominal_max->catatan_anggaran) {
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
            if ($latestLoan->status !== 'paid in full') {
                return back()->with('error', 'Pengajuan pinjaman tidak dapat dilanjutkan. Status pengajuan terakhir masih: ' . $latestLoan->status);
            }

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

                // Cek waktu satu minggu sejak tanggal pengajuan pinjaman terakhir
                if ($loanCreationDate->addWeek()->isFuture()) {
                    return back()->with('error', 'Pengajuan baru tidak dapat dilakukan. Harap tunggu satu minggu sejak pengajuan terakhir pada ' . $loanCreationDate->format('d-m-Y'));
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
            $data = new Loan();
            $data->code = $code;
            $data->anggaran_id = $dataAnggaran->id;
            $data->data_warga_id = $request->data_warga_id;
            $data->loan_amount = $request->loan_amount;
            $data->overpayment_balance = $request->overpayment_balance;
            $data->remaining_balance = $request->remaining_balance;
            $data->description = $request->description;
            $data->status = $request->status;
            $data->submitted_by = $request->submitted_by;
            $data->approved_by = $request->approved_by;
            $data->disbursed_by = $request->disbursed_by;
            $data->approved_date = $request->approved_date;
            $data->disbursed_date = $request->disbursed_date;
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

            $data->save();
            // -------------------------------------

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

            // 1. Ambil semua anggaran dengan label "persentase"
            $anggaranItems = AnggaranSetting::where('label_anggaran', 'persentase')->get();
            foreach ($anggaranItems as $anggaran) {
                $anggaran_saldo_terakhir =  AnggaranSaldo::where('type', $anggaran->anggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
                // Hitung alokasi dana berdasarkan catatan_anggaran sebagai persentase
                $percenAmount = ($request->loan_amount / $saldo_akhir_request->saldo) * 100;
                $saldo_anggaran = new AnggaranSaldo();

                if ($anggaran->anggaran->name === $saldo_akhir_request->type) {
                    $saldo_anggaran->type = $dataAnggaran->name;
                    $saldo_anggaran->percentage = $percenAmount;
                    $saldo_anggaran->amount = '-' . $request->loan_amount;
                    $saldo_anggaran->saldo = $saldo_akhir_request->saldo - $request->loan_amount;
                } else {
                    $saldo_anggaran->type = $anggaran_saldo_terakhir->type;
                    $saldo_anggaran->percentage = $anggaran_saldo_terakhir->percentage;
                    $saldo_anggaran->amount = $anggaran_saldo_terakhir->amount;
                    $saldo_anggaran->saldo = $anggaran_saldo_terakhir->saldo;
                }
                $saldo_anggaran->saldo_id = $saldo->id; //mengambil id dari model saldo di atas

                $saldo_anggaran->save();
            }
            DB::commit();

            return redirect()->back()->with('success', 'Data Pinjaman berhasil di keluarkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data pengeluaran.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = Crypt::decrypt($id);
        $loan = Loan::Find($id);
        $loan_repayment = loanRepayment::where('loan_id', $loan->id)->get();
        $activityLogAdmin = ActivityLog::where('code', $loan->code)->get();

        return view('admin.program.kas.pinjaman.show', compact('loan', 'loan_repayment', 'activityLogAdmin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = Crypt::decrypt($id);
        // Untuk Konfirmasi delet
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $cek_anggaran_pinjam = Anggaran::where('name', 'Dana Pinjam')->first();
        $pinjaman_selesai = Loan::where('status', "paid in full")->get();
        $pinjaman_proses = Loan::where('status', '!=',  "paid in full")->get();
        $data_warga = DataWarga::all();
        $user = User::all();

        // Ambil ID berdasarkan nama role
        $roles = Role::whereIn('name', ['Ketua', 'Bendahara', 'Sekretaris'])->pluck('id')->toArray();
        // Ambil data user berdasarkan role_id yang sesuai
        $pengurus_user = User::whereIn('role_id', $roles)->get();

        $loan = Loan::Find($id);

        return view('admin.program.kas.pinjaman.edit', compact('loan', 'pinjaman_selesai', 'pinjaman_proses', 'data_warga', 'user', 'loan', 'pengurus_user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'data_warga_id' => 'required',
            'remaining_balance' => 'required',
            'overpayment_balance' => 'required',
            // 'loan_amount' => 'required',
            'description' => 'required',
            'status' => 'required',
            'submitted_by' => 'required',
            'approved_by' => 'required',
            'disbursed_by' => 'required',
            // 'approved_date' => 'required',
            // 'disbursed_date' => 'required',
            // 'disbursement_receipt_path' => 'required',
        ]);

        $data = Loan::Find($id);
        if ($request->has('loan_amount') && $request->input('loan_amount') != $data->loan_amount) {
            // Jika ada perubahan, kirim pesan peringatan
            return redirect()->back()->with('error', 'Nominal tidak bisa diubah!');
        }
        // $data->code = $code;
        // $data->anggaran_id = $dataAnggaran->id;
        $data->data_warga_id = $request->data_warga_id;
        // $data->loan_amount = $request->loan_amount;
        $data->overpayment_balance = $request->overpayment_balance;
        $data->remaining_balance = $request->remaining_balance;
        $data->description = $request->description;
        $data->status = $request->status;
        $data->submitted_by = $request->submitted_by;
        $data->approved_by = $request->approved_by;
        $data->disbursed_by = $request->disbursed_by;
        if ($request->approved_date) {
            $data->approved_date = $request->approved_date;
        }
        if ($request->disbursed_date) {
            $data->disbursed_date = $request->disbursed_date;
        }
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
        return redirect()->back()->with('success', ' Data berhasil di ubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = Loan::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Data Pinjaman sudah di hapus');
    }
}

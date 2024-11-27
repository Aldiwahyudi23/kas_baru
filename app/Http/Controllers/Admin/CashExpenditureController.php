<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Anggaran;
use App\Models\AnggaranSaldo;
use App\Models\AnggaranSetting;
use App\Models\CashExpenditures;
use App\Models\Role;
use App\Models\Saldo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\ReturnValueNotConfiguredException;

class CashExpenditureController extends Controller
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

        $pengeluaran = CashExpenditures::all();
        $anggaran = Anggaran::where('program_id', 1)->get();

        // Ambil ID berdasarkan nama role
        $roles = Role::whereIn('name', ['Ketua', 'Bendahara', 'Sekretaris'])->pluck('id')->toArray();

        // Ambil data user berdasarkan role_id yang sesuai
        $pengurus_user = User::whereIn('role_id', $roles)->get();

        return view('admin.program.kas.pengeluaran.index', compact('pengeluaran', 'anggaran', 'pengurus_user'));
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
            'status' => 'required',
            'submitted_by' => 'required',
            'approved_by' => 'required',
            'disbursed_by' => 'required',
            'approved_date' => 'required',
            'disbursed_date' => 'required',
            'receipt_path' => 'required',
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
            $data->status = $request->status;
            $data->submitted_by = $request->submitted_by;
            $data->approved_by = $request->approved_by;
            $data->disbursed_by = $request->disbursed_by;
            $data->approved_date = $request->approved_date;
            $data->disbursed_date = $request->disbursed_date;
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

            $data->save();
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

            // 1. Ambil semua anggaran dengan label "persentase"
            $anggaranItems = AnggaranSetting::where('label_anggaran', 'persentase')->get();
            foreach ($anggaranItems as $anggaran) {
                $anggaran_saldo_terakhir =  AnggaranSaldo::where('type', $anggaran->anggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
                // Hitung alokasi dana berdasarkan catatan_anggaran sebagai persentase
                $percenAmount = ($request->amount / $saldo_akhir_request->saldo) * 100;
                $saldo_anggaran = new AnggaranSaldo();

                if ($anggaran->anggaran->name === $saldo_akhir_request->type) {
                    $saldo_anggaran->type = $dataAnggaran->name;
                    $saldo_anggaran->percentage = $percenAmount;
                    $saldo_anggaran->amount = '-' . $request->amount;
                    $saldo_anggaran->saldo = $saldo_akhir_request->saldo - $request->amount;
                } else {
                    $saldo_anggaran->type = $anggaran_saldo_terakhir->type;
                    $saldo_anggaran->percentage = $anggaran_saldo_terakhir->percentage;
                    $saldo_anggaran->amount = $anggaran_saldo_terakhir->amount;
                    $saldo_anggaran->saldo = $anggaran_saldo_terakhir->saldo;
                }
                $saldo_anggaran->saldo_id = $saldo->id; //mengambil id dari model saldo di atas
                $saldo_anggaran->cash_saldo = $anggaran_saldo_terakhir->cash_saldo - $request->amount;

                $saldo_anggaran->save();
            }
            DB::commit();

            return redirect()->back()->with('success', 'Data Pengeluaran berhasil di keluarkan');
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

        $dataEx = CashExpenditures::FindOrFail($id);
        $activityLogAdmin = ActivityLog::where('code', $dataEx->code)->get();
        return view('admin.program.kas.pengeluaran.show', compact('dataEx', 'activityLogAdmin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Untuk Konfirmasi delet
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $pengeluaran = CashExpenditures::all();
        $anggaran = Anggaran::where('program_id', 1)->get();

        // Ambil ID berdasarkan nama role
        $roles = Role::whereIn('name', ['Ketua', 'Bendahara', 'Sekretaris'])->pluck('id')->toArray();

        // Ambil data user berdasarkan role_id yang sesuai
        $pengurus_user = User::whereIn('role_id', $roles)->get();
        $id = Crypt::decrypt($id);
        $dataEx = CashExpenditures::Find($id);

        return  view('admin.program.kas.pengeluaran.edit', compact('dataEx', 'pengurus_user', 'pengeluaran', 'anggaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'description' => 'required',
            'status' => 'required',
            'submitted_by' => 'required',
            'approved_by' => 'required',
            'disbursed_by' => 'required',
        ]);
        // Ambil data saldo berdasarkan ID
        $cashEx = CashExpenditures::findOrFail($id);
        // Cek apakah ada perubahan pada 'amount'
        if ($request->has('amount') && $request->input('amount') != $cashEx->amount) {
            // Jika ada perubahan, kirim pesan peringatan
            return redirect()->back()->with('error', 'Nominal tidak bisa diubah!');
        }

        $data = CashExpenditures::FindOrFail($id);

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
        // // Cek apakah file profile_picture di-upload
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

        return redirect()->back()->with('success', 'Data sudah di Update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = CashExpenditures::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Data Kas sudah di hapus');
    }
}

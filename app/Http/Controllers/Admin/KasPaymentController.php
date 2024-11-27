<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessProgram;
use App\Models\ActivityLog;
use App\Models\Anggaran;
use App\Models\AnggaranSaldo;
use App\Models\AnggaranSetting;
use App\Models\DataWarga;
use App\Models\KasPayment;
use App\Models\Program;
use App\Models\Role;
use App\Models\Saldo;
use App\Models\User;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class KasPaymentController extends Controller
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
        // Ambil program dengan nama "Kas"
        $programKas = Program::where('name', 'Kas Keluarga')->first();
        $accessProgram = AccessProgram::where('program_id', $programKas->id)->get();

        // mengambil data semua Kas
        $allKas = KasPayment::all();
        // mengambil data semua data warga
        $dataWarga = DataWarga::all();

        // Ambil ID berdasarkan nama role
        $roles = Role::whereIn('name', ['Ketua', 'Bendahara', 'Sekretaris'])->pluck('id')->toArray();

        // Ambil data user berdasarkan role_id yang sesuai
        $pengurus_user = User::whereIn('role_id', $roles)->get();

        return view('admin.program.kas.pemasukan.index', compact('accessProgram', 'allKas', 'dataWarga', 'pengurus_user'));
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
            'payment_date' => 'required',
            'data_warga_id' => 'required',
            'amount' => 'required',
            'payment_method' => 'required',
            'description' => 'required',
            'submitted_by' => 'required',
            'confirmed_by' => 'required',
            'status' => 'required',
            'confirmation_date' => 'required',
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

            $data = new KasPayment();
            $data->code = $code;
            $data->data_warga_id = $request->data_warga_id;
            $data->amount = $request->amount;
            $data->payment_date = $request->payment_date;
            $data->payment_method = $request->payment_method;
            $data->description = $request->description;
            $data->submitted_by = $request->submitted_by;
            $data->confirmed_by = $request->confirmed_by;
            $data->status = $request->status;
            $data->confirmation_date = $request->confirmation_date;
            $data->is_deposited = $request->is_deposited;
            // Cek apakah file profile_picture di-upload
            if ($request->hasFile('transfer_receipt_path')) {
                $file = $request->file('transfer_receipt_path');
                $path = $file->store(
                    'kas/pemasukan',
                    'public'
                ); // Simpan gambar ke direktori public
                $data->transfer_receipt_path = $path;
            }

            $data->save();
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


            foreach ($anggaranItems->get() as $anggaran) {
                $anggaran_saldo_terakhir =  AnggaranSaldo::where('type', $anggaran->anggaran->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
                // Hitung alokasi dana berdasarkan catatan_anggaran sebagai persentase
                $allocatedAmount = $request->amount * ($anggaran->catatan_anggaran / 100);
                $saldo_anggaran = new AnggaranSaldo();
                $saldo_anggaran->saldo_id = $saldo->id; //mengambil id dari model saldo di atas
                $saldo_anggaran->type = $anggaran->anggaran->name;
                $saldo_anggaran->percentage = $anggaran->catatan_anggaran;
                $saldo_anggaran->amount = $allocatedAmount;
                $saldo_anggaran->saldo = ($anggaran_saldo_terakhir->saldo ?? 0) + $allocatedAmount;
                $saldo_anggaran->cash_saldo = ($anggaran_saldo_terakhir->cash_saldo ?? 0) + $request->amount;

                $saldo_anggaran->save();
            }



            DB::commit();

            return redirect()->back()->with('success', 'Pembayaran kas berhasil');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pemasukan.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = Crypt::decrypt($id);
        $dataKas = KasPayment::FindOrFail($id);
        $activityLogAdmin = ActivityLog::where('code', $dataKas->code)->get();
        return view('admin.program.kas.pemasukan.show', compact('dataKas', 'activityLogAdmin'));
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

        $id = Crypt::decrypt($id);
        // Ambil program dengan nama "Kas"
        $programKas = Program::where('name', 'Kas Keluarga')->first();
        $accessProgram = AccessProgram::where('program_id', $programKas->id)->get();

        // mengambil data semua Kas
        $allKas = KasPayment::all();
        // mengambil data semua data warga
        $dataWarga = DataWarga::all();

        // Ambil ID berdasarkan nama role
        $roles = Role::whereIn('name', ['Ketua', 'Bendahara', 'Sekretaris'])->pluck('id')->toArray();

        // Ambil data user berdasarkan role_id yang sesuai
        $pengurus_user = User::whereIn('role_id', $roles)->get();

        // mengambil data kas sesuai id
        $dataKas = KasPayment::FindOrFail($id);

        return view('admin.program.kas.pemasukan.edit', compact('accessProgram', 'allKas', 'dataWarga', 'pengurus_user', 'dataKas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);

        $request->validate([
            'data_warga_id' => 'required',
            // 'amount' => 'required',
            'payment_method' => 'required',
            'description' => 'required',
            'submitted_by' => 'required',
            'confirmed_by' => 'required',
            'status' => 'required',
            'is_deposited' => 'required',
        ]);

        // Ambil data saldo berdasarkan ID
        $cashPay = KasPayment::findOrFail($id);
        // Cek apakah ada perubahan pada 'amount'
        if ($request->has('amount') && $request->input('amount') != $cashPay->amount) {
            // Jika ada perubahan, kirim pesan peringatan
            return redirect()->back()->with('error', 'Nominal tidak bisa diubah!');
        }

        $data = KasPayment::FindOrFail($id);

        $data->data_warga_id = $request->data_warga_id;
        // $data->amount = $request->amount;
        $data->payment_method = $request->payment_method;
        $data->description = $request->description;
        $data->submitted_by = $request->submitted_by;
        $data->confirmed_by = $request->confirmed_by;
        $data->status = $request->status;
        $data->is_deposited = $request->is_deposited;

        if ($request->payment_date) {
            $data->payment_date = $request->payment_date;
        }
        if ($request->confirmation_date) {
            $data->confirmation_date = $request->confirmation_date;
        }
        // Cek apakah file profile_picture di-upload
        if ($request->hasFile('transfer_receipt_path')) {
            $file = $request->file('transfer_receipt_path');
            $path = $file->store(
                'kas/pemasukan',
                'public'
            ); // Simpan gambar ke direktori public
            $data->transfer_receipt_path = $path;
        }

        $data->update();


        return redirect()->back()->with('success', 'Data Kas berhasil di update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = KasPayment::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Data Kas sudah di hapus');
    }
}

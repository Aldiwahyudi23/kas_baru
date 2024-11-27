<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Anggaran;
use App\Models\AnggaranSetting;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AnggaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Untuk konfirmasi delete
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $anggaran = Anggaran::all();
        $program = Program::all();

        return view('admin.master_data.data_anggaran.index', compact('anggaran', 'program'));
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
        // validasi input form 
        $request->validate([
            'name' => 'required|string|max:225',
            'code_anggaran' => 'required|string',
            'program_id' => 'required|string',
            'description' => 'required|string',
            'is_active' => 'required|in:0,1',
        ], [
            'name.required' => "Nama Anggaran harus di isi ",
            'code_anggaran.required' => "Kode Anggaran harus di isi ",
            'program_id.required' => "Anggaran harus di isi ",
            'description.required' => "Deskripsi harus di isi ",
            'is_active.required' => "Status harus di isi ",
        ]);

        // Mengambil waktu saat ini
        $dateTime = now();
        // Format tanggal dan waktu
        $formattedDate = $dateTime->format('dmy'); // Dapatkan format DDMMYY
        $formattedTime = $dateTime->format('His'); // Dapatkan format HHMMSS
        // Menghitung jumlah admin saat ini dan menambahkan 1 untuk urutan
        $Anggarancount = Anggaran::count() + 1;
        // Membuat kode admin
        $code = 'A-' . $formattedDate . $formattedTime . str_pad($Anggarancount, 1, '0', STR_PAD_LEFT);
        // Format akhir: ADM-DDMMYYHHMMSS1

        $data = new Anggaran();
        $data->code = $code;
        $data->program_id = $request->program_id;
        $data->code_anggaran = $request->code_anggaran;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->is_active = $request->is_active;

        $data->save();

        return redirect()->back()->with('success', 'Data Anggaran berhasil di simpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //encripsi Id
        $id = Crypt::decrypt($id);
        $DataAnggaran = Anggaran::FindOrFail($id);
        $anggaran_sett = AnggaranSetting::where('anggaran_id', $id)->get(); //mengambil data yang sesuai dengan id anggaran
        $activityLogAdmin = ActivityLog::where('code', $DataAnggaran->code)->get(); // Mengambil data aktifitas Anggaran sesuai dengan kode

        return view('admin.master_data.data_anggaran.show', compact('DataAnggaran', 'activityLogAdmin', 'anggaran_sett'));
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
        $anggaran = Anggaran::all();
        $program = Program::all();
        $DataAnggaran = Anggaran::FindOrFail($id);

        return view('admin.master_data.data_anggaran.edit', compact('anggaran', 'DataAnggaran', 'program'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // validasi input form 
        $request->validate([
            'name' => 'required|string|max:225',
            'code_anggaran' => 'required|string',
            'program_id' => 'required|string',
            'description' => 'required|string',
        ], [
            'code_anggaran.required' => "Kode Anggaran harus di isi ",
            'name.required' => "Nama Anggaran harus di isi ",
            'program_id.required' => "Anggaran harus di isi ",
            'description.required' => "Deskripsi harus di isi ",
        ]);
        $id = Crypt::decrypt($id);

        $data = Anggaran::FindOrFail($id);
        $data->program_id = $request->program_id;
        $data->code_anggaran = $request->code_anggaran;
        $data->name = $request->name;
        $data->description = $request->description;

        $data->update();

        return redirect()->back()->with('success', 'Data Anggaran berhasil di Update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = Anggaran::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Data Anggaran sudah di hapus');
    }

    public function toggleActive(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $data = Anggaran::findOrFail($id);
        // Toggle status
        $data->is_active = $request->is_active;
        $data->save();

        return response()->json([
            'success' => true,
            'new_status' => $data->is_active // Kembalikan status baru
        ]);
    }

    public function storeAnggaranSetting(Request $request)
    {
        // Validasi input
        $request->validate(
            [
                'label_anggaran' => 'required|string',
                'catatan_anggaran' => 'required|string',
                'anggaran_id' => 'required|exists:anggarans,id',
            ],
            [
                'label_anggaran.required' => 'Label harus di isi',
                'catatan_anggaran.required' => 'catatan harus di isi',
            ]
        );

        // Simpan data ke dalam data
        $data = new AnggaranSetting();
        $data->label_anggaran = $request->label_anggaran;
        $data->catatan_anggaran = $request->catatan_anggaran;
        $data->anggaran_id = $request->anggaran_id;
        $data->save();

        // Kirim response dalam bentuk JSON
        return response()->json([
            'success' => true,
            'anggaran' => $data
        ]);
    }

    public function updateAnggaranSetting(Request $request, $id)
    {
        try {
            // Dekripsi ID jika Anda menggunakan Crypt
            $id = Crypt::decrypt($id);

            // Validasi data
            $request->validate([
                'label_anggaran' => 'required|string|max:255',
                'catatan_anggaran' => 'required|string|max:500',
            ]);

            // Cari data di model data dan update
            $data = AnggaranSetting::findOrFail($id);
            $data->label_anggaran = $request->label_anggaran;
            $data->catatan_anggaran = $request->catatan_anggaran;
            $data->save();

            // Kirim response dalam bentuk JSON
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, kembalikan pesan error
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate data.'], 400);
        }
    }

    // belum aktif
    public function destroyAnggaranSetting($id)
    {
        // Dekripsi ID jika Anda menggunakan Crypt
        $id = Crypt::decrypt($id);

        // Cari data di model data dan hapus
        $data = AnggaranSetting::findOrFail($id);
        $data->delete();

        // Kirim response dalam bentuk JSON
        return response()->json(['success' => true]);
    }
}

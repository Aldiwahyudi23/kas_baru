<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Anggaran;
use App\Models\Program;
use App\Models\ProgramSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ProgramController extends Controller
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

        $program = Program::all();

        return view('admin.master_data.data_program.index', compact('program'));
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
            'description' => 'required|string',
            'snk' => 'required|string',
            'created' => 'required',
            'is_active' => 'required|in:0,1',
        ], [
            'name.required' => "Nama Program harus di isi ",
            'description.required' => "Deskripsi harus di isi ",
            'snk.required' => "Syarat dan Ketentuan harus di isi ",
            'created.required' => "Tanggal di buat harus di isi ",
            'is_active.required' => "Status harus di isi ",
        ]);

        // Mengambil waktu saat ini
        $dateTime = now();
        // Format tanggal dan waktu
        $formattedDate = $dateTime->format('dmy'); // Dapatkan format DDMMYY
        $formattedTime = $dateTime->format('His'); // Dapatkan format HHMMSS
        // Menghitung jumlah admin saat ini dan menambahkan 1 untuk urutan
        $Programcount = Program::count() + 1;
        // Membuat kode admin
        $code = 'P-' . $formattedDate . $formattedTime . str_pad($Programcount, 1, '0', STR_PAD_LEFT);
        // Format akhir: ADM-DDMMYYHHMMSS1

        $data = new Program();
        $data->code = $code;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->snk = $request->snk;
        $data->created = $request->created;
        $data->is_active = $request->is_active;

        $data->save();

        return redirect()->back()->with('success', 'Data Program berhasil di simpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //encripsi Id
        $id = Crypt::decrypt($id);
        $DataProgram = Program::FindOrFail($id);
        $program_sett = ProgramSetting::where('program_id', $id)->get(); //Menagambil data dari program seeting
        $activityLogAdmin = ActivityLog::where('code', $DataProgram->code)->get(); //mengambil data aktiditas
        $anggaran = Anggaran::where('program_id', $id)->get(); //mengambil data anggaran yang berhubungan dengan program (program_id)

        return view('admin.master_data.data_program.show', compact('DataProgram', 'activityLogAdmin', 'program_sett', 'anggaran'));
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
        $program = Program::all();
        $DataProgram = Program::FindOrFail($id);

        return view('admin.master_data.data_program.edit', compact('program', 'DataProgram'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);
        // validasi input form 
        $request->validate([
            'name' => 'required|string|max:225',
            'description' => 'required|string',
            'snk' => 'required|string',
        ], [
            'name.required' => "Nama Program harus di isi ",
            'description.required' => "Deskripsi harus di isi ",
            'snk.required' => "Syarat dan Ketentuan harus di isi ",
        ]);
        $data = Program::FindOrFail($id);
        $data->name = $request->name;
        $data->description = $request->description;
        $data->snk = $request->snk;

        $data->update();

        return redirect()->back()->with('success', 'Data Program berhasil di Update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = Program::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Data Program sudah di hapus');
    }

    public function toggleActive(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $data = Program::findOrFail($id);
        // Toggle status
        $data->is_active = $request->is_active;
        $data->save();

        return response()->json([
            'success' => true,
            'new_status' => $data->is_active // Kembalikan status baru
        ]);
    }

    public function storeProgramSetting(Request $request)
    {
        // Validasi input
        $request->validate([
            'label_program' => 'required|string',
            'catatan_program' => 'required|string',
            'program_id' => 'required|exists:programs,id',
        ]);

        // Simpan data ke dalam ProgramSetting
        $programSetting = new ProgramSetting();
        $programSetting->label_program = $request->label_program;
        $programSetting->catatan_program = $request->catatan_program;
        $programSetting->program_id = $request->program_id;
        $programSetting->save();

        // Kirim response dalam bentuk JSON
        return response()->json([
            'success' => true,
            'program' => $programSetting
        ]);
    }

    public function updateProgramSetting(Request $request, $id)
    {
        try {
            // Dekripsi ID jika Anda menggunakan Crypt
            $id = Crypt::decrypt($id);

            // Validasi data
            $request->validate([
                'label_program' => 'required|string|max:255',
                'catatan_program' => 'required|string|max:500',
            ]);

            // Cari data di model ProgramSetting dan update
            $programSetting = ProgramSetting::findOrFail($id);
            $programSetting->label_program = $request->label_program;
            $programSetting->catatan_program = $request->catatan_program;
            $programSetting->save();

            // Kirim response dalam bentuk JSON
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, kembalikan pesan error
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate data.'], 400);
        }
    }

    // belum aktif
    public function destroyProgramSetting($id)
    {
        // Dekripsi ID jika Anda menggunakan Crypt
        $id = Crypt::decrypt($id);

        // Cari data di model ProgramSetting dan hapus
        $programSetting = ProgramSetting::findOrFail($id);
        $programSetting->delete();

        // Kirim response dalam bentuk JSON
        return response()->json(['success' => true]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class RoleController extends Controller
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

        $role = Role::all();

        return view('admin.master_data.data_role.index', compact('role'));
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
            'name' => 'required|string|max:225',
            'description' => 'required|string',
            'is_active' => 'required|in:0,1',
        ], [
            'name.required' => "Nama Role harus di isi ",
            'description.required' => "Deskripsi harus di isi ",
            'is_active.required' => "Status harus di isi ",
        ]);

        // Mengambil waktu saat ini
        $dateTime = now();
        // Format tanggal dan waktu
        $formattedDate = $dateTime->format('dmy'); // Dapatkan format DDMMYY
        $formattedTime = $dateTime->format('His'); // Dapatkan format HHMMSS
        // Menghitung jumlah admin saat ini dan menambahkan 1 untuk urutan
        $Rolecount = Role::count() + 1;
        // Membuat kode admin
        $code = 'R-' . $formattedDate . $formattedTime . str_pad($Rolecount, 1, '0', STR_PAD_LEFT);
        // Format akhir: ADM-DDMMYYHHMMSS1

        $data = new Role();
        $data->code = $code;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->is_active = $request->is_active;

        $data->save();

        return redirect()->back()->with('success', 'Data Role berhasil di simpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //encripsi Id
        $id = Crypt::decrypt($id);
        $DataRole = Role::FindOrFail($id);
        $user = User::where('role_id', $id)->get(); //Untuk mengambil data user 

        $activityLogAdmin = ActivityLog::where('code', $DataRole->code)->get(); //mengambil data aktiditas

        return view('admin.master_data.data_role.show', compact('DataRole', 'user', 'activityLogAdmin'));
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

        //encripsi Id
        $id = Crypt::decrypt($id);
        $DataRole = Role::FindOrFail($id);
        $role = Role::all(); //Untuk mengambil semua data

        return view('admin.master_data.data_role.edit', compact('DataRole', 'role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'name' => 'required|string|max:225',
            'description' => 'required|string',
        ], [
            'name.required' => "Nama Role harus di isi ",
            'description.required' => "Deskripsi harus di isi ",
        ]);

        $data = Role::FindOrFail($id);
        $data->name = $request->name;
        $data->description = $request->description;
        $data->update();

        return redirect()->back()->with('success', 'Data Role berhasil di Update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = Role::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Data Role sudah di hapus');
    }

    public function toggleActive(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $data = Role::findOrFail($id);
        // Toggle status
        $data->is_active = $request->is_active;
        $data->save();

        return response()->json([
            'success' => true,
            'new_status' => $data->is_active // Kembalikan status baru
        ]);
    }
}

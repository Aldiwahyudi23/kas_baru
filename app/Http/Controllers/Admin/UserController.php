<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DataWarga;
use App\Models\Program;
use App\Models\ProgramSetting;
use App\Models\Role;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use PHPUnit\Framework\Attributes\UsesTrait;

class UserController extends Controller
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

        $user = User::all();

        return view('admin.master_data.data_user.index', compact('user'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = crypt::decrypt($id);
        $dataUser = User::FindOrFail($id);
        $role = Role::all();
        $program = Program::all();
        $activityLogAdmin = ActivityLog::where('code', $dataUser->dataWarga->name)->get();

        return view('admin.master_data.data_user.show', compact('dataUser', 'activityLogAdmin', 'program', 'role'));
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
        $id = Crypt::decrypt($id);
        $request->validate([
            'name' => 'required',
            'no_hp' => [
                'required',
                'min:10',
                'max:14',
                Rule::unique('users')->ignore($id), // Mengabaikan no dengan ID yang sama
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id), // Mengabaikan email dengan ID yang sama
            ],
            'role_id' => 'required',
        ], [
            'name.required' => 'Nama Harus di isi ',
            'no_hp.required' => 'No Harus di isi ',
            'email.required' => 'email Harus di isi ',
            'email.unique' => 'email sudah terdaftar',
            'role_id.required' => 'Role Harus di isi ',
        ]);
        //transction, ada dua model dan jika di dalam try ini sukses semua data tersimpan dan jika ada yang error salah satu tidak tersimpan
        DB::beginTransaction();

        try {

            $data = User::FindOrFail($id);
            $data->name = $request->name;
            $data->role_id = $request->role_id;
            $data->no_hp = $request->no_hp;
            $data->email = $request->email;

            if ($request->password) {
                $data->password = Hash::make($request->password);
            }

            // Cek apakah file profile_picture di-upload
            // if ($request->hasFile('foto')) {
            //     $file = $request->file('foto');
            //     $path = $file->store(
            //         'foto',
            //         'public'
            //     ); // Simpan gambar ke direktori public
            //     $data->profile_photo_path = $path;
            // }

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = 'user-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('img/user'), $filename);  // Simpan gambar ke folder public/img/user
                $data->profile_photo_path = "img/user/$filename";  // Simpan path gambar ke database
            }

            $data->update();

            $account = DataWarga::Find($data->data_warga_id);
            $account->email = $request->email;
            $account->no_hp = $request->no_hp;

            $account->update();

            DB::commit();

            return redirect()->back()->with('success', ' Berhasil di ubah dengan data warga');
        } catch (\Exception $e) {
            // jika teradi error
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan data tidak di simpan' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = User::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Data user sudah di hapus');
    }

    public function toggleActive(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $data = User::findOrFail($id);
        // Toggle status
        $data->is_active = $request->is_active;
        $data->save();

        return response()->json([
            'success' => true,
            'new_status' => $data->is_active // Kembalikan status baru
        ]);
    }
}

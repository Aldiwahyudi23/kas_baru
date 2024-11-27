<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminAuthController extends Controller
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

        $dataAdmin = Admin::all(); //Mengambil semua data
        $adminCount = Admin::count(); // Hitung jumlah admin

        return view('admin.master_data.data_admin.index', compact('dataAdmin', 'adminCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input form
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8',
            'phone_number' => 'required|string|max:13',
            'profile_photo_path' => 'nullable|image|max:2048',
            'is_active' => 'required|in:0,1'
        ], [
            'email.unique' => "Email sudah terdaftar",
            'phone_number.max' => "No HandPhone terlalu panjang",
        ]);

        // Cek jumlah admin di database
        $adminCount = Admin::count();
        if ($adminCount >= 3) {
            // Jika sudah ada 3 admin, tampilkan error
            return redirect()->back()->with('warning', 'Jumlah admin sudah mencapai batas maksimum (3).');
        }

        // Mengambil waktu saat ini
        $dateTime = now();

        // Format tanggal dan waktu
        $formattedDate = $dateTime->format('dmy'); // Dapatkan format DDMMYY
        $formattedTime = $dateTime->format('His'); // Dapatkan format HHMMSS

        // Menghitung jumlah admin saat ini dan menambahkan 1 untuk urutan
        $adminCount = Admin::count() + 1;

        // Membuat kode admin
        $code = 'ADM-' . $formattedDate . $formattedTime . str_pad($adminCount, 1, '0', STR_PAD_LEFT);
        // Format akhir: ADM-DDMMYYHHMMSS1

        // Membuat instance Admin baru
        $admin = new Admin;
        $admin->code = $code;
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);
        $admin->phone_number = $request->phone_number;
        $admin->is_active = $request->is_active;

        // Cek apakah file profile_photo_path di-upload
        if ($request->hasFile('profile_photo_path')) {
            $file = $request->file('profile_photo_path');
            $path = $file->store('profile_photo_paths', 'public'); // Simpan gambar ke direktori public
            $admin->profile_photo_path = $path;
        } else {
            // Jika file tidak di-upload, gunakan gambar default
            $admin->profile_photo_path = 'default/profile_picture.png'; // Set path gambar default
        }

        // Simpan admin ke database
        $admin->save();

        return redirect()->back()->with('success', 'Data Admin berhasil ditambahkan.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = Crypt::decrypt($id);
        $admin = Admin::FindOrFail($id);
        $activityLogAdmin = ActivityLog::where('code', $admin->code)->get();

        return view('admin.master_data.data_admin.show', compact('admin', 'activityLogAdmin'));
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
        // Untuk Konfirmasi delet
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $dataAdmin = Admin::all(); //Mengambil semua data
        $adminCount = Admin::count(); // Hitung jumlah admin

        $admin = Admin::findOrFail($id);
        return view('admin.master_data.data_admin.update', compact('admin', 'adminCount', 'dataAdmin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('admins')->ignore($id), // Mengabaikan email dengan ID yang sama
            ],
            'password' => 'nullable|string|min:8',
            'phone_number' => 'required|string|max:15',
            'profile_photo_path' => 'nullable|image|max:2048'
        ]);

        $admin = Admin::findOrFail($id);
        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->password) {
            $admin->password = Hash::make($request->password);
        }

        $admin->phone_number = $request->phone_number;

        if ($request->hasFile('profile_photo_path')) {
            $file = $request->file('profile_photo_path');
            $path = $file->store('profile_photo_paths', 'public');
            $admin->profile_photo_path = $path;
        }

        $admin->save();

        return redirect()->back()->with('success', 'Admin updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return redirect()->back()->with('success', 'Data Admin sudah di hapus');
    }


    public function toggleStatus(Request $request, $id)
    {

        $id = Crypt::decrypt($id);
        $admin = Admin::findOrFail($id);

        // Toggle status
        $admin->is_active = $request->is_active;
        $admin->save();

        return response()->json([
            'success' => true,
            'new_status' => $admin->is_active // Kembalikan status baru
        ]);
    }








    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => "Email harus di isi",
            'email.email' => "Format email tidak falid",
            'password.required' => "Kata sandi harus di isi",
            'password.min' => "Kata sandi harus di isi minimal 6",
        ]);
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('dashboard.index');
        }
        // Jika gagal login, redirect kembali dengan pesan error
        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}

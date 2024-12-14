<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessProgram;
use App\Models\ActivityLog;
use App\Models\DataWarga;
use App\Models\Program;
use App\Models\Role;
use App\Models\StatusPekerjaan;
use App\Models\StatusPernikahan;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DataWargaController extends Controller
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
        // Untuk Konfirmasi delet
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $warga = DataWarga::all();


        return view('admin.master_data.data_warga.index', compact('warga'));
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
            'name' => 'required',
            'jenis_kelamin' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'alamat' => 'required',
            'agama' => 'required',
        ], [
            'name.required' => 'Nama Warga harus di isi',
            'jenis_kelamin.required' => 'Jenis Kelamin harus di isi',
            'tanggal.required' => 'Tanggal Lahir harus di isi',
            'alamat.required' => 'alamat harus di isi',
            'agama.required' => 'agama harus di isi',
        ]);


        //transction, ada dua model dan jika di dalam try ini sukses semua data tersimpan dan jika ada yang error salah satu tidak tersimpan
        DB::beginTransaction();

        try {
            // Mengambil waktu saat ini
            $dateTime = now();
            // Format tanggal dan waktu
            $formattedDate = $dateTime->format('dmy'); // Dapatkan format DDMMYY
            $formattedTime = $dateTime->format('His'); // Dapatkan format HHMMSS
            // Menghitung jumlah admin saat ini dan menambahkan 1 untuk urutan
            $wargaCount = DataWarga::count() + 1;
            // Membuat kode admin
            $code = 'WR-' . $formattedDate . $formattedTime . str_pad($wargaCount, 1, '0', STR_PAD_LEFT);
            // Format akhir: ADM-DDMMYYHHMMSS1

            $warga = new DataWarga();
            $warga->code = $code;
            $warga->name = $request->name;
            $warga->jenis_kelamin = $request->jenis_kelamin;
            $warga->tempat_lahir = $request->tempat_lahir;
            $warga->tanggal_lahir = $request->tanggal_lahir;
            $warga->alamat = $request->alamat;
            $warga->agama = $request->agama;
            if ($request->no_hp) {
                $warga->no_hp = $request->no_hp;
            }
            if ($request->email) {
                $warga->email = $request->email;
            }
            // Cek apakah file profile_picture di-upload
            // if ($request->hasFile('foto')) {
            //     $file = $request->file('foto');
            //     $path = $file->store(
            //         'foto',
            //         'public'
            //     ); // Simpan gambar ke direktori public
            //     $warga->foto = $path;
            // }

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = 'warga-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(
                    public_path('storage/warga'),
                    $filename
                );  // Simpan gambar ke folder public/storage/warga
                $warga->foto = "storage/warga/$filename";  // Simpan path gambar ke database
            } else {
                // Jika file tidak di-upload, gunakan gambar default
                if ($request->jenis_kelamin == "Laki-Laki") {
                    $warga->foto = 'default/male.jpg'; // Set path gambar default
                }
                if ($request->jenis_kelamin == "Perempuan") {
                    $warga->foto = 'default/female.jpg'; // Set path gambar default
                }
            }
            $warga->save();
            // Untuk menyimpan data pekerjaan sesui data warga
            $pekerjaan = new StatusPekerjaan();
            $pekerjaan->data_warga_id = $warga->id;
            $pekerjaan->status = $request->status_pekerjaan;
            $pekerjaan->pekerjaan = $request->pekerjaan;
            $pekerjaan->save();

            // Untuk menyimpan data pekerjaan sesui data warga

            $pernikahan = new StatusPernikahan();
            if ($request->status_pernikahan == "Belum Menikah") {
                $pasangan = Null;
            } else {
                $pasangan = $request->pasangan_id;
            }
            if ($request->jenis_kelamin == "Laki-Laki") {
                $pernikahan->warga_suami_id = $warga->id;
                $pernikahan->warga_istri_id = $pasangan;
            }
            if ($request->jenis_kelamin == "Perempuan") {
                $pernikahan->warga_suami_id = $pasangan;
                $pernikahan->warga_istri_id = $warga->id;
            }
            $pernikahan->status = $request->status_pernikahan;
            $pernikahan->tanggal = $request->tanggal;
            $pernikahan->save();

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil di simpan');
        } catch (\Exception $e) {
            // jika teradi error
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan data tidak di simpan' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = Crypt::decrypt($id);
        // Mengecek Hubungan pernikhan 
        $dataWarga = DataWarga::FindOrFail($id);
        if ($dataWarga->jenis_kelamin == "Laki-Laki") {
            $statusPernikahan = StatusPernikahan::where('warga_suami_id', $id)->get();
        }
        if ($dataWarga->jenis_kelamin == "Perempuan") {
            $statusPernikahan = StatusPernikahan::where('warga_istri_id', $id)->get();
        }

        $statusPekerjaan = StatusPekerjaan::where('data_warga_id', $id)->get(); //cek ststus pekerjaan sesuai dengan admiin
        $activityLogAdmin = ActivityLog::where('code', $dataWarga->code)->get(); //mengambil data aktiditas

        $role = Role::all(); //mengambil data role'role
        $cek_user = User::where('data_warga_id', $id)->where('email', $dataWarga->email)->count();
        $user = User::where('data_warga_id', $id)->first();

        $program = Program::all();

        return view('admin.master_data.data_warga.show', compact('dataWarga', 'statusPernikahan', 'statusPekerjaan', 'activityLogAdmin', 'role', 'cek_user', 'user', 'program'));
    }

    public function toggleAccess(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
        ]);

        $dataWargaId = $request->data_warga_id;
        $programId = $request->program_id;

        // Jika belum ada, buat entri baru
        $data = new AccessProgram();
        $data->data_warga_id = $dataWargaId;
        $data->program_id = $programId;
        $data->is_active = true; // default diatur aktif
        $data->save();

        return redirect()->back()->with('success', 'Status program berhasil diperbarui!');
    }

    public function toggleActive(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $data = AccessProgram::findOrFail($id);
        // Toggle status
        $data->is_active = $request->is_active;
        $data->save();

        return response()->json([
            'success' => true,
            'new_status' => $data->is_active // Kembalikan status baru
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = Crypt::decrypt($id);
        $dataWarga = DataWarga::Find($id);
        $warga = DataWarga::all();
        // cek data pernikahan
        if ($dataWarga->jenis_kelamin == "Laki-Laki") {
            $pernikahan = StatusPernikahan::where('warga_suami_id', $id)->get();
        }
        if ($dataWarga->jenis_kelamin == "Perempuan") {
            $pernikahan = StatusPernikahan::where('warga_istri_id', $id)->get();
        }
        $pekerjaan = StatusPekerjaan::where('data_warga_id', $id)->get();

        return view('admin.master_data.data_warga.edit', compact('warga', 'dataWarga', 'pekerjaan', 'pernikahan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'jenis_kelamin' => 'required',
            'tempat_lahir' => 'required',
            'alamat' => 'required',
            'agama' => 'required',
        ], [
            'name.required' => 'Nama Warga harus di isi',
            'jenis_kelamin.required' => 'Jenis Kelamin harus di isi',
            'tempat_lahir.required' => 'Tempat Lahir harus di isi',
            'alamat.required' => 'alamat harus di isi',
            'agama.required' => 'agama harus di isi',
        ]);

        $id = Crypt::decrypt($id);
        $warga = DataWarga::FindOrFail($id);
        $warga->name = $request->name;
        $warga->jenis_kelamin = $request->jenis_kelamin;
        $warga->tempat_lahir = $request->tempat_lahir;
        $warga->alamat = $request->alamat;
        $warga->agama = $request->agama;
        if ($request->tanggal_lahir) {
            $warga->tanggal_lahir = $request->tanggal_lahir;
        }
        if ($request->no_hp) {
            $warga->no_hp = $request->no_hp;
        }
        if ($request->email) {
            $warga->email = $request->email;
        }
        // Cek apakah file profile_picture di-upload
        // if ($request->hasFile('foto')) {
        //     $file = $request->file('foto');
        //     $path = $file->store(
        //         'foto',
        //         'public'
        //     ); // Simpan gambar ke direktori public
        //     $warga->foto = $path;
        // }
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'warga-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(
                public_path('storage/warga'),
                $filename
            );  // Simpan gambar ke folder public/storage/warga
            $warga->foto = "storage/warga/$filename";  // Simpan path gambar ke database
        }
        $warga->update();

        return redirect()->back()->with('success', ' Data sudah berhasil di edit');
    }

    public function updatePernikahan(Request $request)
    {
        $request->validate([
            'status_pernikahan' => 'required',
        ], [
            'status_pernikahan.required' => ' Status Pernikahan harus di isi',
        ]);

        // Cek status pernikahan terbaru di database berdasarkan warga_id
        $statusTerbaru = StatusPernikahan::where('warga_suami_id', $request->warga_id)
            ->orWhere('warga_istri_id', $request->warga_id)
            ->latest('created_at')
            ->first();

        // Jika status terbaru adalah "Menikah" dan user memilih "Menikah" atau "Belum Menikah", batal save
        if ($statusTerbaru && $statusTerbaru->status === "Menikah" && in_array($request->status_pernikahan, ["Menikah", "Belum Menikah"])) {
            return redirect()->back()->with('error', 'Tidak dapat mengubah status pernikahan karena status terakhir adalah Menikah');
        }

        $pernikahan = new StatusPernikahan();
        if ($request->status_pernikahan == "Belum Menikah") {
            $pasangan = $request->warga_id;
        } else {
            $pasangan = $request->pasangan_id;
        }
        if ($request->jenis_kelamin == "Laki-Laki") {
            $pernikahan->warga_suami_id = $request->warga_id;
            $pernikahan->warga_istri_id = $pasangan;
        }
        if ($request->jenis_kelamin == "Perempuan") {
            $pernikahan->warga_suami_id = $pasangan;
            $pernikahan->warga_istri_id = $request->warga_id;
        }
        $pernikahan->status = $request->status_pernikahan;
        $pernikahan->tanggal = $request->tanggal;
        $pernikahan->save();

        return redirect()->back()->with('success', 'Data pernikahan berhasil di simpan');
    }

    public function updatePekerjaan(Request $request)
    {
        $request->validate([
            'status_pekerjaan' => 'required',
        ], [
            'status_pekerjaan.required' => ' Status pekerjaan harus di isi',
        ]);

        // Untuk menyimpan data pekerjaan sesui data warga
        $pekerjaan = new StatusPekerjaan();
        $pekerjaan->data_warga_id = $request->warga_id;
        $pekerjaan->status = $request->status_pekerjaan;
        $pekerjaan->pekerjaan = $request->pekerjaan;
        $pekerjaan->save();

        return redirect()->back()->with('success', 'Data pekerjaan berhasil di simpan');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    { {
            $id = Crypt::decrypt($id);
            $data = DataWarga::findOrFail($id);
            $data->delete();

            return redirect()->back()->with('success', 'Data Menu sudah di hapus');
        }
    }

    // Membuat account di kirim ke user
    public function account_store(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|unique:users,no_hp|min:10|max:14',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required',
        ], [
            'no_hp.required' => 'No Harus di isi ',
            'email.required' => 'email Harus di isi ',
            'email.unique' => 'email sudah terdaftar',
            'role_id.required' => 'Role Harus di isi ',
        ]);

        // Temukan role berdasarkan `role_id` dari request
        $role = Role::find($request->role_id);

        // Periksa apakah role ditemukan
        if (!$role) {
            return redirect()->back()->withErrors(['role_id' => 'Role tidak ditemukan.']);
        }
        // Daftar role yang hanya boleh diisi satu data user
        $restrictedRoles = ['Ketua', 'Bendahara', 'Sekretaris'];

        // Periksa apakah role name termasuk dalam restrictedRoles
        if (in_array($role->name, $restrictedRoles)) {
            // Cek apakah role ini sudah terpakai oleh user
            $existingUser = User::where('role_id', $role->id)->first();
            if ($existingUser) {
                // Jika role sudah terpakai, tampilkan error
                return redirect()->back()->withErrors(['role_id' => 'Role ' . $role->name . ' sudah diisi oleh user lain.']);
            }
        }


        //transction, ada dua model dan jika di dalam try ini sukses semua data tersimpan dan jika ada yang error salah satu tidak tersimpan
        DB::beginTransaction();

        try {
            $account = DataWarga::Find($request->warga_id);
            $account->email = $request->email;
            $account->no_hp = $request->no_hp;

            $account->update();

            $data = new User();
            $data->name = $request->name;
            $data->email = $request->email;
            $data->no_hp = $request->no_hp;
            $data->role_id = $request->role_id;
            $data->password = Hash::make('Keluarga123');
            $data->data_warga_id = $request->warga_id;
            $data->is_active = 1;
            $data->profile_photo_path = $request->foto;

            $data->save();

            DB::commit();

            $phoneNumber = $request->no_hp;

            // Contoh pesan notifikasi untuk pendaftaran berhasil
            $message = "*Pendaftaran Berhasil* \n";
            $message .= "Selamat, pendaftaran Anda telah berhasil!\n\n";
            $message .= "Berikut adalah detail akun Anda:\n";
            $message .= "- *Email*: {$request->email}\n";
            $message .= "- *Kata Sandi*: Keluarga123 \n"; // Pastikan variabel password dienkripsi atau diacak jika aman
            $message .= "- *Link Login*: " . url('https://kas.keluargamahaya.com/login') . "\n\n";
            $message .= "*Harap simpan informasi ini dengan baik.*\n";
            $message .= "Terima kasih telah mendaftar di platform kami!\n\n";
            $message .= "*Salam,* \n";
            $message .= "*Terima Kami*";


            // URL gambar dari direktori storage
            $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');


            $response = $this->fonnteService->sendWhatsAppMessage($phoneNumber, $message, $imageUrl);

            if (isset($response['status']) && $response['status'] == 'success') {
                return back()->with('success', 'Data tersimpan, Notifikasi berhasil dikirim!');
            }

            return back()->with('success', 'Gagal mengirim notifikasi');
        } catch (\Exception $e) {
            // jika teradi error
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan data tidak di simpan' . $e->getMessage());
        }
    }



    public function getPasangan(Request $request)
    {
        $jenisKelamin = $request->input('jenis_kelamin');

        if ($jenisKelamin == 'Laki-Laki') {
            // Ambil data dari kolom warga_istri_id yang statusnya Belum Menikah, Cerai Hidup, atau Cerai Mati
            $pasangan = StatusPernikahan::whereIn('status', ['Belum Menikah', 'Cerai Hidup', 'Cerai Mati'])
                ->whereNotNull('warga_istri_id')
                ->with(['istri' => function ($query) {
                    $query->where('jenis_kelamin', 'Perempuan');
                }])
                ->orderBy('created_at', 'desc') // Urutkan berdasarkan data terbaru
                ->get()
                ->unique('warga_istri_id') // Hanya ambil data terbaru untuk setiap warga_istri_id
                ->filter(function ($item) {
                    return $item->istri !== null; // Hanya ambil data dengan pasangan yang valid
                })
                ->map(function ($item) {
                    return [
                        'id' => $item->istri->id,
                        'name' => $item->istri->name,
                        'status' => $item->status // Menambahkan status
                    ];
                });
        } elseif ($jenisKelamin == 'Perempuan') {
            // Ambil data dari kolom warga_suami_id yang statusnya Belum Menikah, Cerai Hidup, atau Cerai Mati
            $pasangan = StatusPernikahan::whereIn('status', ['Belum Menikah', 'Cerai Hidup', 'Cerai Mati'])
                ->whereNotNull('warga_suami_id')
                ->with(['suami' => function ($query) {
                    $query->where('jenis_kelamin', 'Laki-Laki');
                }])
                ->orderBy('created_at', 'desc') // Urutkan berdasarkan data terbaru
                ->get()
                ->unique('warga_suami_id') // Hanya ambil data terbaru untuk setiap warga_suami_id
                ->filter(function ($item) {
                    return $item->suami !== null; // Hanya ambil data dengan pasangan yang valid
                })
                ->map(function ($item) {
                    return [
                        'id' => $item->suami->id,
                        'name' => $item->suami->name,
                        'status' => $item->status // Menambahkan status
                    ];
                });
        } else {
            $pasangan = collect(); // Jika jenis kelamin tidak sesuai, kosongkan hasil
        }

        return response()->json($pasangan);
    }


    public function getPasangans(Request $request)
    {
        $jenisKelamin = $request->input('jenis_kelamin');

        if ($jenisKelamin == 'Laki-Laki') {
            // Ambil data dari kolom warga_istri_id yang statusnya Belum Menikah, Cerai Hidup, atau Cerai Mati
            $pasangan = StatusPernikahan::whereIn('status', ['Belum Menikah', 'Cerai Hidup', 'Cerai Mati'])
                ->whereNotNull('warga_istri_id')
                ->with(['istri' => function ($query) {
                    $query->where('jenis_kelamin', 'Perempuan');
                }])
                ->get()
                ->filter(function ($item) {
                    return $item->istri !== null; // Hanya ambil data dengan pasangan yang valid
                })
                ->map(function ($item) {
                    return [
                        'id' => $item->istri->id,
                        'name' => $item->istri->name,
                        'status' => $item->status // Menambahkan status
                    ];
                });
        } elseif ($jenisKelamin == 'Perempuan') {
            // Ambil data dari kolom warga_suami_id yang statusnya Belum Menikah, Cerai Hidup, atau Cerai Mati
            $pasangan = StatusPernikahan::whereIn('status', ['Belum Menikah', 'Cerai Hidup', 'Cerai Mati'])
                ->whereNotNull('warga_suami_id')
                ->with(['suami' => function ($query) {
                    $query->where('jenis_kelamin', 'Laki-Laki');
                }])
                ->get()
                ->filter(function ($item) {
                    return $item->suami !== null; // Hanya ambil data dengan pasangan yang valid
                })
                ->map(function ($item) {
                    return [
                        'id' => $item->suami->id,
                        'name' => $item->suami->name,
                        'status' => $item->status // Menambahkan status
                    ];
                });
        } else {
            $pasangan = collect(); // Jika jenis kelamin tidak sesuai, kosongkan hasil
        }

        return response()->json($pasangan);
    }
}

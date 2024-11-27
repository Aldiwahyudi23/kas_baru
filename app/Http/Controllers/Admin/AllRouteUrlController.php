<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AllRouteUrl;
use App\Models\Menu;
use App\Models\SubMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;

class AllRouteUrlController extends Controller
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

        // Ambil semua daftar route yang terdaftar di aplikasi
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return $route->getName(); // Ambil nama route
        })->filter(); // Hanya ambil route yang memiliki nama

        $AllRouteUrl = AllRouteUrl::all();

        return view('admin.master_data.data_route_url.index', compact('AllRouteUrl', 'routes'));
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
        // Validasi input form
        $request->validate([
            'name' => 'required|string|max:255',
            'route_name' => 'required|string|max:255',
            'description' => 'required|string',
        ], [
            'name.required' => "Nama harus di isi",
            'route_name.required' => "Nama Route harus di isi",
            'description.required' => "Deskripsi harus di isi",
        ]);

        // Mengambil waktu saat ini
        $dateTime = now();
        // Format tanggal dan waktu
        $formattedDate = $dateTime->format('dmy'); // Dapatkan format DDMMYY
        $formattedTime = $dateTime->format('His'); // Dapatkan format HHMMSS
        // Menghitung jumlah admin saat ini dan menambahkan 1 untuk urutan
        $adminCount = AllRouteUrl::count() + 1;
        // Membuat kode admin
        $code = 'RU-' . $formattedDate . $formattedTime . str_pad($adminCount, 1, '0', STR_PAD_LEFT);
        // Format akhir: ADM-DDMMYYHHMMSS1

        // Membuat instance Admin baru
        $data = new AllRouteUrl();
        $data->code = $code;
        $data->name = $request->name;
        $data->route_name = $request->route_name;
        $data->description = $request->description;

        $data->save();

        return redirect()->back()->with('success', 'Data Route sudah berhasil di simpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = Crypt::decrypt($id);

        $DataRouteUrl = AllRouteUrl::FindOrFail($id);
        $menu = Menu::where('route_url_id', $id)->get();
        $subMenu = SubMenu::where('route_url_id', $id)->get();

        $activityLogAdmin = ActivityLog::where('code', $DataRouteUrl->code)->get();

        return view('admin.master_data.data_route_url.show', compact('DataRouteUrl', 'activityLogAdmin', 'menu', 'subMenu'));
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

        // Ambil semua daftar route yang terdaftar di aplikasi
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return $route->getName(); // Ambil nama route
        })->filter(); // Hanya ambil route yang memiliki nama

        $id = Crypt::decrypt($id);

        $DataRouteUrl = AllRouteUrl::FindOrFail($id);
        $AllRouteUrl = AllRouteUrl::all();

        return view('admin.master_data.data_route_url.edit', compact('AllRouteUrl', 'DataRouteUrl', 'routes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);
        // Validasi input form
        $request->validate([
            'name' => 'required|string|max:255',
            'route_name' => 'required|string|max:255',
            'description' => 'required|string',
        ], [
            'name.required' => "Nama harus di isi",
            'route_name.required' => "Nama Route harus di isi",
            'description.required' => "Deskripsi harus di isi",
        ]);

        $data = AllRouteUrl::FindOrFail($id);
        $data->name = $request->name;
        $data->route_name = $request->route_name;
        $data->description = $request->description;

        $data->update();
        return redirect()->back()->with('success', 'Data Sudah berhasil di Update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = AllRouteUrl::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Data Route sudah di hapus');
    }
}

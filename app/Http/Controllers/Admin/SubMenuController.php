<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AllRouteUrl;
use App\Models\Menu;
use App\Models\SubMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class SubMenuController extends Controller
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

        $subMenu = SubMenu::all();
        $menu = Menu::all();
        $AllRouteUrl = AllRouteUrl::all();

        return view('admin.master_data.data_sub_menu.index', compact('subMenu', 'menu', 'AllRouteUrl'));
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
            'menu_id' => 'required|string',
            'name' => 'required|string|max:255',
            'icon' => 'required|string',
            'color' => 'required|string',
            'route_url_id' => 'required|string',
            'description' => 'required|string',
            'is_active' => 'required|in:0,1',
        ], [
            'menu_id.required' => "Menu harus di isi",
            'name.required' => "Nama harus di isi",
            'icon.required' => "icon harus di isi",
            'color.required' => "Warna harus di isi",
            'route_url_id.required' => "Id harus di isi",
            'description.required' => "Deskripsi harus di isi",
            'is_active.required' => "Status harus di isi",
        ]);

        // Mengambil waktu saat ini
        $dateTime = now();
        // Format tanggal dan waktu
        $formattedDate = $dateTime->format('dmy'); // Dapatkan format DDMMYY
        $formattedTime = $dateTime->format('His'); // Dapatkan format HHMMSS
        // Menghitung jumlah admin saat ini dan menambahkan 1 untuk urutan
        $subMenuAcount = SubMenu::count() + 1;
        // Membuat kode admin
        $code = 'SM-' . $formattedDate . $formattedTime . str_pad($subMenuAcount, 1, '0', STR_PAD_LEFT);
        // Format akhir: ADM-DDMMYYHHMMSS1

        // Membuat instance Admin baru
        $data = new SubMenu();
        $data->code = $code;
        $data->menu_id = $request->menu_id;
        $data->name = $request->name;
        $data->icon = $request->icon;
        $data->color = $request->color;
        $data->route_url_id = $request->route_url_id;
        $data->description = $request->description;
        $data->is_active = $request->is_active;

        $data->save();

        return redirect()->back()->with('success', 'Data Sub Menu sudah berhasil di simpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = Crypt::decrypt($id);

        $DataSubMenu = SubMenu::FindOrFail($id);
        $activityLogAdmin = ActivityLog::where('code', $DataSubMenu->code)->get();

        return view('admin.master_data.data_sub_menu.show', compact('DataSubMenu', 'activityLogAdmin'));
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

        $DataSubMenu = SubMenu::FindOrFail($id);
        $subMenu = SubMenu::all();
        $menu = Menu::all();
        $AllRouteUrl = AllRouteUrl::all();

        return view('admin.master_data.data_sub_menu.edit', compact('subMenu', 'menu', 'DataSubMenu', 'AllRouteUrl'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);
        // Validasi input form
        $request->validate([
            'menu_id' => 'required|string',
            'name' => 'required|string|max:255',
            'icon' => 'required|string',
            'color' => 'required|string',
            'route_url_id' => 'required|string',
            'description' => 'required|string',
        ], [
            'menu_id.required' => "Menu harus di isi",
            'name.required' => "Nama harus di isi",
            'icon.required' => "icon harus di isi",
            'color.required' => "Warna harus di isi",
            'route_url_id.required' => "Id harus di isi",
            'description.required' => "Deskripsi harus di isi",
        ]);

        // Membuat instance Admin baru
        $data = SubMenu::FindOrFail($id);
        $data->menu_id = $request->menu_id;
        $data->name = $request->name;
        $data->icon = $request->icon;
        $data->color = $request->color;
        $data->route_url_id = $request->route_url_id;
        $data->description = $request->description;

        $data->update();

        return redirect()->back()->with('success', 'Data Sub Menu sudah berhasil di rubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = Crypt::decrypt($id);
        $data = SubMenu::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Data Sub Menu sudah di hapus');
    }

    public function toggleActive(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $data = SubMenu::findOrFail($id);
        // Toggle status
        $data->is_active = $request->is_active;
        $data->save();

        return response()->json([
            'success' => true,
            'new_status' => $data->is_active // Kembalikan status baru
        ]);
    }
}

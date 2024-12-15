<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessNotification;
use App\Models\DataNotification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AccessNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = AccessNotification::all();
        $roles = Role::whereNotIn('name', ['Anggota', 'Warga'])->get();
        $DataNotification = DataNotification::all();

        return view('admin.notification.index', compact('roles', 'DataNotification'));
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
            'type' => 'required',
            'name' => 'required',
            'keterangan' => 'required',
        ]);

        $data = new DataNotification();
        $data->type = $request->type;
        $data->name = $request->name;
        $data->keterangan = $request->keterangan;
        $data->wa_notification = $request->has('wa_notification');
        $data->email_notification = $request->has('email_notification');
        $data->anggota = $request->has('anggota');
        $data->pengurus = $request->has('pengurus');
        $data->program = $request->has('program');
        $data->save();

        if ($request->pengurus) {
            // Cek role dari checkbox
            $role = Role::find($request->role_id);

            if (!$role) {
                return;
            }
            // Cari user dengan role yang dipilih
            $users = User::where('role_id', $role->id)->get();

            foreach ($users as $user) {
                $access = AccessNotification::where('notification_id', $data->id)
                    ->where('data_warga_id', $user->data_warga_id)
                    ->first();

                if ($access) {
                    // Jika data sudah ada, toggle is_active
                    $access->is_active = !$access->is_active;
                    $access->save();
                } else {
                    // Jika data belum ada, tambahkan
                    AccessNotification::create([
                        'notification_id' => $data->id,
                        'data_warga_id' => $user->data_warga_id,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Data berhasil di tambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $data = DataNotification::find($id);
        return view('admin.notification.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccessNotification $accessNotification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccessNotification $accessNotification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccessNotification $accessNotification)
    {
        //
    }
}

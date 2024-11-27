<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayoutsForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class LayoutsFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $layoutForm = LayoutsForm::first();

        return view('admin.program.layoutForm', compact('layoutForm'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = Crypt::decrypt($id);

        $data = LayoutsForm::findOrFail($id);
        // Cek apakah file profile_picture di-upload
        if ($request->hasFile('icon_kas')) {
            $file = $request->file('icon_kas');
            $path = $file->store(
                'layoutForm',
                'public'
            ); // Simpan gambar ke direktori public
            $data->icon_kas = $path;
        }
        if ($request->hasFile('icon_tabungan')) {
            $file = $request->file('icon_tabungan');
            $path = $file->store(
                'layoutForm',
                'public'
            ); // Simpan gambar ke direktori public
            $data->icon_tabungan = $path;
        }
        if ($request->hasFile('icon_b_pinjam')) {
            $file = $request->file('icon_b_pinjam');
            $path = $file->store(
                'layoutForm',
                'public'
            ); // Simpan gambar ke direktori public
            $data->icon_b_pinjam = $path;
        }
        if ($request->hasFile('pinjam_pinjam')) {
            $file = $request->file('pinjam_pinjam');
            $path = $file->store(
                'layoutForm',
                'public'
            ); // Simpan gambar ke direktori public
            $data->pinjam_pinjam = $path;
        }

        $data->kas_proses = $request->kas_proses;
        $data->tabungan_proses = $request->tabungan_proses;
        $data->b_pinjam_proses = $request->b_pinjam_proses;
        $data->pinjam_proses = $request->pinjam_proses;
        $data->pinjam_saldo = $request->pinjam_saldo;
        $data->pinjam_penuh = $request->pinjam_penuh;
        $data->pinjam_nunggak = $request->pinjam_nunggak;

        $data->update();

        return redirect()->back()->with('success', 'Berhasil di rubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

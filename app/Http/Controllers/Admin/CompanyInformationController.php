<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\CompanyInformation;
use Illuminate\Http\Request;

class CompanyInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companyInfo = CompanyInformation::first(); // Ambil data informasi perusahaan
        return view('admin.company_info.index', compact('companyInfo'));
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
    public function show(CompanyInformation $companyInformation)
    {
        $companyInfo = CompanyInformation::first(); // Ambil data informasi perusahaan
        return view('admin.company_info', compact('companyInfo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompanyInformation $companyInformation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompanyInformation $companyInformation)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'logo' => 'nullable|image|max:2048', // Validasi untuk logo
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $companyInfo = CompanyInformation::first();
        if (!$companyInfo) {
            $companyInfo = new CompanyInformation();
        }

        $companyInfo->company_name = $request->company_name;
        $companyInfo->description = $request->description;
        $companyInfo->vision = $request->vision;
        $companyInfo->mission = $request->mission;
        $companyInfo->address = $request->address;
        $companyInfo->phone_number = $request->phone_number;
        $companyInfo->email = $request->email;

        // Meng-upload dan menyimpan logo jika ada file yang diupload
        // if ($request->hasFile('logo')) {
        //     $logoPath = $request->file('logo')->store('logos', 'public');
        //     $companyInfo->logo = $logoPath;
        // }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/company'), $filename);  // Simpan gambar ke folder public/storage/company
            $companyInfo->logo = "storage/company/$filename";  // Simpan path gambar ke database
        }


        $companyInfo->save();

        return redirect()->back()->with('success', 'Informasi perusahaan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompanyInformation $companyInformation)
    {
        //
    }
}

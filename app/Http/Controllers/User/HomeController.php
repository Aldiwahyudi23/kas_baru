<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AnggaranSaldo;
use App\Models\Saldo;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $saldo = Saldo::latest()->first();
        $saldo_kas = AnggaranSaldo::where('type', 'Dana Kas')->latest()->first();
        $saldo_amal = AnggaranSaldo::where('type', 'Dana Amal')->latest()->first();
        $saldo_pinjam = AnggaranSaldo::where('type', 'Dana Pinjam')->latest()->first();
        $saldo_darurat = AnggaranSaldo::where('type', 'Dana Darurat')->latest()->first();
        return view('user.dashboard.index', compact('saldo', 'saldo_kas', 'saldo_amal', 'saldo_pinjam', 'saldo_darurat'));
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
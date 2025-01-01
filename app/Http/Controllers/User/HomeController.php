<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AnggaranSaldo;
use App\Models\Konter\TransaksiKonter;
use App\Models\Loan;
use App\Models\Member;
use App\Models\MemberType;
use App\Models\Saldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Untuk Tgaihan aktif
        $pinjaman = Loan::where('submitted_by', Auth::user()->data_warga_id)->whereIn('status', ['Acknowledged', 'In Repayment'])->get();
        $sisa = $pinjaman->sum('remaining_balance') ?? 0;
        $konter = TransaksiKonter::where('status', 'Berhasil')->where('submitted_by', Auth::user()->dataWarga->name)->get();
        $data_konter = $konter->sum('invoice') ?? 0;
        $total = $sisa +  $data_konter;

        //menu member
        $user = Auth::user();
        $memberType = MemberType::where('name', 'Konter')->first();
        $isMemberKonter = Member::where('member_type_id', $memberType->id)->where('user_id', Auth::user()->id)->where('is_active', true)->exists();
        $isPengurus = in_array($user->role->name, ['Ketua', 'Bendahara', 'Sekretaris', 'Wakil Ketua', 'Wakil Bendahara', 'Wakil Sekretaris']);

        return view('user.dashboard.index', compact('saldo', 'saldo_kas', 'saldo_amal', 'saldo_pinjam', 'saldo_darurat', 'total', 'konter', 'pinjaman', 'isMemberKonter', 'isPengurus'));
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
    public function saldo()
    {
        // Fetch all Saldo records ordered by created_at descending
        $saldoData = Saldo::orderBy('created_at', 'desc')->get();

        return view('user.dashboard.saldo.utama', compact('saldoData'));
    }
    public function saldo_anggaran($type)
    {
        // Ambil data berdasarkan type
        $saldoAnggaran = AnggaranSaldo::where('type', $type)->orderBy('created_at', 'desc')->get();

        // Kirim data ke view
        return view('user.dashboard.saldo.anggaran', compact('saldoAnggaran', 'type'));
    }
}
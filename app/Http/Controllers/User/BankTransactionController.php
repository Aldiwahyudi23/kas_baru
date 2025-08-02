<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(BankTransaction $bankTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankTransaction $bankTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankTransaction $bankTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankTransaction $bankTransaction)
    {
        //
    }

    public function showTransferForm($bankAccountId)
{
    // Dapatkan rekening sumber
    $sourceAccount = BankAccount::with(['warga', 'latestBalance'])
        ->findOrFail($bankAccountId);
    
    // Dapatkan semua rekening aktif kecuali yang sedang dipilih
    $destinationAccounts = BankAccount::where('id', '!=', $bankAccountId)
        ->where('is_active', true)
        ->get();
    
    return view('user.dashboard.saldo.bank.transfer', compact('sourceAccount', 'destinationAccounts'));
}

public function processTransfer(Request $request)
{
    $request->validate([
        'source_account_id' => 'required|exists:bank_accounts,id',
        'destination_account_id' => 'required|exists:bank_accounts,id|different:source_account_id',
        'amount' => 'required|numeric|min:1000',
        'admin_fee' => 'nullable|numeric|min:0',
        'description' => 'required|string|max:255'
    ]);

    try {
        DB::beginTransaction();

        // 1. Proses pengurangan dari rekening sumber
        $lastSourceBalance = BankTransaction::where('bank_account_id', $request->source_account_id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $sourceCurrentBalance = $lastSourceBalance ? $lastSourceBalance->balance : 0;
        $totalDebit = $request->amount + ($request->admin_fee ?? 0);
        
        if ($sourceCurrentBalance < $totalDebit) {
            throw new \Exception('Saldo tidak mencukupi untuk transfer ini');
        }

        $sourceTransaction = BankTransaction::create([
            'bank_account_id' => $request->source_account_id,
            'saldo_id' => null,
            'amount' => -$totalDebit,
            'balance' => $sourceCurrentBalance - $totalDebit,
            'description' => $request->description . "Admin Fee: " . ($request->admin_fee ?? 0),
        ]);

        // 2. Proses penambahan ke rekening tujuan
        $lastDestBalance = BankTransaction::where('bank_account_id', $request->destination_account_id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $destCurrentBalance = $lastDestBalance ? $lastDestBalance->balance : 0;

        $destTransaction = BankTransaction::create([
            'bank_account_id' => $request->destination_account_id,
             'saldo_id' => null,
            'amount' => $request->amount,
            'balance' => $destCurrentBalance + $request->amount,
            'description' => $request->description,
        ]);

        // 3. Link kedua transaksi
      
        $sourceTransaction->save();
        $destTransaction->save();

        DB::commit();

        return redirect()->back()
            ->with('success', 'Transfer dana berhasil diproses');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()
            ->with('error', 'Gagal memproses transfer: ' . $e->getMessage());
    }
}
}

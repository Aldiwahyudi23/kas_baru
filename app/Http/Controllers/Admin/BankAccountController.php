<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\BankAccount;
use App\Models\DataWarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $bankAccount = BankAccount::with('warga')
            ->latest()
            ->paginate(10);
            $wargas = DataWarga::all();
            
        return view('admin.master_data.data_bank.index', compact('bankAccount','wargas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warga_id' => 'required|exists:data_wargas,id',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50|unique:bank_accounts',
            'account_holder_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        BankAccount::create([
            'warga_id' => $request->warga_id,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder_name' => $request->account_holder_name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Rekening bank berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount)
    {
        return view('admin.master_data.data_bank.show', compact('bankAccount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        $bankAccount = BankAccount::with('warga')
            ->latest()
            ->paginate(10);
          $wargas = DataWarga::all();
        return view('admin.master_data.data_bank.edit', compact('bankAccount', 'wargas','bankAccount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
         $validator = Validator::make($request->all(), [
            'warga_id' => 'required|exists:data_wargas,id',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50|unique:bank_accounts,account_number,' . $bankAccount->id,
            'account_holder_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $bankAccount->update([
            'warga_id' => $request->warga_id,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder_name' => $request->account_holder_name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Data rekening bank berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Rekening bank berhasil dihapus');
    }

    /**
     * Restore the specified soft deleted resource.
     */
    public function restore($id)
    {
        $bankAccount = BankAccount::withTrashed()->findOrFail($id);
        $bankAccount->restore();

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Rekening bank berhasil dipulihkan');
    }

    /**
     * Permanently delete the specified resource.
     */
    public function forceDelete($id)
    {
        $bankAccount = BankAccount::withTrashed()->findOrFail($id);
        $bankAccount->forceDelete();

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Rekening bank berhasil dihapus permanen');
    }

    public function toggleStatus(Request $request, $id)
    {
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->update(['is_active' => $request->is_active]);
        
        return response()->json(['success' => true]);
    }
}

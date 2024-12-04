<?php

namespace App\Http\Controllers\User\Konter;

use App\Http\Controllers\Controller;
use App\Models\Konter\ProductKonter;
use App\Models\Konter\TransaksiKonter;
use Illuminate\Http\Request;

class TransaksiKonterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaksi = TransaksiKonter::all();
        $transaksi_pending = TransaksiKonter::where('status', 'pending');
        return view('admin.konter.transaksi.index', compact('transaksi', 'transaksi_pending'));
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
            'product_id' => 'required|exists:product_konters,id',
            'submitted_by' => 'required|string',
            'payment_method' => 'required|in:transfer,cash',
            'status' => 'required|in:pending,Berhasil,Gagal',
            'buying_price' => 'required|numeric',
            'price' => 'required|numeric',
            'is_deposited' => 'nullable|boolean',
            'deposit_id' => 'nullable|exists:deposits,id',
            'deadline_date' => 'nullable|date'
        ]);

        $data = new TransaksiKonter();
        $data->product_id = $request->product_id;
        $data->submitted_by = $request->submitted_by;
        $data->buying_price = $request->buying_price;
        $data->price = $request->price;
        $data->is_deposited = $request->is_deposited;
        $data->deadline_date = $request->deadline_date;
        $data->status = 'pending';

        return redirect()->back()->with('success', 'Data berhasil di simpan');
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

    public function checkPhone(Request $request)
    {
        $no_hp = $request->get('no_hp');

        $prefixes = [
            'AXIS' => ['0831', '0832', '0833', '0838'],
            'simPATI' => ['0811', '0812', '0813', '0821', '0822', '0852', '0853', '0823'],
            'Im3' => ['0814', '0815', '0816', '0855', '0856', '0857', '0858'],
            'XL' => ['0817', '0818', '0819', '0859', '0877', '0878'],
            '3' => ['0895', '0896', '0897', '0898', '0899'],
            'Smartfren' => ['0881', '0882', '0883', '0884', '0885', '0886', '0887', '0888', '0889']
        ];

        // Default layanan tidak ditemukan
        $layanan = null;

        // Cari layanan berdasarkan prefix
        foreach ($prefixes as $key => $values) {
            foreach ($values as $prefix) {
                if (strpos($no_hp, $prefix) === 0) {
                    $layanan = $key;
                    break 2;
                }
            }
        }

        if ($layanan) {
            // Query produk berdasarkan kategori dan provider
            $products = ProductKonter::where('kategori_id', '1')
                ->where('provider_id', $layanan)
                ->get(['id', 'provider', 'amount']);

            return response()->json([
                'success' => true,
                'layanan' => $layanan,
                'products' => $products,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Provider tidak ditemukan untuk nomor HP ini.'
        ]);
    }
}

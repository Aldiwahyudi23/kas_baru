<?php

namespace App\Http\Controllers\Admin\Konter;

use App\Http\Controllers\Controller;
use App\Models\Konter\ProductKonter;
use App\Models\Konter\TransaksiKonter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TransaksiKonterController extends Controller
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

        $transaksi = TransaksiKonter::with('product')->get();
        $products = ProductKonter::all();
        $transaksi_pending = TransaksiKonter::with('product')->where('status', 'pending');
        return view('admin.konter.transaksi.index', compact('transaksi', 'transaksi_pending', 'products'));
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
        $validated = $request->validate([
            'code' => 'required|unique:transaksi_konters',
            'product_id' => 'nullable|exists:product_konters,id',
            'submitted_by' => 'required|string',
            'payment_method' => 'required|in:transfer,cash',
            'status' => 'required|in:pending,Berhasil,Gagal',
            'buying_price' => 'required|numeric',
            'price' => 'required|numeric',
            'is_deposited' => 'required|boolean',
            'deadline_date' => 'nullable|date',
        ]);

        TransaksiKonter::create($validated);
        return redirect()->back()->with('success', 'Transaksi berhasil dibuat.');
    }

    // Show
    public function show(TransaksiKonter $transaksiKonter)
    {
        return view('admin.konter.transaksi.show', compact('transaksiKonter'));
    }

    // Edit
    public function edit(TransaksiKonter $transaksiKonter, $id)
    {

        // Untuk Konfirmasi delet
        $title = 'Delete !';
        $text = "Apakah benar anda mau hapus data ini?";
        confirmDelete($title, $text);

        $id = Crypt::decrypt($id);
        $transaksi = TransaksiKonter::all();
        $data_transaksi = TransaksiKonter::find($id);
        $products = ProductKonter::all();
        return view('admin.konter.transaksi.edit', compact('transaksiKonter', 'transaksi', 'data_transaksi', 'products'));
    }

    // Update
    public function update(Request $request, TransaksiKonter $transaksiKonter, $id)
    {
        $id = Crypt::decrypt($id);
        $validated = $request->validate([
            'code' => 'required|unique:transaksi_konters,code,' . $id,
            'product_id' => 'nullable|exists:product_konters,id',
            'submitted_by' => 'required|string',
            'payment_method' => 'required|in:transfer,cash',
            'status' => 'required|in:pending,Berhasil,Gagal',
            'buying_price' => 'required|numeric',
            'price' => 'required|numeric',
            'is_deposited' => 'required|boolean',
            'deadline_date' => 'nullable|date',
        ]);

        $transaksiKonter = TransaksiKonter::find($id);
        $transaksiKonter->update($validated);
        return redirect()->back()->with('success', 'Transaksi berhasil diperbarui.');
    }

    // Delete
    public function destroy(TransaksiKonter $transaksiKonter, $id)
    {
        $id = Crypt::decrypt($id);
        $data = TransaksiKonter::findOrFail($id);
        $data->delete();
        return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
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
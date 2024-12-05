<?php

namespace App\Http\Controllers\User\Konter;

use App\Http\Controllers\Controller;
use App\Models\DataWarga;
use App\Models\Konter\DetailTransaksiKonter;
use App\Models\Konter\ProductKonter;
use App\Models\Konter\ProviderKonter;
use App\Models\Konter\TransaksiKonter;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Fragment\FragmentUriGenerator;

class KonterController extends Controller
{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }
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


    public function pulsa()
    {
        $products = ProductKonter::where('provider_id', 1)
            ->where('kategori_id', 1)
            ->get(['id', 'amount']);
        return view('user.konter.pulsa', compact('products'));
    }

    public function token_listrik()
    {
        return view('user.konter.token_listrik');
    }
    public function tagihan_listrik()
    {
        return view('user.konter.tagihan_listrik');
    }


    protected $prefixes = [
        'AXIS' => ['0831', '0832', '0833', '0838'],
        'simPATI' => ['0811', '0812', '0813', '0821', '0822', '0852', '0853', '0823'],
        'Im3' => ['0814', '0815', '0816', '0855', '0856', '0857', '0858'],
        'XL' => ['0817', '0818', '0819', '0859', '0877', '0878'],
        '3' => ['0895', '0896', '0897', '0898', '0899'],
        'Smartfren' => ['0881', '0882', '0883', '0884', '0885', '0886', '0887', '0888', '0889']
    ];

    public function detectProvider(Request $request)
    {
        $phoneNumber = $request->input('phoneNumber');
        $provider = null;

        // Cari provider berdasarkan prefix
        foreach ($this->prefixes as $name => $prefixList) {
            foreach ($prefixList as $prefix) {
                if (str_starts_with($phoneNumber, $prefix)) {
                    $provider = $name;
                    break 2;
                }
            }
        }

        if (!$provider) {
            return response()->json(['error' => 'Provider tidak ditemukan'], 404);
        }

        // Cari provider_id dari tabel ProviderKonter
        $providerRecord = ProviderKonter::where('name', $provider)->first();

        if (!$providerRecord) {
            return response()->json(['error' => 'Provider tidak ditemukan di database'], 404);
        }

        // Cari produk berdasarkan provider_id dan kategori_id = 1
        $products = ProductKonter::where('provider_id', $providerRecord->id)
            ->where('kategori_id', 1)
            ->get(['id', 'amount', 'price']);

        return response()->json([
            'provider' => $provider,
            'products' => $products
        ]);
    }

    public function transaksi_umum($encryptedProductId, $phoneNumber)
    {

        $product = ProductKonter::find($encryptedProductId);

        return view('user.konter.transaksi', [
            'product' => $product,
            'phoneNumber' => $phoneNumber
        ]);
    }

    public function calculatePrice(Request $request)
    {
        $productId = Crypt::decryptString($request->input('product_id'));
        $product = ProductKonter::findOrFail($productId);

        // Tentukan harga berdasarkan rentang waktu
        $price = match ($request->input('range')) {
            '1-7' => $product->price1,
            '8-14' => $product->price2,
            '15-21' => $product->price3,
            '22-30' => $product->price4,
            default => $product->price,
        };

        // Tentukan deadline
        $deadline = match ($request->input('range')) {
            '1-7' => Carbon::now()->addWeek(),
            '8-14' => Carbon::now()->addWeeks(2),
            '15-21' => Carbon::now()->addWeeks(3),
            '22-30' => Carbon::now()->addWeeks(4),
            default => Carbon::now(),
        };

        return response()->json([
            'price' => $price,
            'deadline' => $deadline->format('Y-m-d')
        ]);
    }

    public function transaksi_proses(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|exists:product_konters,id',
            'submitted_by' => 'required|string',
            'price' => 'required|numeric',
            'phone_number' => 'required|numeric',
            'deadline_date' => 'nullable',
        ]);

        if ($request->deadline_date == "Sekarang") {
            $jatuh_tempo = now()->format('Y-m-d');
        } else {
            $jatuh_tempo = $request->deadline_date;
        }
        // Membuat kode transaksi otomatis
        $today = date('Ymd'); // Format tanggal: YYYYMMDD
        $lastTransaction = TransaksiKonter::whereDate('created_at', now()->format('Y-m-d'))
            ->orderBy('id', 'desc')
            ->first();
        // Ambil nomor urut terakhir, jika tidak ada, mulai dari 1
        $lastNumber = $lastTransaction ? intval(substr($lastTransaction->code, -3)) : 0;
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // Menambahkan angka 3 digit
        // Format kode transaksi
        $transactionCode = 'PULSA-' . $today . '-' . $newNumber;

        DB::beginTransaction();

        try {

            $data = new TransaksiKonter();
            $data->code = $transactionCode;
            $data->product_id = $request->product_id;
            $data->submitted_by = $request->submitted_by;
            $data->price = $request->price;
            $data->deadline_date = $jatuh_tempo;
            $data->status = "pending";

            $data->save();

            $data_detail = new DetailTransaksiKonter();
            $data_detail->transaksi_id = $data->id;
            $data_detail->no_hp = $request->phone_number;
            $data_detail->name = $request->submitted_by;

            $data_detail->save();

            // Nama-nama yang ingin dikirimkan pesan
            $selectedNames = ['aldi wahyudi', 'Rifki'];

            // Ambil data dari database berdasarkan nama yang dipilih
            $access_pengurus = DataWarga::whereIn('name', $selectedNames)
                ->get();

            // URL gambar dari direktori storage
            $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

            // Data untuk link
            $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            $link = "https://keluargamahaya.com/pulsa/{$encryptedId}";

            $product = ProductKonter::find($request->product_id);



            // Mengirim pesan ke setiap nomor
            foreach ($access_pengurus as $access) {
                $number = $access->no_hp; // Nomor telepon
                $name = $access->name;   // Nama warga
                // Membuat pesan khusus untuk masing-masing warga
                $message = "*Pengajuan Pulsa*\n";
                $message .= "Halo {$name},\n\n";
                $message .= "Kami informasikan bahwa {$request->submitted_by} mengajukan pembelian {$product->kategori->name} {$product->provider->name}:\n\n";
                $message .= "- *ID transaksi*: {$transactionCode}\n";
                $message .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
                $message .= "- *Number HP*: {$request->phone_number}\n";
                $message .= "- *Nominal*: Rp" . number_format($product->amount, 0, ',', '.') . "\n";
                $message .= "- *Harga*: Rp" . number_format($request->price, 0, ',', '.') . "\n";
                $message .= "- *Pembelian*: {$request->payment_method}\n";
                $message .= "- *Jatuh Tempo*: {$jatuh_tempo}\n";
                $message .= "Terima kasih atas kerjasama dan dukungan Anda dalam proses ini.\n\n";
                $message .= "Silakan klik link berikut untuk info selanjutnya:\n";
                $message .= $link . "\n\n";
                $message .= "*Salam hormat,*\n";
                $message .= "*Sistem Kas Keluarga*";

                // Mengirim pesan ke nomor warga
                $response = $this->fonnteService->sendWhatsAppMessage($number, $message, $imageUrl);
            }

            DB::commit();

            if (isset($response['status']) && $response['status'] == 'success') {
                return redirect()->route('pulsa')->with('success', 'Pengajuan Pembelian Pulsa sedang di proses, Notifikasi berhasil dikirim!');
            }
            return back()->with('error', 'Data tersimpan, Gagal mengirim notifikasi');

            //jika notifikasi email dan wa aktif maka yang di bawah di komen

            // DB::commit();
            // return redirect()->route('pulsa')->with('success', 'Pengajuan Pembelian Pulsa sedang di proses');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.' . $e->getMessage());
        }
    }
}
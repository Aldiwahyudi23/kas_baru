<?php

namespace App\Http\Controllers\User\Konter;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\AnggaranSaldo;
use App\Models\DataWarga;
use App\Models\Konter\DetailTransaksiKonter;
use App\Models\Konter\KategoriKonter;
use App\Models\Konter\ProductKonter;
use App\Models\Konter\ProviderKonter;
use App\Models\Konter\TransaksiKonter;
use App\Models\Saldo;
use App\Services\FonnteService;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $pengajuan_proses = TransaksiKonter::where('status', ['proses', 'pending'])->get();
        $transaksi_sukses = TransaksiKonter::where('status', 'Berhasil')->get();
        $transaksi_gagal = TransaksiKonter::where('status', 'Gagal')->get();
        $transaksi_selesai = TransaksiKonter::where('status', 'Selesai')->get();

        return view('user.konter.index', compact('pengajuan_proses', 'transaksi_sukses', 'transaksi_gagal', 'transaksi_selesai'));
    }

    /**
     * Show the form for creating a new resource.Berhasil
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
        $id = Crypt::decrypt($id);

        $data = TransaksiKonter::find($id);

        return view('user.konter.show', compact('data'));
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

    //Untuk Halaman Show pengajuan dan konfirmasi
    public function pengajuan(string $id)
    {
        $id = Crypt::decrypt($id);
        $pengajuan = TransaksiKonter::find($id);
        $data_Warga = DataWarga::all();

        return view('user.konter.pengajuan', compact('pengajuan', 'data_Warga'));
    }
    // Jika berhasil maka menyimpan data
    public function pengajuan_berhasil(Request $request, $id)
    {
        $id = Crypt::decrypt($id);

        DB::beginTransaction();

        try {
            if ($request->status == "Berhasil" || $request->status == "Selesai") {
                $data = TransaksiKonter::find($id);
                $data->buying_price = $request->buying_price;
                $data->diskon = $request->diskon;
                $data->status = $request->status;
                if ($request->price) {
                    $data->price = $request->price;
                }
                if ($request->invoice) {
                    $invoice = $request->invoice;
                } else {
                    $invoice = $data->price;
                }
                $data->invoice = $invoice;

                if ($request->status == "Selesai") {
                    $data->margin = $invoice - $request->buying_price;
                    $data->confirmed_by = Auth::user()->data_warga_id;
                    $data->confirmation_date = now();
                }
                if ($request->payment_method) {
                    $data->payment_method = $request->payment_method;
                }
                if ($request->warga_id) {
                    $data->warga_id = $request->warga_id;
                }
                if ($request->payment_method == "transfer") {
                    $data->is_deposited = true;
                } else {
                    $data->is_deposited = false;
                }

                $data->update();

                // Menyimpan data Detail
                $data_detail = DetailTransaksiKonter::find($data->konter_detail_id);
                if ($request->no_hp) {
                    $data_detail->no_hp = $request->no_hp;
                }
                if ($request->token_code) {
                    $data_detail->token_code = $request->token_code;
                }
                if ($request->no_listrik) {
                    $data_detail->no_listrik = $request->no_listrik;
                }
                if ($request->description) {
                    $data_detail->description = $data_detail->description . "<br> Catatan Pengurus : <br>" . $request->description;
                }
                $data_detail->update();

                // Menyimpan data ke saldo



                $saldo_terbaru = Saldo::latest()->first();

                if ($request->status == "Selesai") {
                    // Nominal amount initialization
                    $nominal_amount = 0;
                    $atm = $saldo_terbaru->atm_balance;
                    $out = $saldo_terbaru->cash_outside;

                    if ($data->payment_status == "Hutang") {
                        // Hutang condition
                        $nominal_amount = $invoice; // Nominal amount is taken directly from the invoice

                        if ($request->payment_method == "transfer") {
                            $atm += $nominal_amount; // Add to ATM balance
                            $total = $saldo_terbaru->total_balance + $nominal_amount;
                        } elseif ($request->payment_method == "cash") {
                            $out += $nominal_amount; // Add to cash outside
                            $total = $saldo_terbaru->total_balance + $nominal_amount;
                        }
                    } elseif ($data->payment_status == "Langsung") {
                        // Langsung condition
                        if ($request->payment_method == "transfer") {
                            $nominal_amount = $invoice - $request->buying_price; // Calculate margin
                            $atm += $nominal_amount; // Add margin to ATM balance
                            $total = $saldo_terbaru->total_balance + $nominal_amount;
                        } elseif ($request->payment_method == "cash") {
                            // First transaction: deduct buying price from ATM and total
                            $atm -= $request->buying_price;
                            $nominal_amount = $invoice;
                            $out += $nominal_amount; // Add invoice to cash outside
                            $nominal_amount_cash = $invoice - $request->buying_price; // Calculate margin
                            $total = $saldo_terbaru->total_balance + $nominal_amount_cash;
                        }
                    }

                    // Mengambil waktu saat ini
                    $dateTime = now();

                    // Update total balance

                    // Create new Saldo entry
                    $saldo = new Saldo();
                    $saldo->code = $data->code;
                    $saldo->amount = $nominal_amount;
                    $saldo->atm_balance = $atm;
                    $saldo->total_balance = $total;
                    $saldo->ending_balance = $saldo_terbaru->total_balance;
                    $saldo->cash_outside = $out;

                    $saldo->save();
                } elseif ($request->status == "Berhasil") {
                    // Berhasil condition: buying price reduces ATM and total balances
                    $nominal_amount = -$request->buying_price;
                    $atm = $saldo_terbaru->atm_balance + $nominal_amount;
                    $total = $saldo_terbaru->total_balance + $nominal_amount;

                    // Create new Saldo entry
                    $saldo = new Saldo();
                    $saldo->code = $data->code;
                    $saldo->amount = $nominal_amount;
                    $saldo->atm_balance = $atm;
                    $saldo->total_balance = $total;
                    $saldo->ending_balance = $saldo_terbaru->total_balance;
                    $saldo->cash_outside = $saldo_terbaru->cash_outside;

                    $saldo->save();
                }

                // -----------------------------------------
                if ($data->payment_status == "Langsung") {
                    if ($request->payment_method == "cash") {
                        $nominal_anggaran = $invoice - $request->buying_price; // Calculate margin
                    } else {
                        $nominal_anggaran = $nominal_amount;
                    }
                } else {
                    $nominal_anggaran = $nominal_amount;
                }
                $anggaranKas = Anggaran::where('name', 'Dana Kas')->first();
                $saldo_akhir_kas =  AnggaranSaldo::where('type', $anggaranKas->name)->latest()->first(); //mengambil data yang terakhir berdasarkan type anggaran
                $saldo_kas = new AnggaranSaldo();
                $saldo_kas->type = $anggaranKas->name;
                $saldo_kas->percentage = 0;
                $saldo_kas->amount = $nominal_anggaran;
                $saldo_kas->saldo = $saldo_akhir_kas->saldo + $nominal_anggaran;
                $saldo_kas->saldo_id = $saldo->id; //mengambil id dari model saldo di atas
                $saldo_kas->save();

                // // URL gambar dari direktori storage
                // $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

                // // Data untuk link
                // $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
                // $link = "https://keluargamahaya.com/pulsa/{$encryptedId}";

                // $product = ProductKonter::find($data->product_id);

                // $number = $data_detail->no_hp; // Nomor telepon
                // $name = $data_detail->name;   // Nama warga
                // // Membuat pesan khusus untuk masing-masing warga
                // $message = "*Halo {$name},*\n";
                // $message .= "Kami informasikan bahwa pembelian {$product->kategori->name} {$product->provider->name}:\n";
                // $message .= "- *Pengajuan*: {$data->created_at}\n";
                // $message .= "- *Nominal*: " . number_format($product->amount, 0, ',', '.') . "\n";
                // if ($product->kategori->name == "Listrik") {
                //     $message .= "- *No Meteran*: {$data_detail->no_listrik}\n";
                //     $message .= "- *No TOKEN*: *{$data_detail->token_code}* \n";
                //     $message .= "- *A/N*: {$data_detail->name}\n";
                // }
                // $message .= "- *Harga*: Rp" . number_format($data->price, 0, ',', '.') . "\n";
                // $message .= "- *Pembelian*: {$data->payment_status}\n";
                // $message .= "- *Jatuh Tempo*: {$data->deadline_date}\n";
                // $message .= "Terima kasih atas kerjasama dan dukungannya.\n\n";
                // $message .= "*Salam hormat,*\n";
                // $message .= "*Sistem Kas Keluarga*";

                // // Mengirim pesan ke nomor warga
                // $response = $this->fonnteService->sendWhatsAppMessage($number, $message, $imageUrl);


                // DB::commit();

                // if (isset($response['status']) && $response['status'] == 'success') {
                //     return redirect()->back()->with('success', 'Pengajuan Pembelian Pulsa sedang di proses, Notifikasi berhasil dikirim!');
                // }
                // return back()->with('error', 'Data tersimpan, Gagal mengirim notifikasi');

                //jika notifikasi email dan wa aktif maka yang di bawah di komen

                DB::commit();
                return redirect()->back()->with('success', 'Pengajuan Pembelian Pulsa sedang di proses');
            } else {
                $data = TransaksiKonter::find($id);
                $data->status = $request->status;
                $data->update();
                return redirect()->back()->with('success', 'Pengajuan Pembelian Pulsa sedang di proses');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.' . $e->getMessage());
        }
    }


    public function pulsa()
    {
        return view('user.konter.pulsa');
    }
    public function pulsaUser()
    {
        $story = TransaksiKonter::where('submitted_by',Auth::user()->dataWarga->name)->get();
        return view('user.konter.user.pulsa',compact('story'));
    }

    public function token_listrik()
    {
        return view('user.konter.token_listrik');
    }
    public function token_listrikUser()
    {
        $story = TransaksiKonter::where('submitted_by', Auth::user()->dataWarga->name)->get();
        return view('user.konter.user.token_listrik', compact('story'));
    }
    public function tagihan_listrik()
    {

        $provider = ProviderKonter::where('name', 'Tagihan Listrik')->first();
        if (!$provider) {
            redirect()->back()->with('error', 'Tidak tersedia');
        }
        $product = ProductKonter::where('provider_id', $provider->id)
            ->where('kategori_id', 2)
            ->first();
        return view('user.konter.transaksi_tagihanListrik', compact('product'));
    }
    public function tagihan_listrikUser()
    {

        $provider = ProviderKonter::where('name', 'Tagihan Listrik')->first();
        if (!$provider) {
            redirect()->back()->with('error', 'Tidak tersedia');
        }
        $product = ProductKonter::where('provider_id', $provider->id)
            ->where('kategori_id', 2)
            ->first();
        $story = TransaksiKonter::where('submitted_by', Auth::user()->dataWarga->name)->get();
        return view('user.konter.user.transaksi_tagihanListrik', compact('product', 'story'));
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
        $no_listrik = $request->input('no_listrik');
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

        // Untuk menegecek Listrik
        if ($no_listrik) {
            $cekKategori = KategoriKonter::where('name', 'Listrik')->first();
            // Tentukan provider berdasarkan aturan
            $provider = 'Token Listrik'; // Hardcoded untuk skenario "Token Listrik"
            $kategori = $cekKategori->id;
        } else {
            $cekKategori = KategoriKonter::where('name', 'Pulsa')->first();
            $kategori = $cekKategori->id;
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
            ->where('kategori_id', $kategori)
            ->get(['id', 'amount', 'price']);

        return response()->json([
            'provider' => $provider,
            'products' => $products
        ]);
    }

    public function transaksi_umum($encryptedProductId, $phoneNumber)
    {
        // Cek apakah nomor HP memiliki lebih dari 2 transaksi dengan status tertentu
        $count_transaksi = TransaksiKonter::whereHas('detail', function ($query) use ($phoneNumber) {
            $query->where('no_hp', $phoneNumber);
        })->whereIn('status', ['pending', 'Proses', 'Berhasil'])
            ->count();

        // Jika sudah ada 2 transaksi atau lebih, berikan pesan error
        if ($count_transaksi >= 2) {
            return redirect()->back()->with(
                'error',
                'Pembelian tidak bisa dilakukan karena sudah ada 2 pengajuan yang masuk. Harap lunasi salah satu.'
            );
        }

        // Cek jika ada transaksi dalam status "pending" atau "Proses" untuk nomor HP tersebut
        $cek_status = TransaksiKonter::whereHas('detail', function ($query) use ($phoneNumber) {
            $query->where('no_hp', $phoneNumber);
        })->whereIn('status', ['pending', 'Proses'])
            ->exists();

        if ($cek_status) {
            return redirect()->back()->with(
                'error',
                'Nomor HP tersebut sudah dalam pengajuan dan sedang diproses.'
            );
        }

        // Lanjutkan proses lainnya jika semua validasi lolos


        $product = ProductKonter::find($encryptedProductId);

        return view('user.konter.transaksi', [
            'product' => $product,
            'phoneNumber' => $phoneNumber
        ]);
    }
    public function transaksi_user($encryptedProductId, $phoneNumber)
    {
        // Cek apakah nomor HP memiliki lebih dari 2 transaksi dengan status tertentu
        $count_transaksi = TransaksiKonter::whereHas('detail', function ($query) use ($phoneNumber) {
            $query->where('no_hp', $phoneNumber);
        })->whereIn('status', ['pending', 'Proses', 'Berhasil'])
            ->count();

        // Jika sudah ada 2 transaksi atau lebih, berikan pesan error
        if ($count_transaksi >= 2) {
            return redirect()->back()->with(
                'error',
                'Pembelian tidak bisa dilakukan karena sudah ada 2 pengajuan yang masuk. Harap lunasi salah satu.'
            );
        }

        // Cek jika ada transaksi dalam status "pending" atau "Proses" untuk nomor HP tersebut
        $cek_status = TransaksiKonter::whereHas('detail', function ($query) use ($phoneNumber) {
            $query->where('no_hp', $phoneNumber);
        })->whereIn('status', ['pending', 'Proses'])
            ->exists();

        if ($cek_status) {
            return redirect()->back()->with(
                'error',
                'Nomor HP tersebut sudah dalam pengajuan dan sedang diproses.'
            );
        }

        // Lanjutkan proses lainnya jika semua validasi lolos


        $product = ProductKonter::find($encryptedProductId);

        return view('user.konter.user.transaksi', [
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
            'name' => 'required|string',
            'price' => 'required|numeric',
            'phone_number' => 'required|numeric',
            'deadline_date' => 'nullable',
        ]);

        // Cek apakah nomor HP memiliki lebih dari 2 transaksi dengan status tertentu
        $count_transaksi = TransaksiKonter::whereHas('detail', function ($query) use ($request) {
            $query->where('no_hp', $request->phone_number);
        })->whereIn('status', ['pending', 'Proses', 'Berhasil'])
            ->count();

        // Jika sudah ada 2 transaksi atau lebih, berikan pesan error
        if ($count_transaksi >= 2) {
            return redirect()->back()->with(
                'error',
                'Pembelian tidak bisa dilakukan karena sudah ada 2 pengajuan yang masuk. Harap lunasi salah satu.'
            );
        }

        // Cek jika ada transaksi dalam status "pending" atau "Proses" untuk nomor HP tersebut
        $cek_status = TransaksiKonter::whereHas('detail', function ($query) use ($request) {
            $query->where('no_hp', $request->phone_number);
        })->whereIn('status', ['pending', 'Proses'])
            ->exists();

        if ($cek_status) {
            return redirect()->back()->with(
                'error',
                'Nomor HP tersebut sudah dalam pengajuan dan sedang diproses.'
            );
        }

        // Lanjutkan proses lainnya jika semua validasi lolos
        $filter = ProductKonter::find($request->product_id);


        // Untuk dedline merubah kata sekaran jadi waktu
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
        if ($filter->kategori->name == "Pulsa") {
            $transactionCode = 'Pls-' . $today . '-' . $newNumber;
            // Cek apakah user sedang login
            $submittedBy = Auth::check() ? Auth::user()->name : $request->name;
            $no = $request->phone_number;
        }
        if ($filter->kategori->name == "Listrik") {
            $transactionCode = 'Ltk-' . $today . '-' . $newNumber;
            // Cek apakah user sedang login
            $submittedBy = Auth::check() ? Auth::user()->name : $request->submitted_by;
            $listrik = $request->phone_number;
            $no = $request->no_hp;
        }
        if ($filter->kategori->name == "Kouta") {
            $transactionCode = 'Kt-' . $today . '-' . $newNumber;
            // Cek apakah user sedang login
            $submittedBy = Auth::check() ? Auth::user()->name : $request->name;
            $listrik = $request->phone_number;
            $no = $request->no_hp;
        }

        DB::beginTransaction();

        try {

            $data_detail = new DetailTransaksiKonter();
            $data_detail->no_hp = $no;
            $data_detail->name = $request->name;
            $data_detail->description = $request->description;
            if ($filter->kategori->name == "Listrik") {
                $data_detail->no_listrik = $listrik;
            }

            $data_detail->save();


            $data = new TransaksiKonter();
            $data->code = $transactionCode;
            $data->product_id = $request->product_id;
            $data->konter_detail_id = $data_detail->id;
            $data->submitted_by = $submittedBy;
            $data->price = $request->price;
            $data->deadline_date = $jatuh_tempo;
            $data->payment_status = $request->payment_method;
            $data->status = "Proses";

            $data->save();


            // // Nama-nama yang ingin dikirimkan pesan
            // $selectedNames = ['aldi wahyudi', 'Rifki'];
            // // Ambil data dari database berdasarkan nama yang dipilih
            // $access_pengurus = DataWarga::whereIn('name', $selectedNames)
            //     ->get();

            // // URL gambar dari direktori storage
            // $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

            // // Data untuk link
            // $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            // $link = "https://keluargamahaya.com/pulsa/{$encryptedId}";

            // $product = ProductKonter::find($request->product_id);



            // // Mengirim pesan ke setiap nomor
            // foreach ($access_pengurus as $access) {
            //     $number = $access->no_hp; // Nomor telepon
            //     $name = $access->name;   // Nama warga
            //     // Membuat pesan khusus untuk masing-masing warga
            //     $message = "*Pengajuan Pulsa*\n";
            //     $message .= "Halo {$name},\n\n";
            //     $message .= "Kami informasikan bahwa {$request->submitted_by} mengajukan pembelian {$product->kategori->name} {$product->provider->name}:\n\n";
            //     $message .= "- *ID transaksi*: {$transactionCode}\n";
            //     $message .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
            //     $message .= "- *Number HP*: {$request->phone_number}\n";
            //     $message .= "- *Nominal*: Rp" . number_format($product->amount, 0, ',', '.') . "\n";
            // if ($product->kategori->name == "Listrik") {
            //     $message .= "- *No Meteran*: {$data_detail->no_listrik}\n";
            //     $message .= "- *No TOKEN*: *{$data_detail->token_code}* \n";
            //     $message .= "- *A/N*: {$data_detail->name}\n";
            // }
            //     $message .= "- *Harga*: Rp" . number_format($request->price, 0, ',', '.') . "\n";
            //     $message .= "- *Pembelian*: {$request->payment_method}\n";
            //     $message .= "- *Jatuh Tempo*: {$jatuh_tempo}\n";
            //     $message .= "Terima kasih atas kerjasama dan dukungan Anda dalam proses ini.\n\n";
            //     $message .= "Silakan klik link berikut untuk info selanjutnya:\n";
            //     $message .= $link . "\n\n";
            //     $message .= "*Salam hormat,*\n";
            //     $message .= "*Sistem Kas Keluarga*";

            //     // Mengirim pesan ke nomor warga
            //     $response = $this->fonnteService->sendWhatsAppMessage($number, $message, $imageUrl);
            // }

            // DB::commit();

            // if (isset($response['status']) && $response['status'] == 'success') {
            //     return redirect()->route('pulsa')->with('success', 'Pengajuan Pembelian Pulsa sedang di proses, Notifikasi berhasil dikirim!');
            // }
            // return back()->with('error', 'Data tersimpan, Gagal mengirim notifikasi');

            // //jika notifikasi email dan wa aktif maka yang di bawah di komen

            DB::commit();
            if($request->repayment){
                return redirect()->route('konter.index')->with('success', 'Pengajuan Pembelian Pulsa sedang di proses');
            }else{
                return redirect()->route('pulsa')->with('success', 'Pengajuan Pembelian Pulsa sedang di proses');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.' . $e->getMessage());
        }
    }
    public function transaksi_proses_user(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|exists:product_konters,id',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'phone_number' => 'required|numeric',
            'deadline_date' => 'nullable',
        ]);

        // Cek apakah nomor HP memiliki lebih dari 2 transaksi dengan status tertentu
        $count_transaksi = TransaksiKonter::whereHas('detail', function ($query) use ($request) {
            $query->where('no_hp', $request->phone_number);
        })->whereIn('status', ['pending', 'Proses', 'Berhasil'])
            ->count();

        // Jika sudah ada 2 transaksi atau lebih, berikan pesan error
        if ($count_transaksi >= 2) {
            return redirect()->back()->with(
                'error',
                'Pembelian tidak bisa dilakukan karena sudah ada 2 pengajuan yang masuk. Harap lunasi salah satu.'
            );
        }

        // Cek jika ada transaksi dalam status "pending" atau "Proses" untuk nomor HP tersebut
        $cek_status = TransaksiKonter::whereHas('detail', function ($query) use ($request) {
            $query->where('no_hp', $request->phone_number);
        })->whereIn('status', ['pending', 'Proses'])
            ->exists();

        if ($cek_status) {
            return redirect()->back()->with(
                'error',
                'Nomor HP tersebut sudah dalam pengajuan dan sedang diproses.'
            );
        }

        // Lanjutkan proses lainnya jika semua validasi lolos
        $filter = ProductKonter::find($request->product_id);


        // Untuk dedline merubah kata sekaran jadi waktu
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
        if ($filter->kategori->name == "Pulsa") {
            $transactionCode = 'Pls-' . $today . '-' . $newNumber;
            // Cek apakah user sedang login
            $submittedBy = Auth::check() ? Auth::user()->name : $request->name;
            $no = $request->phone_number;
        }
        if ($filter->kategori->name == "Listrik") {
            $transactionCode = 'Ltk-' . $today . '-' . $newNumber;
            // Cek apakah user sedang login
            $submittedBy = Auth::check() ? Auth::user()->name : $request->submitted_by;
            $listrik = $request->phone_number;
            $no = $request->no_hp;
        }
        if ($filter->kategori->name == "Kouta") {
            $transactionCode = 'Kt-' . $today . '-' . $newNumber;
            // Cek apakah user sedang login
            $submittedBy = Auth::check() ? Auth::user()->name : $request->name;
            $listrik = $request->phone_number;
            $no = $request->no_hp;
        }

        DB::beginTransaction();

        try {

            $data_detail = new DetailTransaksiKonter();
            $data_detail->no_hp = $no;
            $data_detail->name = $request->name;
            $data_detail->description = $request->description;
            if ($filter->kategori->name == "Listrik") {
                $data_detail->no_listrik = $listrik;
            }

            $data_detail->save();


            $data = new TransaksiKonter();
            $data->code = $transactionCode;
            $data->product_id = $request->product_id;
            $data->konter_detail_id = $data_detail->id;
            $data->submitted_by = $submittedBy;
            $data->price = $request->price;
            $data->deadline_date = $jatuh_tempo;
            $data->payment_status = $request->payment_method;
            $data->status = "Proses";

            $data->save();


            // // Nama-nama yang ingin dikirimkan pesan
            // $selectedNames = ['aldi wahyudi', 'Rifki'];
            // // Ambil data dari database berdasarkan nama yang dipilih
            // $access_pengurus = DataWarga::whereIn('name', $selectedNames)
            //     ->get();

            // // URL gambar dari direktori storage
            // $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

            // // Data untuk link
            // $encryptedId = Crypt::encrypt($data->id); // Mengenkripsi ID untuk keamanan
            // $link = "https://keluargamahaya.com/pulsa/{$encryptedId}";

            // $product = ProductKonter::find($request->product_id);



            // // Mengirim pesan ke setiap nomor
            // foreach ($access_pengurus as $access) {
            //     $number = $access->no_hp; // Nomor telepon
            //     $name = $access->name;   // Nama warga
            //     // Membuat pesan khusus untuk masing-masing warga
            //     $message = "*Pengajuan Pulsa*\n";
            //     $message .= "Halo {$name},\n\n";
            //     $message .= "Kami informasikan bahwa {$request->submitted_by} mengajukan pembelian {$product->kategori->name} {$product->provider->name}:\n\n";
            //     $message .= "- *ID transaksi*: {$transactionCode}\n";
            //     $message .= "- *Tanggal Pengajuan*: {$data->created_at}\n";
            //     $message .= "- *Number HP*: {$request->phone_number}\n";
            //     $message .= "- *Nominal*: Rp" . number_format($product->amount, 0, ',', '.') . "\n";
            // if ($product->kategori->name == "Listrik") {
            //     $message .= "- *No Meteran*: {$data_detail->no_listrik}\n";
            //     $message .= "- *No TOKEN*: *{$data_detail->token_code}* \n";
            //     $message .= "- *A/N*: {$data_detail->name}\n";
            // }
            //     $message .= "- *Harga*: Rp" . number_format($request->price, 0, ',', '.') . "\n";
            //     $message .= "- *Pembelian*: {$request->payment_method}\n";
            //     $message .= "- *Jatuh Tempo*: {$jatuh_tempo}\n";
            //     $message .= "Terima kasih atas kerjasama dan dukungan Anda dalam proses ini.\n\n";
            //     $message .= "Silakan klik link berikut untuk info selanjutnya:\n";
            //     $message .= $link . "\n\n";
            //     $message .= "*Salam hormat,*\n";
            //     $message .= "*Sistem Kas Keluarga*";

            //     // Mengirim pesan ke nomor warga
            //     $response = $this->fonnteService->sendWhatsAppMessage($number, $message, $imageUrl);
            // }

            // DB::commit();

            // if (isset($response['status']) && $response['status'] == 'success') {
            //     return redirect()->route('pulsa')->with('success', 'Pengajuan Pembelian Pulsa sedang di proses, Notifikasi berhasil dikirim!');
            // }
            // return back()->with('error', 'Data tersimpan, Gagal mengirim notifikasi');

            // //jika notifikasi email dan wa aktif maka yang di bawah di komen

            DB::commit();
            if($request->repayment){
                return redirect()->route('konter.index')->with('success', 'Pengajuan Pembelian Pulsa sedang di proses');
            }else{
                return redirect()->route('dashboard.index')->with('success', 'Pengajuan Pembelian Pulsa sedang di proses');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.' . $e->getMessage());
        }
    }

    public function checkPhone($phone)
    {
        // Cari nomor telepon di tabel DataWarga
        $dataWarga = DataWarga::where('no_hp', $phone)->first();

        if ($dataWarga) {
            return response()->json([
                'success' => true,
                'name' => $dataWarga->name,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nomor tidak ditemukan',
        ]);
    }

    public function repayment_pulsa ($id)
    {
$id = Crypt::decrypt($id);
$pengajuan = TransaksiKonter::find($id);
        $product = ProductKonter::find($pengajuan->product_id);
        if ($product->kategori->name == "Pulsa") {
            $phoneNumber = $pengajuan->detail->no_hp;
        } elseif ($product->kategori->name == "Kouta") {
            $phoneNumber = $pengajuan->detail->no_hp;
        } else {
            $phoneNumber = $pengajuan->detail->no_listrik;
        }

        return view('user.konter.repayment.transaksi',compact('product','phoneNumber', 'pengajuan'));
    }
}
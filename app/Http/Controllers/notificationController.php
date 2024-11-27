<?php

namespace App\Http\Controllers;

use App\Models\AccessProgram;
use App\Models\Program;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class notificationController extends Controller
{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    public function sendPaymentSuccessNotification(Request $request)
    {
        // $program = Program::where('name', 'Kas Keluarga')->first();
        // $access_program_kas = AccessProgram::where('program_id', $program->id)->get();
        $access_program_kas = AccessProgram::whereHas('program', function ($query) {
            $query->where('name', 'Kas Keluarga');
        })->get();

        $target = [];
        foreach ($access_program_kas as $data) {
            // Menambahkan nomor telepon lengkap ke dalam array target
            $target[] = $data->dataWarga->no_hp;
        }
        // Menghasilkan array yang berisi semua nomor telepon
        $numbers = $target;

        $message = "*Percobaan* \n";
        $message .= "Halo, pembayaran kas Anda sebesar \n  Rp " . number_format($request->amount, 2, ',', '.') . " telah berhasil.\n\n";
        $message .= "Detail Pembayaran:\n";
        $message .= "- Nama: {$request->name}\n";
        $message .= "- Tanggal: " . now()->format('d-m-Y') . "\n";
        $message .= "- Keterangan: Pembayaran kas keluarga.\n\n";
        $message .= "*Terima kasih telah melakukan pembayaran.* \n\n";

        // URL gambar dari direktori storage
        $imageUrl = asset('storage/kas/pengeluaran/ymKJ8SbQ7NLrLAhjAAKMNfOFHCK8O70HiqEiiIPE.jpg');

        foreach ($numbers as $number) {
            $response = $this->fonnteService->sendWhatsAppMessage($number, $message, $imageUrl);
        }
        if (isset($response['status']) && $response['status'] == 'success') {
            return back()->with('success', 'Notifikasi berhasil dikirim!');
        }

        return response()->json(['message' => 'Gagal mengirim notifikasi'], 500);
    }
    public function index()
    {
        $access_program_kas = AccessProgram::where('program_id', 1)->get();

        $target = [];
        foreach ($access_program_kas as $data) {
            // Menambahkan nomor telepon lengkap ke dalam array target
            $target[] = $data->dataWarga->no_hp;
        }

        // Menghasilkan array yang berisi semua nomor telepon
        $numbers = $target;
        // print_r($numbers);  // Output: ['083825740395', '084312345678', '082536974839']
        return view('tes_notif',);
    }
}

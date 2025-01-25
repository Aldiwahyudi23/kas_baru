<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpLoginController extends Controller
{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    public function showLoginForm()
    {
        return view('auth.login-otp');
    }

    public function checkPhone(Request $request)
    {
        $request->validate(['phone' => 'required|numeric']);

        $user = User::where('no_hp', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'Nomor HP tidak terdaftar.']);
        }

        $otp = rand(1000, 9999); // 4 digit OTP
        $expiresAt = Carbon::now()->addMinutes(2);

        // Simpan OTP dan waktu kedaluwarsa ke dalam database dan session
        session(['phone' => $request->phone, 'otp' => $otp, 'expires_at' => $expiresAt->timestamp]);


        Otp::updateOrCreate(
            ['phone' => $request->phone],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        $message = "Hai! Kode OTP Anda untuk login adalah: $otp. Kode ini berlaku selama 2 menit. Harap masukkan kode OTP di aplikasi untuk melanjutkan proses login. Terima kasih!";

        $img = "";
        $this->fonnteService->sendWhatsAppMessage($request->phone, $message, $img);

        return redirect()->route('login-otp')->with([
            'otp_sent' => true,
            'phone' => $request->phone,
            'expires_at' => $expiresAt->timestamp, // Kirim waktu kedaluwarsa sebagai timestamp
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
            'otp' => 'required|numeric|digits:4',
        ]);

        $otp = Otp::where('phone', $request->phone) // Menyaring berdasarkan nomor telepon
            ->where('otp', $request->otp) // Menyaring berdasarkan OTP yang dimasukkan
            ->where('expires_at', '>', Carbon::now()) // Memastikan waktu kadaluwarsa belum lewat
            ->first(); // Mengambil satu record yang pertama jika cocok
        $cek = Otp::where('phone', $request->phone) // Menyaring berdasarkan nomor telepon
            ->where('expires_at', '>', Carbon::now()) // Memastikan waktu kadaluwarsa belum lewat
            ->first(); // Mengambil satu record yang pertama jika cocok
        if (!$otp) {
            // Jika OTP tidak ditemukan atau sudah kedaluwarsa
            // $otpData = Otp::where('phone', $request->phone)->latest()->first(); // Ambil OTP terakhir untuk mendapatkan `expires_at`
            // $expiresAt = $otpData ? $otpData->expires_at : Carbon::now()->addMinutes(2); // Jika tidak ditemukan, set default waktu mundur 2 menit

            // Jika OTP tidak ditemukan atau sudah kedaluwarsa
            // return redirect()->route('login-otp')
            // ->with('otp_sent', true)
            // ->with('phone', $request->phone)
            //->withErrors(['otp' => 'Kode OTP tidak valid atau telah kedaluwarsa.'])
            // ->with('expires_at',  Carbon::parse($cek->expires_at)->timestamp); // Ambil expires_at dari OTP yang ada, jika tidak ada gunakan waktu default
            // }


            // Jika OTP tidak ditemukan atau sudah kedaluwarsa
            $otpData = Otp::where('phone', $request->phone)->latest()->first(); // Ambil OTP terakhir untuk mendapatkan `expires_at`
            $expiresAt = $otpData ? $otpData->expires_at : Carbon::now()->addMinutes(2); // Jika tidak ditemukan, set default waktu mundur 2 menit

            return redirect()->route('login-otp')
                ->with('otp_sent', true)
                ->with('phone', $request->phone)
                ->withErrors(['otp' => 'Kode OTP tidak valid atau telah kedaluwarsa.'])
                ->with('expires_at', Carbon::parse($expiresAt)->timestamp); // Gunakan waktu dari database atau waktu default
        }




        $user = User::where('no_hp', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'Nomor HP tidak terdaftar.']);
        }

        Auth::login($user, true); // Login dengan "Remember Me"

        $otp->delete(); // Hapus OTP setelah berhasil login

        return redirect()->intended('/dashboard');
    }



    public function resendOtp(Request $request)
    {

        $request->validate(['phone' => 'required|numeric']);

        $user = User::where('no_hp', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'Nomor HP tidak terdaftar.']);
        }

        $otp = rand(1000, 9999); // 4 digit OTP
        $expiresAt = Carbon::now()->addMinutes(2);
        // Simpan OTP dan waktu kedaluwarsa ke dalam database dan session
        session(['phone' => $request->phone, 'otp' => $otp, 'expires_at' => $expiresAt->timestamp]);


        Otp::updateOrCreate(
            ['phone' => $request->phone],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        $message = "Hai! Kode OTP Anda untuk login adalah: $otp. Kode ini berlaku selama 2 menit. Harap masukkan kode OTP di aplikasi untuk melanjutkan proses login. Terima kasih!";
        $img = "";
        $this->fonnteService->sendWhatsAppMessage($request->phone, $message, $img);

        return redirect()->route('login-otp')->with([
            'otp_sent' => true,
            'phone' => $request->phone,
            'expires_at' => $expiresAt->timestamp, // Kirim waktu kedaluwarsa sebagai timestamp
        ]);
    }
}

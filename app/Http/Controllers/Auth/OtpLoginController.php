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

        Otp::updateOrCreate(
            ['phone' => $request->phone],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        $message = "Hai! Kode OTP Anda untuk login adalah: $otp. Kode ini berlaku selama 2 menit. Harap masukkan kode OTP di aplikasi untuk melanjutkan proses login. Terima kasih!";

        $img = "";
        $this->fonnteService->sendWhatsAppMessage($request->phone, $message, $img);

        return redirect()->route('login')->with([
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

        $otp = Otp::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau telah kedaluwarsa.']);
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
        $phone = session('phone');
        $otp = rand(1000, 9999); // OTP baru
        $expiresAt = Carbon::now()->addMinutes(2);

        Otp::updateOrCreate(
            ['phone' => $phone],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        $message = "Hai! Kode OTP Anda untuk login adalah: $otp. Kode ini berlaku selama 2 menit. Harap masukkan kode OTP di aplikasi untuk melanjutkan proses login. Terima kasih!";

        $img = "";
        $this->fonnteService->sendWhatsAppMessage($phone, $message, $img);

        return redirect()->route('login')->with([
            'otp_sent' => true,
            'phone' => $phone,
            'expires_at' => $expiresAt->timestamp, // Kirim waktu kedaluwarsa sebagai timestamp
        ]);
    }
}

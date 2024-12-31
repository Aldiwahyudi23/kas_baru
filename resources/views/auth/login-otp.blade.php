<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex justify-center mb-6 animate-bounce">
                <x-authentication-card-logo />
            </div>
        </x-slot>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <h1 class="text-center text-2xl font-semibold mb-6">
                @if(session('otp_sent'))
                Masukkan Kode OTP
                @else
                Login dengan Nomor HP
                @endif
            </h1>

            <x-validation-errors class="mb-4" />
            @if(session('otp_error'))
            <div class="text-red-500 text-center mb-4">
                {{ session('otp_error') }}
            </div>
            @endif

            @if (session('otp_sent'))
            <!-- Form OTP -->
            <form id="otpForm" action="{{ route('login.verify') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="phone" value="{{ session('phone') }}">

                <div>
                    <x-label for="otp" value="Masukkan OTP" />
                    <x-input id="otp" name="otp" type="text" maxlength="4" required class="block mt-1 w-full"
                        placeholder="4 Digit OTP" />
                </div>

                <div id="countdown" class="text-center text-sm text-gray-600 mt-4"></div>

                <div class="flex justify-center mt-6">
                    <x-button
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md focus:outline-none focus:ring focus:ring-indigo-300 transition-transform transform hover:scale-105">
                        Verifikasi
                    </x-button>
                </div>

                <div id="resend-link" class="text-center mt-4 hidden">
                    <a href="{{ route('resendOtp') }}" class="text-indigo-600 hover:underline">
                        Kirim Ulang OTP
                    </a>
                </div>

            </form>
            @else
            <!-- Form Nomor HP -->
            <form action="{{ route('login.check') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <x-label for="phone" value="Nomor HP" />
                    <x-input id="phone" name="phone" type="text" required class="block mt-1 w-full"
                        placeholder="08xxxxxxxxxx" />
                </div>

                <div class="flex justify-center mt-6">
                    <x-button
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md focus:outline-none focus:ring focus:ring-indigo-300 transition-transform transform hover:scale-105">
                        Kirim OTP
                    </x-button>
                </div>
            </form>
            @endif
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Ambil waktu kedaluwarsa dari session flash dengan Blade Syntax
                const expiresAt = @json(session(
                    'expires_at')); // Menggunakan Blade untuk mengirimkan data session sebagai JSON
                const currentTime = Math.floor(Date.now() / 1000); // Waktu saat ini dalam detik

                let timer = expiresAt - currentTime; // Waktu hitung mundur (dalam detik)

                const countdownElement = document.getElementById('countdown');
                const resendLink = document.getElementById('resend-link');
                const otpInput = document.getElementById('otp');
                const otpForm = document.getElementById('otpForm');

                // Tampilkan countdown jika elemen countdown tersedia
                if (countdownElement) {
                    const interval = setInterval(() => {
                        if (timer > 0) {
                            countdownElement.textContent = `Kode OTP berlaku dalam ${timer--} detik.`;
                        } else {
                            clearInterval(interval);
                            countdownElement.textContent = "Kode OTP sudah tidak berlaku.";
                            resendLink.classList.remove('hidden'); // Menampilkan link Kirim Ulang OTP
                        }
                    }, 1000);
                }

                // Submit otomatis ketika OTP 4 digit sudah dimasukkan
                if (otpInput) {
                    otpInput.addEventListener('input', function() {
                        if (otpInput.value.length === 4) {
                            otpForm.submit(); // Submit form secara otomatis jika OTP sudah lengkap
                        }
                    });
                }
            });
        </script>

    </x-authentication-card>
</x-guest-layout>
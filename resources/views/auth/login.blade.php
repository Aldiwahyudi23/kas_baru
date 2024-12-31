<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex justify-center mb-6 animate-bounce">
                <x-authentication-card-logo />
            </div>
        </x-slot>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <x-validation-errors class="mb-4" />

            @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 animate-fade-in">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ isset($guard) ? url($guard.'/login') : route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <x-label for="email" value="{{ __('Email') }}" class="text-lg font-semibold" />
                    <x-input id="email"
                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 rounded-lg"
                        type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                </div>

                <div>
                    <x-label for="password" value="{{ __('Password') }}" class="text-lg font-semibold" />
                    <div class="relative">
                        <!-- Tombol Toggle Password -->
                        <button type="button" id="togglePassword"
                            class="absolute inset-y-0 right-0 left-2 pr-3 flex items-center text-gray-500 focus:outline-none">
                            <!-- Ikon Mata -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12m4.8-3.6A7.2 7.2 0 1018 17.2a7.2 7.2 0 001.8-8.8zm0 0a7.2 7.2 0 01-8.8 1.8 7.2 7.2 0 018.8 1.8zm0 0a7.2 7.2 0 01-8.8-8.8 7.2 7.2 0 018.8 8.8z" />
                            </svg>
                        </button>

                        <!-- Input Password -->
                        <x-input id="password"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 rounded-lg pl-10"
                            type="password" name="password" required autocomplete="current-password" />
                    </div>
                </div>


                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="flex items-center">
                        <x-checkbox id="remember_me" name="remember" checked
                            class="text-indigo-600 focus:ring-indigo-500" />
                        <span class="ms-2 text-sm text-gray-600">{{ __('Selalu masuk') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out"
                        href="{{ route('password.request') }}">
                        {{ __('Lupa Password?') }}
                    </a>
                    @endif

                </div>

                <div class="flex items-center justify-center mt-6">
                    <x-button
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md focus:outline-none focus:ring focus:ring-indigo-300 transition-transform transform hover:scale-105">
                        {{ __('Log in') }}
                    </x-button>
                    <!-- Link untuk Login menggunakan OTP -->
                </div>

            </form>
            <div class="text-center mt-4">
                <a href="{{ route('login-otp') }}"
                    class="text-sm text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out">
                    {{ __('Masuk Menggunakan No Hp') }}
                </a>
            </div>
        </div>

        <script>
            document.getElementById('togglePassword').addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;

                // Ganti ikon
                this.querySelector('svg').classList.toggle('text-indigo-600');
            });
        </script>


        <style>
            button#togglePassword {
                height: 100%;
                align-items: center;
            }

            @keyframes fade-in {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .animate-fade-in {
                animation: fade-in 0.5s ease-in-out;
            }
        </style>
    </x-authentication-card>
</x-guest-layout>
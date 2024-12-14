<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex justify-center mb-4">
                <!-- Ganti dengan logo Anda -->
                <img src="{{ asset('default/logo.jpg') }}" alt="Logo"
                    class="w-20 h-20 animate__animated animate__fadeIn animate__delay-1s">
            </div>
        </x-slot>

        <h3
            class="text-center text-2xl font-semibold text-indigo-600 mb-6 animate__animated animate__fadeIn animate__delay-1s">
            Admin Masuk</h3>

        <x-validation-errors class="mb-4" />

        @session('status')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ $value }}
        </div>
        @endsession

        <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
            @csrf

            <div class="flex flex-col animate__animated animate__fadeIn animate__delay-2s">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email"
                    class="block mt-1 w-full border-2 border-gray-300 focus:border-indigo-500 rounded-lg shadow-md py-2 px-3"
                    type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex flex-col mt-4 animate__animated animate__fadeIn animate__delay-2s">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password"
                    class="block mt-1 w-full border-2 border-gray-300 focus:border-indigo-500 rounded-lg shadow-md py-2 px-3"
                    type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4 animate__animated animate__fadeIn animate__delay-3s">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                <a class="text-sm text-indigo-600 hover:text-indigo-800 transition-colors duration-200"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
                @endif

                <x-button
                    class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg py-2 px-6 transition-colors duration-300 shadow-lg transform hover:scale-105">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
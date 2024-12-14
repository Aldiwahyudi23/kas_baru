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
                    <x-input id="password"
                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 rounded-lg"
                        type="password" name="password" required autocomplete="current-password" />
                </div>

                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="flex items-center">
                        <x-checkbox id="remember_me" name="remember" class="text-indigo-600 focus:ring-indigo-500" />
                        <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                    @endif
                </div>

                <div class="flex items-center justify-center mt-6">
                    <x-button
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md focus:outline-none focus:ring focus:ring-indigo-300 transition-transform transform hover:scale-105">
                        {{ __('Log in') }}
                    </x-button>
                </div>
            </form>
        </div>

        <style>
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
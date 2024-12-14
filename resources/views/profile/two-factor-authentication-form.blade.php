<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Two Factor Authentication') }}</h6>
    </div>
    <div class="card-body">
        <!-- Status Header -->
        <h3 class="h5 font-weight-bold">
            @if ($this->enabled)
            @if ($showingConfirmation)
            {{ __('Finish enabling two factor authentication.') }}
            @else
            {{ __('You have enabled two factor authentication.') }}
            @endif
            @else
            {{ __('You have not enabled two factor authentication.') }}
            @endif
        </h3>

        <!-- Description -->
        <p class="text-muted mt-3">
            {{ __('Add additional security to your account using two factor authentication.') }}
        </p>
        <p class="text-muted">
            {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
        </p>

        @if ($this->enabled)
        <!-- QR Code Section -->
        @if ($showingQrCode)
        <div class="mt-4">
            <p>
                @if ($showingConfirmation)
                {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.') }}
                @else
                {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.') }}
                @endif
            </p>
            <div class="mt-3 p-3 bg-light border">
                {!! $this->user->twoFactorQrCodeSvg() !!}
            </div>
            <p class="font-weight-bold mt-3">
                {{ __('Setup Key') }}: {{ decrypt($this->user->two_factor_secret) }}
            </p>
        </div>

        <!-- OTP Code Input -->
        @if ($showingConfirmation)
        <div class="mt-4">
            <label for="code" class="form-label">{{ __('Code') }}</label>
            <input type="text" id="code" class="form-control w-50" wire:model="code"
                wire:keydown.enter="confirmTwoFactorAuthentication" inputmode="numeric" autofocus
                autocomplete="one-time-code">
            @error('code')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>
        @endif
        @endif

        <!-- Recovery Codes Section -->
        @if ($showingRecoveryCodes)
        <div class="mt-4">
            <p class="font-weight-bold">
                {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
            </p>
            <div class="p-3 bg-light border rounded">
                @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                <div class="font-monospace">{{ $code }}</div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        <!-- Buttons -->
        <div class="mt-4">
            @if (! $this->enabled)
            <button class="btn btn-primary" wire:click="enableTwoFactorAuthentication" wire:loading.attr="disabled">
                {{ __('Enable') }}
            </button>
            @else
            @if ($showingRecoveryCodes)
            <button class="btn btn-secondary me-2" wire:click="regenerateRecoveryCodes" wire:loading.attr="disabled">
                {{ __('Regenerate Recovery Codes') }}
            </button>
            @elseif ($showingConfirmation)
            <button class="btn btn-primary me-2" wire:click="confirmTwoFactorAuthentication"
                wire:loading.attr="disabled">
                {{ __('Confirm') }}
            </button>
            @else
            <button class="btn btn-secondary me-2" wire:click="showRecoveryCodes" wire:loading.attr="disabled">
                {{ __('Show Recovery Codes') }}
            </button>
            @endif

            <button class="btn btn-danger" wire:click="disableTwoFactorAuthentication" wire:loading.attr="disabled">
                {{ __('Disable') }}
            </button>
            @endif
        </div>
    </div>
</div>
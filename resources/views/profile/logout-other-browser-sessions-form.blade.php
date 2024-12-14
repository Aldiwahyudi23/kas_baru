<div>
    <!-- Card Wrapper -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Browser Sessions') }}</h6>
        </div>
        <div class="card-body">
            <p class="text-muted">
                {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.') }}
            </p>

            @if (count($this->sessions) > 0)
            <ul class="list-group mt-4">
                @foreach ($this->sessions as $session)
                <li class="list-group-item d-flex align-items-center">
                    <div class="me-3">
                        @if ($session->agent->isDesktop())
                        <i class="fas fa-desktop text-secondary fa-lg"></i>
                        @else
                        <i class="fas fa-mobile-alt text-secondary fa-lg"></i>
                        @endif
                    </div>
                    <div>
                        <p class="mb-0 text-secondary">
                            {{ $session->agent->platform() ? $session->agent->platform() : __('Unknown') }} -
                            {{ $session->agent->browser() ? $session->agent->browser() : __('Unknown') }}
                        </p>
                        <small class="text-muted">
                            {{ $session->ip_address }},
                            @if ($session->is_current_device)
                            <span class="text-success font-weight-bold">{{ __('This device') }}</span>
                            @else
                            {{ __('Last active') }} {{ $session->last_active }}
                            @endif
                        </small>
                    </div>
                </li>
                @endforeach
            </ul>
            @endif

            <div class="mt-4">
                <button class="btn btn-primary" wire:click="confirmLogout" wire:loading.attr="disabled">
                    {{ __('Log Out Other Browser Sessions') }}
                </button>
                <span wire:loading.class="d-inline ms-2 text-success" wire:target="confirmLogout">
                    {{ __('Processing...') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Log Out Other Devices Confirmation Modal -->
    @if ($confirmingLogout)
    <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">{{ __('Log Out Other Browser Sessions') }}</h5>
                    <button type="button" class="btn-close" wire:click="$toggle('confirmingLogout')"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}
                    </p>
                    <input type="password" class="form-control mt-3" placeholder="{{ __('Password') }}"
                        wire:model="password" wire:keydown.enter="logoutOtherBrowserSessions" />
                    @error('password')
                    <span class="text-danger mt-2">{{ $message }}</span>
                    @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        wire:click="$toggle('confirmingLogout')">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" wire:click="logoutOtherBrowserSessions"
                        wire:loading.attr="disabled">
                        {{ __('Log Out Other Browser Sessions') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Update Password') }}</h6>
    </div>
    <div class="card-body">
        <p class="mb-4 text-muted">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
        <form wire:submit.prevent="updatePassword">
            <!-- Current Password -->
            <div class="form-group">
                <label for="current_password">{{ __('Current Password') }}</label>
                <input type="password" id="current_password" class="form-control" wire:model="state.current_password"
                    autocomplete="current-password">
                @error('current_password')
                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                @enderror
            </div>

            <!-- New Password -->
            <div class="form-group">
                <label for="password">{{ __('New Password') }}</label>
                <input type="password" id="password" class="form-control" wire:model="state.password"
                    autocomplete="new-password">
                @error('password')
                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                <input type="password" id="password_confirmation" class="form-control"
                    wire:model="state.password_confirmation" autocomplete="new-password">
                @error('password_confirmation')
                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                @if (session()->has('saved'))
                <span class="text-success small">{{ __('Saved.') }}</span>
                @endif
                <button type="submit" class="btn btn-primary">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>
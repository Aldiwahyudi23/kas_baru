<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Profile Information') }}</h6>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="updateProfileInformation">
            <!-- Profile Photo -->
            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="form-group">
                <label for="photo" class="form-label">{{ __('Photo') }}</label>

                <!-- Current Profile Photo -->
                <div class="mb-2" x-show="!photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}"
                        class="img-profile rounded-circle" style="width: 100px; height: 100px;">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mb-2" x-show="photoPreview" style="display: none;">
                    <img class="img-profile rounded-circle" x-bind:src="photoPreview"
                        style="width: 100px; height: 100px;">
                </div>

                <!-- File Input -->
                <input type="file" id="photo" class="form-control d-none" wire:model.live="photo" x-ref="photo"
                    x-on:change="
                               photoName = $refs.photo.files[0].name;
                               const reader = new FileReader();
                               reader.onload = (e) => {
                                   photoPreview = e.target.result;
                               };
                               reader.readAsDataURL($refs.photo.files[0]);
                           ">

                <button type="button" class="btn btn-primary mt-2" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
                </button>

                @if ($this->user->profile_photo_path)
                <button type="button" class="btn btn-danger mt-2" wire:click="deleteProfilePhoto">
                    {{ __('Remove Photo') }}
                </button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
            @endif

            <!-- Name -->
            <div class="form-group">
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input type="text" id="name" class="form-control" wire:model="state.name" required autocomplete="name">
                <x-input-error for="name" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input type="email" id="email" class="form-control" wire:model="state.email" required
                    autocomplete="username">
                <x-input-error for="email" class="mt-2" />

                @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) &&
                !$this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 text-danger">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="btn btn-link p-0" wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                <p class="text-sm mt-2 text-success">
                    {{ __('A new verification link has been sent to your email address.') }}
                </p>
                @endif
                @endif
            </div>

            <!-- Submit Button -->
            <div class="form-group d-flex justify-content-between">
                <span class="text-success" wire:loading wire:target="photo">
                    {{ __('Uploading...') }}
                </span>
                <button type="submit" class="btn btn-primary">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
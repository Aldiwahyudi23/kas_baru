@extends('user.layout.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Profile') }}</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Profile Information -->
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Profile Information</h6>
                </div>
                <div class="card-body">
                    @livewire('profile.update-profile-information-form')
                </div>
            </div>
            @endif

            <!-- Update Password -->
            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Password</h6>
                </div>
                <div class="card-body">
                    @livewire('profile.update-password-form')
                </div>
            </div>
            @endif

            <!-- Two-Factor Authentication -->
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Two-Factor Authentication</h6>
                </div>
                <div class="card-body">
                    @livewire('profile.two-factor-authentication-form')
                </div>
            </div>
            @endif

            <!-- Logout Other Browser Sessions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Logout Other Browser Sessions</h6>
                </div>
                <div class="card-body">
                    @livewire('profile.logout-other-browser-sessions-form')
                </div>
            </div>

            <!-- Delete Account -->
            <!-- @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Delete Account</h6>
                </div>
                <div class="card-body">
                    @livewire('profile.delete-user-form')
                </div>
            </div>
            @endif -->
        </div>
    </div>
</div>
@endsection
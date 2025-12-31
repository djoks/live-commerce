<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    $twoFactorRoute = Volt::route('settings/two-factor', 'settings.two-factor');

    // Conditionally apply password confirmation middleware if 2FA is enabled and requires confirmation
    if (Features::canManageTwoFactorAuthentication()
        && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
        $twoFactorRoute->middleware(['password.confirm']);
    }

    $twoFactorRoute->name('two-factor.show');
});

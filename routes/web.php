<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationRequestController;
use App\Http\Controllers\TwoFactorAuthenticationController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureUserIsAuthorizedToUseApp;
use App\Http\Middleware\Requires2FA;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(static function (): void {
    // redirect to dashboard when on root page
    Route::get('/', static function () {
        return redirect('/dashboard');
    });

    Route::get('register-with-token/{token}', [RegisteredUserController::class, 'create'])
        ->name('register-with-token')
        ->middleware(['signed', 'throttle:6,1']);

    Route::post('register', [RegisteredUserController::class, 'store'])->name('register');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');

    Route::get('/registration-request/create', [RegistrationRequestController::class, 'create'])
        ->name('registration-requests.create');

    Route::post('/registration-request/create', [RegistrationRequestController::class, 'store'])
        ->name('registration-requests.store')
        ->middleware('throttle:' . config('throttle.registration_requests'));

    Route::get('/registration-request/thank-you', [RegistrationRequestController::class, 'thankYou'])
        ->name('registration-requests.thank-you');
});

Route::middleware('auth')->group(static function (): void {
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::middleware(['auth', Requires2FA::class, EnsureUserIsAuthorizedToUseApp::class])->group(static function (): void {
    Route::get('/dashboard', static function () {
        return view('dashboard');
    })->name('dashboard');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/profile/2fa/reset', [TwoFactorAuthenticationController::class, 'create'])->name('profile.2fa.reset');
    Route::post('/profile/2fa/reset', [TwoFactorAuthenticationController::class, 'update'])->name('profile.2fa.confirm');

    Route::prefix('users')->group(static function (): void {
        Route::get('/', [UserController::class, 'index'])->name('users.index');

        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/create', [UserController::class, 'store'])->name('users.store');

        Route::get('/{user}', [UserController::class, 'edit'])->name('users.edit');
        Route::post('/{user}', [UserController::class, 'update'])->name('users.update');

        Route::get('/reset/{user}', [UserController::class, 'reset'])->name('users.reset');
        Route::post('/reset/{user}', [UserController::class, 'doReset'])->name('users.reset');

        Route::get('/remove/{user}', [UserController::class, 'remove'])->name('users.remove');
        Route::delete('/remove/{user}', [UserController::class, 'delete'])->name('users.delete');

        Route::get('/deactivate/{user}', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::get('/activate/{user}', [UserController::class, 'activate'])->name('users.activate');
    });

    Route::prefix('clients')->group(static function (): void {
        Route::get('/', [ClientController::class, 'index'])->name('clients.index');

        Route::get('/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('/create', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/{client}', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/{client}', [ClientController::class, 'update'])->name('clients.update');
    });

    Route::prefix('organisations')->group(static function (): void {
        Route::get('/', [OrganisationController::class, 'index'])->name('organisations.index');
        Route::get('/create', [OrganisationController::class, 'create'])->name('organisations.create');
        Route::post('/create', [OrganisationController::class, 'store'])->name('organisations.store');
        Route::get('/{organisation}', [OrganisationController::class, 'edit'])->name('organisations.edit');
        Route::put('/{organisation}', [OrganisationController::class, 'update'])->name('organisations.update');
    });
});

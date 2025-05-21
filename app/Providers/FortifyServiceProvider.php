<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Fortify\EnsureUserIsActive;
use App\Actions\Fortify\RedirectToTwoFactorAuthentication;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\CanonicalizeUsername;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Fortify;

use function array_filter;
use function assert;
use function config;
use function is_string;
use function view;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // @codeCoverageIgnoreStart
        // We currently don't have tests for individual views
        Fortify::twoFactorChallengeView(static function () {
            return view('auth.two-factor-challenge');
        });
        // @codeCoverageIgnoreEnd

        RateLimiter::for('login', static function (Request $request) {
            $username = $request->input(Fortify::username());
            assert(is_string($username));

            $throttleKey = Str::transliterate(Str::lower($username) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', static function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::loginView(static function () {
            return view('auth.login');
        });

        Fortify::authenticateThrough(static function () {
            return array_filter([
                config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
                config('fortify.lowercase_usernames') ? CanonicalizeUsername::class : null,
                EnsureUserIsActive::class,
                RedirectToTwoFactorAuthentication::class,
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ]);
        });
    }
}

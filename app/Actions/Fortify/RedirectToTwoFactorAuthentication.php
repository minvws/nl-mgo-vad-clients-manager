<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;
use Symfony\Component\HttpFoundation\Response;

use function assert;
use function config;
use function event;
use function method_exists;
use function redirect;
use function tap;
use function trans;

class RedirectToTwoFactorAuthentication
{
    protected StatefulGuard $guard;
    protected LoginRateLimiter $limiter;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(StatefulGuard $guard, LoginRateLimiter $limiter)
    {
        $this->guard = $guard;
        $this->limiter = $limiter;
    }

    /**
     * Handle the incoming request.
     *
     * @throws ValidationException
     */
    public function handle(Request $request): Response
    {
        $user = $this->validateCredentials($request);

        assert($user instanceof User);

        if (!$user->two_factor_secret) {
            $this->fireFailedEvent($request);

            $this->throwFailedAuthenticationException($request);
        }

        return $this->twoFactorChallengeResponse($request, $user);
    }

    /**
     * Attempt to validate the incoming credentials.
     *
     * @throws ValidationException
     */
    protected function validateCredentials(Request $request): mixed
    {
        // @phpstan-ignore method.notFound
        $model = $this->guard->getProvider()->getModel();

        return tap(
            $model::where(Fortify::username(), $request->{Fortify::username()})->first(),
            function ($user) use ($request): void {
                if (
                    !$user ||
                    !$this->guard->getProvider()->validateCredentials($user, ['password' => $request->password])
                ) {
                    $this->fireFailedEvent($request, $user);

                    $this->throwFailedAuthenticationException($request);
                }

                if (
                    config('hashing.rehash_on_login', true) &&
                    method_exists($this->guard->getProvider(), 'rehashPasswordIfRequired')
                ) {
                    $this->guard->getProvider()->rehashPasswordIfRequired($user, ['password' => $request->password]);
                }
            },
        );
    }

    /**
     * Throw a failed authentication validation exception.
     *
     * @throws ValidationException
     */
    protected function throwFailedAuthenticationException(Request $request): void
    {
        $this->limiter->increment($request);

        throw ValidationException::withMessages([
            Fortify::username() => [trans('authentication.login_failed')],
        ]);
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     */
    protected function fireFailedEvent(Request $request, ?Authenticatable $user = null): void
    {
        event(new Failed($this->guard?->name ?? config('fortify.guard'), $user, [
            Fortify::username() => $request->{Fortify::username()},
            'password' => $request->password,
        ]));
    }

    /**
     * Get the two factor authentication enabled response.
     */
    protected function twoFactorChallengeResponse(Request $request, User $user): Response
    {
        $request->session()->put([
            'login.id' => $user->getKey(),
            'login.remember' => $request->boolean('remember'),
        ]);

        TwoFactorAuthenticationChallenged::dispatch($user);

        return redirect()->route('two-factor.login');
    }
}

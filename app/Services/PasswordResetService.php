<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\Logging\UserPasswordResetEvent;
use App\Http\Dtos\User\StorePasswordRequestDto;
use App\Models\User;
use App\Support\I18n;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use MinVWS\Logging\Laravel\LogService;
use Webmozart\Assert\Assert;

use function decrypt;
use function event;
use function is_string;
use function now;

class PasswordResetService
{
    public function __construct(
        private readonly TwoFactorAuthenticationProvider $provider,
        private readonly LogService $logger,
    ) {
    }

    public function resetPassword(StorePasswordRequestDto $dto): string
    {
        $resetStatus = Password::reset(
            [
                'email' => $dto->email,
                'password' => $dto->password,
                'password_confirmation' => $dto->password_confirmation,
                'token' => $dto->token,
            ],
            function (User $user) use ($dto): void {
                $this->verify2FACode($user, $dto->two_factor_code);
                $this->updateUserPassword($user, $dto->password);
                $this->logPasswordReset($user);
            },
        );

        Assert::string($resetStatus);
        return $resetStatus;
    }

    /**
     * @throws ValidationException If 2FA verification fails
     */
    private function verify2FACode(User $user, string $code): void
    {
        $secret = decrypt($user->two_factor_secret);

        if (!is_string($secret) || empty($user->two_factor_secret) || !$this->provider->verify($secret, $code)) {
            throw ValidationException::withMessages([
                'two_factor_code' => [
                    I18n::trans('authentication.forgot_password.reset_error'),
                ],
            ]);
        }
    }

    private function updateUserPassword(User $user, string $password): void
    {
        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
            'registered_at' => now(),
            'two_factor_confirmed_at' => now(),
            'registration_token' => null,
        ])->save();
    }

    private function logPasswordReset(User $user): void
    {
        $this->logger->log((new UserPasswordResetEvent())
            ->withActor($user)
            ->withData([
                'userId' => $user->id,
            ]));

        event(new PasswordReset($user));
    }
}

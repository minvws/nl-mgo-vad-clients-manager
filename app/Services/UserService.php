<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Role;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use App\Notifications\Auth\UserRegistered;
use App\Notifications\Auth\UserReset;
use App\Support\SessionHelper;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use TypeError;
use Webmozart\Assert\Assert;

use function config;
use function encrypt;
use function hash;
use function now;

class UserService
{
    public function __construct(
        protected TwoFactorAuthenticationProvider $twoFactorAuthenticationProvider,
        protected Encrypter $encrypter,
        protected SessionHelper $sessionHelper,
    ) {
    }

    /**
     * @param array<int, Role> $roles
     */
    public function createUser(string $name, string $email, array $roles = [Role::User], ?string $registrationToken = null): User
    {
        $registrationToken = $registrationToken ?? Str::random(32);

        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            "password" => Hash::make(Str::random(32)),
            'registration_token' => hash('sha256', $registrationToken),
            'active' => true,
        ]);

        $secret = $this->twoFactorAuthenticationProvider->generateSecretKey();

        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
        ]);

        foreach ($roles as $role) {
            $user->attachRole($role);
        }
        $user->save();

        $registerUrl = $this->generateRegistrationUrl($registrationToken);
        $user->notify(new UserRegistered($registerUrl));

        return $user;
    }

    /**
     * @throws TypeError
     */
    public function updateUser(UpdateRequest $request, User $user): User
    {
        $dto = $request->getValidatedDto();
        $user->roles()->detach();

        $user->update([
            'name' => $dto->name,
            'email' => $dto->email,
        ]);

        foreach ($dto->roles as $role) {
            $user->attachRole(Role::from($role));
        }

        $user->save();

        $this->sessionHelper->invalidateUser($user->id);

        return $user;
    }

    public function resetUser(User $user): void
    {
        $registrationToken = Str::random(32);

        $user->password = Hash::make(Str::random(32));
        $user->registration_token = hash('sha256', $registrationToken);

        $secret = $this->twoFactorAuthenticationProvider->generateSecretKey();

        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
        ]);

        $user->save();

        $registerUrl = $this->generateRegistrationUrl($registrationToken);
        $user->notify(new UserReset($registerUrl));
    }

    public function deleteUser(User $user): void
    {
        $user->roles()->detach();
        $user->delete();
    }

    public function generateRegistrationUrl(string $registrationToken, ?int $expirationTtl = null): string
    {
        $expirationTtl = $expirationTtl ?? $this->getDefaultExpirationTtl();
        $expiration = now()->addMinutes($expirationTtl);

        return URL::temporarySignedRoute(
            name: 'register-with-token',
            expiration: $expiration,
            parameters: [
                'token' => $registrationToken,
            ],
        );
    }

    public function generateSignedPasswordResetUrl(
        string $resetToken,
        string $email,
        ?int $expirationTtl = null,
    ): string {
        $expirationTtl = $expirationTtl ?? $this->getDefaultExpirationTtl();
        $expiration = now()->addMinutes($expirationTtl);

        return URL::temporarySignedRoute(
            name: 'password.reset',
            expiration: $expiration,
            parameters: [
                'token' => $resetToken,
                'email' => $email,
            ],
        );
    }

    public function getDefaultExpirationTtl(): int
    {
        $expirationTtl = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        Assert::integer($expirationTtl);

        return $expirationTtl;
    }
}

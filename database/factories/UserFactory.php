<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

use function app;
use function encrypt;
use function fake;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $twoFactorAuthenticationProvider = app(TwoFactorAuthenticationProvider::class);

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'two_factor_secret' => encrypt($twoFactorAuthenticationProvider->generateSecretKey()),
            'two_factor_confirmed_at' => Carbon::now(),
            'registered_at' => Carbon::now(),
            'active' => true,
        ];
    }

    /**
     * @return Factory<User>
     */
    public function activated(): Factory
    {
        return $this->state(function () {
            return [
                'active' => true,
            ];
        });
    }
}

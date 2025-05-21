<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Models\User;
use App\Support\I18n;
use Illuminate\Support\Facades\App;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Mockery;
use Tests\TestCase;

use function encrypt;
use function fake;
use function route;

class TwoFactorAuthenticationControllerTest extends TestCase
{
    public function testReset2FA(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.2fa.reset'));
        $response->assertStatus(200);
        $response->assertViewIs('profile.2fa.create');
        $response->assertViewHas('qrCode');
        $response->assertViewHas('encryptedSecret');
    }

    public function testConfirm2FAResetWithValidCode(): void
    {
        $secretKey = fake()->word();
        $twoFactorProvider = Mockery::mock(TwoFactorAuthenticationProvider::class);
        $twoFactorProvider->shouldReceive('generateSecretKey')->andReturn('test-secret-key');
        $twoFactorProvider->shouldReceive('verify')->with($secretKey, '123456')->andReturn(true);
        App::instance(TwoFactorAuthenticationProvider::class, $twoFactorProvider);

        $user = User::factory()->create();
        $initialTwoFactorSecret = $user->two_factor_secret;
        $encryptedSecret = encrypt($secretKey);

        $response = $this->actingAs($user)->post(route('profile.2fa.confirm'), [
            'code' => '123456',
            'encrypted_secret' => $encryptedSecret,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertNotEquals($initialTwoFactorSecret, $user->refresh()->two_factor_secret);
        $this->assertEquals($encryptedSecret, $user->two_factor_secret);
    }

    public function testConfirm2FAResetWithNonDecryptableValue(): void
    {
        $user = User::factory()->create();
        $initialTwoFactorSecret = $user->two_factor_secret;
        $encryptedSecret = fake()->word();
        $response = $this->actingAs($user)
            ->post(route('profile.2fa.confirm'), [
                'code' => (string) fake()->numberBetween(100_000, 999_999),
                'encrypted_secret' => $encryptedSecret,
            ]);

        $response
            ->assertRedirect(route('profile.2fa.reset', ['encrypted_secret' => $encryptedSecret]))
            ->assertSessionHas(
                FlashNotification::SESSION_KEY,
                function (FlashNotification $flashMessage) {
                    return $flashMessage->getMessage() === I18n::trans(
                        'profile.2fa.non_decryptable_secret',
                    ) && $flashMessage->getType() === FlashNotificationTypeEnum::ERROR;
                },
            );

        $this->assertEquals($initialTwoFactorSecret, $user->refresh()->two_factor_secret);
    }

    public function testConfirm2FAResetWithInvalidCode(): void
    {
        $fakeSecretKey = fake()->word();
        $twoFactorProvider = Mockery::mock(TwoFactorAuthenticationProvider::class);
        $twoFactorProvider->shouldReceive('generateSecretKey')->andReturn($fakeSecretKey);
        $twoFactorProvider->shouldReceive('verify')->with($fakeSecretKey, '000000')->andReturn(false);
        App::instance(TwoFactorAuthenticationProvider::class, $twoFactorProvider);

        $user = $this->login();
        $encryptedSecret = encrypt($fakeSecretKey);
        $response = $this->actingAs($user)->post(route('profile.2fa.confirm'), [
            'code' => '000000',
            'encrypted_secret' => $encryptedSecret,
        ]);

        $response
            ->assertRedirect(route('profile.2fa.reset', ['encrypted_secret' => $encryptedSecret]))
            ->assertSessionHas(
                FlashNotification::SESSION_KEY,
                function (FlashNotification $flashMessage) {
                    return $flashMessage->getMessage() === I18n::trans(
                        'profile.2fa.invalid',
                    ) && $flashMessage->getType() === FlashNotificationTypeEnum::ERROR;
                },
            );
    }

    public function testConfirm2FAResetWithFailedFirstAttemptUsesSameCodeToPreventRescanning(): void
    {
        $fakeSecretKey = fake()->word();
        $twoFactorProvider = Mockery::mock(TwoFactorAuthenticationProvider::class);
        $twoFactorProvider->shouldReceive('generateSecretKey')->andReturn($fakeSecretKey);
        $twoFactorProvider->shouldReceive('verify')->with($fakeSecretKey, '000000')->andReturn(false);
        $twoFactorProvider->shouldReceive('verify')->with($fakeSecretKey, '123456')->andReturn(true);
        App::instance(TwoFactorAuthenticationProvider::class, $twoFactorProvider);

        $user = $this->login();
        $initialTwoFactorSecret = $user->two_factor_secret;
        $encryptedSecret = encrypt($fakeSecretKey);
        $response = $this->actingAs($user)->post(route('profile.2fa.confirm'), [
            'code' => '000000',
            'encrypted_secret' => $encryptedSecret,
        ]);

        $response
            ->assertRedirect(route('profile.2fa.reset', ['encrypted_secret' => $encryptedSecret]))
            ->assertSessionHas(
                FlashNotification::SESSION_KEY,
                function (FlashNotification $flashMessage) {
                    return $flashMessage->getMessage() === I18n::trans(
                        'profile.2fa.invalid',
                    ) && $flashMessage->getType() === FlashNotificationTypeEnum::ERROR;
                },
            );


        $secondResponse = $this->actingAs($user)->post(route('profile.2fa.confirm'), [
            'code' => '123456',
            'encrypted_secret' => $encryptedSecret,
        ]);

        $secondResponse->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertNotEquals($user->two_factor_secret, $initialTwoFactorSecret);
        $this->assertEquals($encryptedSecret, $user->two_factor_secret);
    }
}

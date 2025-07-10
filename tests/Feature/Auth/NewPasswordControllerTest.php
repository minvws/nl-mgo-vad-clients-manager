<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Models\User;
use App\Notifications\Auth\UserPasswordReset;
use App\Services\PasswordResetService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Mockery;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

use function decrypt;
use function is_string;
use function route;

class NewPasswordControllerTest extends TestCase
{
    private User $user;
    private string $token;
    private string $validPassword = 'M@ke1Compl!cated#Passw0rd';
    private string $validOtp;

    public function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->user = User::factory()->create();
        $this->post('/forgot-password', ['email' => $this->user->email]);

        Notification::assertSentTo($this->user, UserPasswordReset::class, function ($notification) {
            $this->token = $notification->token;

            $secret = decrypt($this->user->two_factor_secret);
            if (is_string($secret)) {
                $engine = new Google2FA();
                $this->validOtp = $engine->getCurrentOtp($secret);
            }

            return true;
        });
    }

    public function testPasswordResetSuccessWithValidData(): void
    {
        $response = $this->post('/reset-password', [
            'token' => $this->token,
            'email' => $this->user->email,
            'password' => $this->validPassword,
            'password_confirmation' => $this->validPassword,
            'two_factor_code' => $this->validOtp,
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('login'))
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: 'authentication.forgot_password.reset_success',
            ));

        $this->user->refresh();
        $this->assertNotNull($this->user->two_factor_confirmed_at);
        $this->assertNull($this->user->registration_token);
    }

    public function testPasswordResetFailsWithInvalidToken(): void
    {
        $response = $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => $this->user->email,
            'password' => $this->validPassword,
            'password_confirmation' => $this->validPassword,
            'two_factor_code' => $this->validOtp,
        ]);

        $response->assertStatus(302)
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'authentication.forgot_password.reset_error',
            ));
    }

    public function testPasswordResetFailsWithInvalidEmail(): void
    {
        $response = $this->post('/reset-password', [
            'token' => $this->token,
            'email' => 'wrong@example.com',
            'password' => $this->validPassword,
            'password_confirmation' => $this->validPassword,
            'two_factor_code' => $this->validOtp,
        ]);

        $response->assertStatus(302)
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'authentication.forgot_password.reset_error',
            ));
    }

    public function testPasswordResetFailsWithWeakPassword(): void
    {
        $response = $this->post('/reset-password', [
            'token' => $this->token,
            'email' => $this->user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'two_factor_code' => $this->validOtp,
        ]);

        $response->assertSessionHasErrors(['password'])
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'authentication.forgot_password.reset_error',
            ));
    }

    public function testPasswordResetFailsWithMismatchedPasswords(): void
    {
        $response = $this->post('/reset-password', [
            'token' => $this->token,
            'email' => $this->user->email,
            'password' => $this->validPassword,
            'password_confirmation' => 'DifferentPassword123!',
            'two_factor_code' => $this->validOtp,
        ]);

        $response->assertSessionHasErrors(['password'])
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'authentication.forgot_password.reset_error',
            ));
    }

    public function testPasswordResetFailsWithInvalid2FACode(): void
    {
        $response = $this->post('/reset-password', [
            'token' => $this->token,
            'email' => $this->user->email,
            'password' => $this->validPassword,
            'password_confirmation' => $this->validPassword,
            'two_factor_code' => '123456', // Invalid code
        ]);

        $response->assertStatus(302)
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'authentication.forgot_password.reset_error',
            ));
    }

    public function testWithServiceMockedToFailValidation(): void
    {
        $mockService = Mockery::mock(PasswordResetService::class);
        $mockService->shouldReceive('resetPassword')
            ->andThrow(ValidationException::withMessages([
                'two_factor_code' => ['Invalid code'],
            ]));
        $this->app->instance(PasswordResetService::class, $mockService);

        $response = $this->post('/reset-password', [
            'token' => $this->token,
            'email' => $this->user->email,
            'password' => $this->validPassword,
            'password_confirmation' => $this->validPassword,
            'two_factor_code' => $this->validOtp,
        ]);

        $response->assertStatus(302)
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'authentication.forgot_password.reset_error',
            ));
    }
}

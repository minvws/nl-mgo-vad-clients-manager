<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Models\User;
use App\Notifications\Auth\UserPasswordReset;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

use function app;
use function assert;
use function decrypt;
use function is_string;
use function route;
use function trans;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function testResetPasswordLinkScreenCanBeRendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function testResetPasswordLinkCanBeRequested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo(
            $user,
            UserPasswordReset::class,
            function (UserPasswordReset $notification) use ($user) {
                $this->assertNotNull($notification->token);

                $mailMessage = $notification->toMail($user);

                $this->assertSame(trans('user.mail.password_reset.subject'), $mailMessage->subject);
                $this->assertSame(trans('general.mail.greeting', ['name' => $user->name]), $mailMessage->greeting);

                return true;
            },
        );
    }

    public function testResetPasswordScreenCanBeRendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, UserPasswordReset::class, function ($notification) use ($user) {
            $userService = app(UserService::class);
            $resetUrl = $userService->generateSignedPasswordResetUrl(
                $notification->token,
                $user->email,
            );

            $response = $this->get($resetUrl);

            $response->assertStatus(200);

            return true;
        });
    }

    public function testPasswordCanBeResetWithValidToken(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, UserPasswordReset::class, function ($notification) use ($user) {
            $engine = new Google2FA();

            $currentOtp = decrypt($user->two_factor_secret);
            assert(is_string($currentOtp));

            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'M@ke1Compl!cated#Passw0rd',
                'password_confirmation' => 'M@ke1Compl!cated#Passw0rd',
                'two_factor_code' => $engine->getCurrentOtp($currentOtp),
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }

    public function testPasswordValidatePasswordRules(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, UserPasswordReset::class, function ($notification) use ($user) {
            $engine = new Google2FA();

            $currentOtp = decrypt($user->two_factor_secret);
            assert(is_string($currentOtp));

            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
                'two_factor_code' => $engine->getCurrentOtp($currentOtp),
            ]);

            $response
                ->assertSessionHasErrors(['password'])
                ->assertSessionHas('flash_notification', new FlashNotification(
                    type: FlashNotificationTypeEnum::ERROR,
                    message: 'authentication.forgot_password.reset_error',
                ));

            return true;
        });
    }

    public function testPasswordValidatePasswordsShouldMatch(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, UserPasswordReset::class, function ($notification) use ($user) {
            $engine = new Google2FA();

            $currentOtp = decrypt($user->two_factor_secret);
            assert(is_string($currentOtp));

            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'passwordA',
                'password_confirmation' => 'passwordB',
                'two_factor_code' => $engine->getCurrentOtp($currentOtp),
            ]);

            $response
                ->assertSessionHasErrors(['password'])
                ->assertSessionHas('flash_notification', new FlashNotification(
                    type: FlashNotificationTypeEnum::ERROR,
                    message: 'authentication.forgot_password.reset_error',
                ));

            return true;
        });
    }

    public function testPasswordValidateNewTwoFACode(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, UserPasswordReset::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'M@ke1Compl!cated#Passw0rd',
                'password_confirmation' => 'M@ke1Compl!cated#Passw0rd',
                'two_factor_code' => '123',
            ]);

            $response->assertSessionHasErrors(['two_factor_code']);

            return true;
        });
    }
}

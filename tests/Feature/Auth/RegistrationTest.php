<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

use function app;
use function assert;
use function decrypt;
use function hash;
use function is_string;
use function now;
use function route;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function testRegistrationScreenCanBeRendered(): void
    {
        $registrationToken = Str::random(32);

        User::factory()->create([
            'registration_token' => hash('sha256', $registrationToken),
        ]);

        $userService = app(UserService::class);
        $registrationUrl = $userService->generateRegistrationUrl($registrationToken);

        $response = $this->get($registrationUrl);

        $response->assertStatus(200);
    }

    public function testNewUsersCanRegister(): void
    {
        $registrationToken = Str::random(32);

        $user = User::factory()->create([
            'registration_token' => hash('sha256', $registrationToken),
        ]);

        $engine = new Google2FA();

        $secret = decrypt($user->two_factor_secret);
        assert(is_string($secret));

        $response = $this->post('/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'This@Is@A@New@Password#1245',
            'password_confirmation' => 'This@Is@A@New@Password#1245',
            'two_factor_code' => $engine->getCurrentOtp($secret),
            'token' => $registrationToken,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function testNewUsersShouldUseValidPassword(): void
    {
        $registrationToken = Str::random(32);

        $user = User::factory()->create([
            'registration_token' => hash('sha256', $registrationToken),
        ]);

        $engine = new Google2FA();

        $secret = decrypt($user->two_factor_secret);
        assert(is_string($secret));

        $response = $this->post('/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'two_factor_code' => $engine->getCurrentOtp($secret),
            'token' => $registrationToken,
        ]);

        $this->assertGuest();

        $response
            ->assertSessionHasErrors()
            ->assertSessionHasErrors(['password'])
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'user.flash.register_error',
            ));
    }

    public function testNewUsersShouldUseValid2FAToken(): void
    {
        $registrationToken = Str::random(32);

        $user = User::factory()->create([
            'registration_token' => hash('sha256', $registrationToken),
        ]);

        $response = $this->post('/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'This@Is@A@New@Password#1245',
            'password_confirmation' => 'This@Is@A@New@Password#1245',
            'two_factor_code' => '123',
            'token' => $registrationToken,
        ]);

        $this->assertGuest();

        $response
            ->assertSessionHasErrors()
            ->assertSessionHasErrors(['two_factor_code'])
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'user.flash.register_error',
            ));
    }

    public function testNewUsersShouldUseValid2FASecret(): void
    {
        $registrationToken = Str::random(32);

        $user = User::factory()->create([
            'registration_token' => hash('sha256', $registrationToken),
            'two_factor_secret' => null,
        ]);

        $response = $this->post('/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'This@Is@A@New@Password#1245',
            'password_confirmation' => 'This@Is@A@New@Password#1245',
            'two_factor_code' => '123',
            'token' => $registrationToken,
        ]);

        $this->assertGuest();

        $response
            ->assertSessionHasErrors()
            ->assertSessionHasErrors(['two_factor_code'])
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'user.flash.register_error',
            ));
    }

    public function testRegistrationScreenDonTRenderWithoutToken(): void
    {
        $response = $this->get('/register-with-token');

        $response->assertStatus(404);
    }

    public function testRegistrationScreenDonTRenderWithoutValidToken(): void
    {
        $userService = app(UserService::class);
        $registrationUrl = $userService->generateRegistrationUrl('invalid-token');

        $response = $this->get($registrationUrl);

        $response->assertStatus(404);
    }

    public function testRegistrationScreenDonTRenderWithAlreadyRegisteredUser(): void
    {
        $registrationToken = Str::random(32);

        User::factory()->create([
            'registered_at' => now(),
        ]);

        $userService = app(UserService::class);
        $registrationUrl = $userService->generateRegistrationUrl($registrationToken);

        $response = $this->get($registrationUrl);

        $response->assertStatus(404);
    }
}

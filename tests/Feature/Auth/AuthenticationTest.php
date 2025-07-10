<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Event;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

use function bcrypt;
use function decrypt;
use function route;
use function trans;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginScreenCanBeRendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function testUsersCanAuthenticateUsingTheLoginScreen(): void
    {
        $engine = new Google2FA();
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(302)
            ->assertRedirect('/two-factor-challenge');

        $response = $this
            ->post('/two-factor-challenge', [
                'code' => $engine->getCurrentOtp(decrypt($user->two_factor_secret)),
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function testUsersCanNotAuthenticateWithInvalidPassword(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function testUsersCanLogout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function testUsersCannotAuthenticateUsingTheWrongOtp(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(302)
            ->assertRedirect('/two-factor-challenge');

        $response = $this
            ->post('/two-factor-challenge', [
                'code' => 'wrong-otp',
            ]);

        $this->assertGuest();
        $response->assertRedirect('/two-factor-challenge');
    }

    public function testUsersCannotAuthenticateWithoutTwoFactorSecret(): void
    {
        Event::fake();
        $user = User::factory()->create(['two_factor_secret' => null]);

        $this
            ->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ])
            ->assertStatus(302)
            ->assertRedirect('/');

        $this->assertGuest();
        Event::assertDispatched(Failed::class);
    }

    public function testEnsureIsRateLimited(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->expectException(ThrottleRequestsException::class);

        // Simulate too many login attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $this
            ->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ])
            ->assertStatus(429)
            ->assertJson([
                'message' => trans('authentication.ratelimited', ['seconds' => 60]),
            ]);
    }

    public function testUserCannotLoginWhenUserIsInactive(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'active' => false,
        ]);

        $user->markAsRegistered();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $this->assertGuest();
    }

    public function testUserCanLoginWhenUserIsActive(): void
    {
        $engine = new Google2FA();
        $user = User::factory()->create([
            'active' => true,
        ]);

        $user->markAsRegistered();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(302)
            ->assertRedirect('/two-factor-challenge');

        $response = $this
            ->post('/two-factor-challenge', [
                'code' => $engine->getCurrentOtp(decrypt($user->two_factor_secret)),
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function testUserAccessIsRevokedWhenDeactivatedDuringSession(): void
    {
        $user = $this->login();

        $this->assertAuthenticated();

        $this->get('/dashboard')
            ->assertStatus(200);

        $user->update([
            'active' => false,
        ]);

        $this->get('/dashboard')
            ->assertRedirect(route('login'))
            ->assertInvalid('email');

        $this->assertGuest();
    }
}

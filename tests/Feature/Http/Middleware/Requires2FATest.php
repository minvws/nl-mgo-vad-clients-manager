<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function request;
use function route;

class Requires2FATest extends TestCase
{
    use RefreshDatabase;

    public function testUserWith2FAEnabledCanAccess(): void
    {
        $user = User::factory()->create(['two_factor_secret' => 'secret']);
        $user->attachRole(Role::User);
        $user->markAsRegistered();

        Auth::login($user);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
    }

    public function testUserWithout2FACannotAccess(): void
    {
        $user = User::factory()->create(['two_factor_secret' => null]);
        $user->attachRole(Role::User);
        $user->markAsRegistered();

        Auth::login($user);

        Log::shouldReceive('alert')
            ->once()
            ->with('User without 2FA attempting usage', [
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
            ]);

        $response = $this->get('/dashboard');

        $response->assertStatus(403);
        $response->assertSee('No 2FA options available for user');
    }

    public function testUnauthenticatedUserCannotAccess(): void
    {
        $this->get('/dashboard')
            ->assertRedirect(route('login'));
    }
}

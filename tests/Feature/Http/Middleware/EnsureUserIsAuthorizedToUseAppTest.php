<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\EnsureUserIsAuthorizedToUseApp;
use App\Models\User;
use App\Support\I18n;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

use function now;
use function response;
use function route;

class EnsureUserIsAuthorizedToUseAppTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(EnsureUserIsAuthorizedToUseApp::class)
            ->get('/middleware-test', fn() => response('Next middleware called'));
    }

    public static function unauthorizedUserProvider(): array
    {
        return [
            'guest' => [null],
            'notRegistered' => [
                [
                    'registered_at' => null,
                    'two_factor_confirmed_at' => now(),
                ]],
            'noTwoFactor' => [
                [
                    'registered_at' => now(),
                    'two_factor_confirmed_at' => null,
                    'registration_token' => null,
                ]],
        ];
    }

    /**
     * @dataProvider unauthorizedUserProvider
     */
    public function testUnauthorizedUser(array|null $userAttrs): void
    {
        if ($userAttrs !== null) {
            $user = User::factory()->create($userAttrs);
            $this->actingAs($user);
        }

        $response = $this->get('/middleware-test');
        $response->assertStatus(401);
    }

    public function testInactiveUserIsRedirectedToLogin(): void
    {
        $user = User::factory()->create([
            'registered_at' => now(),
            'two_factor_confirmed_at' => now(),
            'active' => false,
        ]);
        $user->markAsRegistered();
        $response = $this->actingAs($user)->get('/middleware-test');

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => I18n::trans('authentication.inactive'),
            ]);
    }

    public function testActiveUserWith2faAndRegisteredPasses(): void
    {
        $user = User::factory()->create([
            'registered_at' => now(),
            'two_factor_confirmed_at' => now(),
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get('/middleware-test');

        $response
            ->assertOk()
            ->assertSeeText('Next middleware called');
    }
}

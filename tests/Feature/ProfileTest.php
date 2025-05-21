<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function now;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function testProfilePageIsDisplayed(): void
    {
        $user = User::factory()
            ->create([
                'two_factor_confirmed_at' => now(),
                'registered_at' => now(),
            ]);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function testProfileInformationCanBeUpdated(): void
    {
        $user = User::factory()->create([
            'two_factor_confirmed_at' => now(),
            'registered_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
    }

    public function testUserCanNotDeleteTheirAccount(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertStatus(405);
    }
}

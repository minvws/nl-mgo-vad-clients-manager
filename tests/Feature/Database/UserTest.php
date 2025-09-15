<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use App\Models\User;
use Illuminate\Database\QueryException;
use Tests\TestCase;

use function fake;
use function now;

class UserTest extends TestCase
{
    public function testUserCannotBeCreatedWithExistingEmail(): void
    {
        $email = fake()->safeEmail();
        User::factory()->create(['email' => $email, 'deleted_at' => null]);
        $this->expectException(QueryException::class);
        $newUser = User::factory()->make(['email' => $email, 'deleted_at' => null]);
        $newUser->save();

        $this->assertDatabaseCount('users', 1);
        $this->assertFalse($newUser->wasRecentlyCreated, 'User with existing email should not be created');
    }

    public function testUserCanBeCreatedWithEmailThatExistsForDeletedUser(): void
    {
        $email = fake()->safeEmail();
        User::factory()->create(['email' => $email, 'deleted_at' => now()]);
        $newUser = User::factory()->make(['email' => $email, 'deleted_at' => null]);
        $newUser->save();

        $this->assertDatabaseCount('users', 2);
        $this->assertTrue($newUser->wasRecentlyCreated, 'User with previously deleted email should be created');
    }
}

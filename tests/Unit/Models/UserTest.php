<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testMarkAsRegistered(): void
    {
        $user = User::factory()->create();

        $user->markAsRegistered();

        $this->assertNotNull($user->registered_at);
        $this->assertNull($user->registration_token);
        $this->assertNotNull($user->two_factor_confirmed_at);
    }

    public function testGetAuditData(): void
    {
        $user = User::factory()->create();

        $this->assertSame($user->id, $user->getAuditId());
        $this->assertSame($user->name, $user->getName());
        $this->assertSame($user->email, $user->getEmail());
    }

    public function testUserIsAdministrator(): void
    {
        $user = User::factory()->create();
        $user->attachRole(Role::UserAdmin);
        $user->save();

        $this->assertCount(1, $user->getRoles());
        $this->assertTrue($user->isUserAdministrator());
        $this->assertSame($user->getRoles()[0], Role::UserAdmin);
    }

    public function testNoDuplicateRoles(): void
    {
        $user = User::factory()->create();

        $user->attachRole(Role::User);
        $user->attachRole(Role::User);
        $user->save();

        $this->assertCount(1, $user->roles);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Policies;

use App\Enums\Role;
use App\Models\User;
use App\Policies\UserPolicy;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    public function testChangeActiveStatusAllowsAdminToDeactivateNonAdmin(): void
    {
        $admin = User::factory()->create();
        $admin->attachRole(Role::UserAdmin);

        $nonAdmin = User::factory()->create();

        $this->assertTrue((new UserPolicy())->changeActiveStatus($admin, $nonAdmin));
    }

    public function testChangeActiveStatusDeniesAdminToDeactivateAnotherAdmin(): void
    {
        $admin1 = User::factory()->create();
        $admin1->attachRole(Role::UserAdmin);

        $admin2 = User::factory()->create();
        $admin2->attachRole(Role::UserAdmin);

        $this->assertFalse((new UserPolicy())->changeActiveStatus($admin1, $admin2));
    }

    public function testChangeActiveStatusDeniesUserToDeactivateSelf(): void
    {
        $user = User::factory()->create();
        $user->attachRole(Role::UserAdmin);

        $this->assertFalse((new UserPolicy())->changeActiveStatus($user, $user));
    }

    public function testChangeActiveStatusDeniesNonAdminToDeactivateAnotherUser(): void
    {
        $user1 = User::factory()->create();
        $user1->attachRole(Role::User);

        $user2 = User::factory()->create();
        $user2->attachRole(Role::User);

        $this->assertFalse((new UserPolicy())->changeActiveStatus($user1, $user2));
    }
}

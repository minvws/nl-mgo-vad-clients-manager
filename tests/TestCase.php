<?php

declare(strict_types=1);

namespace Tests;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function login(Role $role = Role::UserAdmin): User
    {
        $adminUser = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@exmple.com',
        ]);

        $adminUser->attachRole($role);
        $adminUser->markAsRegistered();
        $adminUser->save();

        $this->actingAs($adminUser);

        return $adminUser;
    }
}

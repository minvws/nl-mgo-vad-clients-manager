<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Client;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Seeder;

use function bcrypt;
use function encrypt;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->createOne([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'two_factor_secret' => encrypt("super-secret-2fa"),
        ]);

        $user->markAsRegistered();
        $user->attachRole(Role::UserAdmin);

        $user->save();

        $organisation = Organisation::factory()->createOne();

        Client::factory()->for($organisation)->createOne();
    }
}

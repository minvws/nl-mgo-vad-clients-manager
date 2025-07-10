<?php

declare(strict_types=1);

namespace Tests\Feature\Commands;

use App\Enums\Role as RoleEnum;
use App\Models\User;
use App\Notifications\Auth\UserRegistered;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

use function sprintf;

class UserAdminCreateCommandTest extends TestCase
{
    #[Test]
    public function testSuccessfulCommand(): void
    {
        $email = $this->faker->email;
        $name = $this->faker->name;
        $output = sprintf("User admin %s created. Please share the following URL to let the user make his registration complete:", $email);

        $this->artisan('user:create-admin')
            ->expectsQuestion('What is the email?', $email)
            ->expectsQuestion('What is the name?', $name)
            ->expectsOutput($output)
            ->assertSuccessful();

        $this->assertDatabaseHas(User::class, [
            'name' => $name,
            'email' => $email,
        ]);

        $userId = User::where('email', $email)->firstOrFail()->id;
        $this->assertDatabaseHas('role_user', [
            'user_id' => $userId,
            'role_name' => RoleEnum::UserAdmin,
        ]);
    }

    #[Test]
    public function testSuccessfulCommandWithMail(): void
    {
        Notification::fake();
        $email = $this->faker->email;
        $name = $this->faker->name;

        $this->artisan('user:create-admin --sendMail')
            ->expectsQuestion('What is the email?', $email)
            ->expectsQuestion('What is the name?', $name)
            ->assertSuccessful();

        $this->assertDatabaseHas(User::class, [
            'name' => $name,
            'email' => $email,
        ]);

        $user = User::where('email', $email)->firstOrFail();
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_name' => RoleEnum::UserAdmin,
        ]);

        Notification::assertSentTo($user, UserRegistered::class);
    }
}

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
        $user = User::factory()->create();

        $output = sprintf(
            "User admin %s created. Please share the following URL to let the user make his registration complete:",
            $user->email,
        );

        $this->artisan('user:create-admin')
            ->expectsQuestion('What is the email?', $user->email)
            ->expectsQuestion('What is the name?', $user->name)
            ->expectsOutput($output)
            ->assertSuccessful();

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_name' => RoleEnum::UserAdmin,
        ]);

        $this->assertDatabaseHas(User::class, [
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }

    #[Test]
    public function testSuccessfulCommandWithMail(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->artisan('user:create-admin --sendMail')
            ->expectsQuestion('What is the email?', $user->email)
            ->expectsQuestion('What is the name?', $user->name)
            ->assertSuccessful();

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_name' => RoleEnum::UserAdmin,
        ]);

        $this->assertDatabaseHas(User::class, [
            'id' => $user->id,
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, UserRegistered::class);
    }
}

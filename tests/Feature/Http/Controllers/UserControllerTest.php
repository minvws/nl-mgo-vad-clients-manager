<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Enums\Role;
use App\Models\User;
use App\Notifications\Auth\UserReset;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

use function assert;
use function now;
use function route;
use function sprintf;
use function str_contains;
use function trans;

// @phpcs:disable Generic.Files.LineLength

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['/users'], 'user list')]
    #[TestWith(['/users/create'], 'user creation')]
    #[TestWith(['/users/{userId}'], 'user edit')]
    #[TestWith(['/users/reset/{userId}'], 'user reset')]
    #[TestWith(['/users/remove/{userId}'], 'user delete')]
    public function testAdminAllowedPageAccess(string $pageUrl): void
    {
        $this->login();

        if (str_contains($pageUrl, '{userId}')) {
            $user = User::factory()->create();
            $pageUrl = sprintf("/users/%s", $user->id);
        }

        $this
            ->get($pageUrl)
            ->assertStatus(200);
    }

    #[TestWith(['/users', 403], 'user list')]
    #[TestWith(['/users/create', 403], 'user creation')]
    #[TestWith(['/users/{userId}', 403], 'user edit')]
    #[TestWith(['/users/reset/{userId}', 403], 'user reset')]
    #[TestWith(['/users/remove/{userId}', 403], 'user delete')]
    public function testNonAdminNotAllowedPageAccess(string $pageUrl, int $errorCode): void
    {
        $user = User::factory()->create([
            'name' => 'NonAdmin',
            'email' => 'nonadmin@exmple.com',
        ]);
        $user->attachRole(Role::User);
        $user->markAsRegistered();

        if (str_contains($pageUrl, '{userId}')) {
            $subjectUser = User::factory()->create();
            $pageUrl = sprintf("/users/%s", $subjectUser->id);
        }

        $this
            ->actingAs($user)
            ->get($pageUrl)
            ->assertStatus($errorCode);
    }

    public function testUserFilter(): void
    {
        $adminUser = $this->getAdminUser();

        $user = User::factory()->create();
        $user->attachRole(Role::User);
        $user->markAsRegistered();
        $pageUrl = sprintf("/users?filter=%s", $user->email);

        $this
            ->actingAs($adminUser)
            ->get($pageUrl)
            ->assertStatus(200)
            ->assertSee($user->email);
    }

    public function testUserFilterAndSort(): void
    {
        $adminUser = $this->getAdminUser();

        $user = User::factory()->create();
        $user->attachRole(Role::User);
        $user->markAsRegistered();
        $pageUrl = sprintf("/users?filter=%s&sort=name&direction=desc", $user->email);

        $this
            ->actingAs($adminUser)
            ->get($pageUrl)
            ->assertStatus(200)
            ->assertSee($user->email);
    }

    #[TestWith([''], 'empty username')]
    #[TestWith([
        'padding-jiffy-hormone-hebrew-hayrick-parakeet-dubiety-pleat-empiric-forsook-virtuosi-realm-deserve-mores-3HXUHZWBWdZikt0t9NFvMzFBfABkuPMFsbwKAadGNt3vijunwetvKessp99fCoABWXCthxrnFP2kh04Fb3p0uVNkisQk7V3GXPHj-stalwart-brightly9scram_sailing8trout0jiffy3stumbled!tumors2DUTCH7claudius1zero2petals3visible!sanity*admirers2towing',
    ], 'long username')]
    public function testCreateUserShouldHaveValidName(string $username): void
    {
        $adminUser = $this->getAdminUser();

        $this
            ->actingAs($adminUser)
            ->get('/users/create')
            ->assertStatus(200);

        $this
            ->actingAs($adminUser)
            ->post('/users/create', [
                'name' => $username,
                'email' => 'john.doe@example.com',
                'roles' => [Role::User->value],
            ])
            ->assertRedirect('/users/create')
            ->assertSessionHasErrors()
            ->assertSessionHasErrors(['name'])
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'user.flash.created_error',
            ));
    }

    #[TestWith([''], 'empty email')]
    #[TestWith(['invalid'], 'invalid email')]
    public function testCreateUserShouldHaveValidEmail(string $emailAddress): void
    {
        $adminUser = $this->getAdminUser();

        $this
            ->actingAs($adminUser)
            ->get('/users/create')
            ->assertStatus(200);

        $this
            ->actingAs($adminUser)
            ->post('/users/create', [
                'name' => $this->faker()->name,
                'email' => $emailAddress,
                'roles' => [Role::User->value],
            ])
            ->assertRedirect('/users/create')
            ->assertSessionHasErrors()
            ->assertSessionHasErrors(['email'])
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'user.flash.created_error',
            ));
    }

    public function testCreateUserShouldHaveUniqueEmail(): void
    {
        $adminUser = $this->getAdminUser();

        $user = User::factory()->create();
        $user->attachRole(Role::User);
        $user->markAsRegistered();

        $this
            ->actingAs($adminUser)
            ->get('/users/create')
            ->assertStatus(200);

        $this
            ->actingAs($adminUser)
            ->post('/users/create', [
                'name' => $this->faker()->name(),
                'email' => $user->email,
                'roles' => [Role::User->value],
            ])
            ->assertRedirect('/users/create')
            ->assertSessionHasErrors()
            ->assertSessionHasErrors(['email'])
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'user.flash.created_error',
            ));
    }

    public function testUserCanBeUpdated(): void
    {
        $adminUser = $this->getAdminUser();

        $user = User::factory()->create();
        $user->attachRole(Role::User);
        $user->markAsRegistered();

        $this
            ->actingAs($adminUser)
            ->get(sprintf("/users/%s", $user->id))
            ->assertStatus(200);

        $this
            ->actingAs($adminUser)
            ->post(sprintf("/users/%s", $user->id), [
                'name' => 'Jane Doe',
                'email' => 'jane.doe@example.com',
                'roles' => [Role::User->value],
            ])
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
        ]);
    }

    public function testAdminUserCanBeCreated(): void
    {
        $adminUser = $this->getAdminUser();

        $this
            ->actingAs($adminUser)
            ->get('/users/create')
            ->assertStatus(200);

        $email = 'john.doe@example.com';
        $this
            ->actingAs($adminUser)
            ->post('/users/create', [
                'name' => 'John Doe',
                'email' => $email,
                'roles' => [Role::UserAdmin->value],
            ])
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => $email,
        ]);

        $userId = User::where('email', $email)->firstOrFail()->id;
        $this->assertDatabaseHas('role_user', [
            'user_id' => $userId,
            'role_name' => Role::UserAdmin->value,
        ]);
    }

    public function testUserCanBeCreatedWithMultipleRoles(): void
    {
        $adminUser = $this->getAdminUser();

        $this
            ->actingAs($adminUser)
            ->get('/users/create')
            ->assertStatus(200);

        $email = 'john.doe@example.com';
        $this
            ->actingAs($adminUser)
            ->post('/users/create', [
                'name' => 'John Doe',
                'email' => $email,
                'roles' => [Role::UserAdmin->value, Role::User->value],
            ])
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => $email,
        ]);

        $userId = User::where('email', $email)->firstOrFail()->id;
        $this->assertDatabaseHas('role_user', [
            'user_id' => $userId,
            'role_name' => Role::UserAdmin->value,
        ]);
        $this->assertDatabaseHas('role_user', [
            'user_id' => $userId,
            'role_name' => Role::User->value,
        ]);
    }

    public function testUserShouldExistBeforeUpdate(): void
    {
        $adminUser = $this->getAdminUser();

        $this
            ->actingAs($adminUser)
            ->get(sprintf("/users/%s", '1234'))
            ->assertStatus(404);
    }

    #[TestWith(['name', '', 'john.doe@example.com'], 'empty name')]
    #[TestWith(
        ['name', 'padding-jiffy-hormone-hebrew-hayrick-parakeet-dubiety-pleat-empiric-forsook-virtuosi-realm-deserve-mores-3HXUHZWBWdZikt0t9NFvMzFBfABkuPMFsbwKAadGNt3vijunwetvKessp99fCoABWXCthxrnFP2kh04Fb3p0uVNkisQk7V3GXPHj-stalwart-brightly9scram_sailing8trout0jiffy3stumbled!tumors2DUTCH7claudius1zero2petals3visible!sanity*admirers2towing', 'john.doe@example.com'],
        'long username',
    )]
    #[TestWith(['email', 'John Doe', ''], 'empty email')]
    #[TestWith(['email', 'John Doe', 'john'], 'invalid email')]
    public function testUserUpdatedShouldBeValid(string $field, string $name, string $emailAddress): void
    {
        $adminUser = $this->getAdminUser();

        $user = User::factory()->create();
        $user->attachRole(Role::User);
        $user->markAsRegistered();

        $this
            ->actingAs($adminUser)
            ->get(sprintf("/users/%s", $user->id))
            ->assertStatus(200);

        $this
            ->actingAs($adminUser)
            ->post(sprintf("/users/%s", $user->id), [
                'name' => $name,
                'email' => $emailAddress,
                'roles' => [Role::User->value],
            ])
            ->assertRedirect(sprintf("/users/%s", $user->id))
            ->assertSessionHasErrors()
            ->assertSessionHasErrors([$field])
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'user.flash.update_error',
            ));
    }

    public function testUserCanBeDeleted(): void
    {
        $adminUser = $this->getAdminUser();

        $user = User::factory()->create();

        $this
            ->actingAs($adminUser)
            ->get(sprintf("/users/remove/%s", $user->id))
            ->assertStatus(200);

        $this
            ->actingAs($adminUser)
            ->delete(sprintf("/users/remove/%s", $user->id))
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => now(),
        ]);
    }

    public function testUserCanBeDeactivated(): void
    {
        $adminUser = $this->getAdminUser();

        $user = User::factory()->create([
            'active' => true,
        ]);

        $this
            ->actingAs($adminUser)
            ->get(route('users.deactivate', ['user' => $user->id]))
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'active' => false,
        ]);
    }

    public function testUserCanBeActivated(): void
    {
        $adminUser = $this->getAdminUser();

        $user = User::factory()->create([
            'active' => false,
        ]);

        $this
            ->actingAs($adminUser)
            ->get(route('users.activate', ['user' => $user->id]))
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'active' => true,
        ]);
    }

    public function testAdminUserCanReset(): void
    {
        Notification::fake();

        $adminUser = $this->getAdminUser();

        $user = User::factory()->create();

        $this
            ->actingAs($adminUser)
            ->get(sprintf("/users/reset/%s", $user->id))
            ->assertStatus(200);

        $this
            ->actingAs($adminUser)
            ->post(sprintf("/users/reset/%s", $user->id))
            ->assertRedirect('/users');

        $this->post('/logout');

        $dbUser = User::find($user->id);
        assert($dbUser instanceof User);
        $this->assertSame($user->id, $dbUser->id);

        $this->assertNotSame($dbUser->two_factor_secret, $user->two_factor_secret);
        $this->assertNotNull($dbUser->registration_token);

        Notification::assertSentTo($dbUser, UserReset::class, function (UserReset $notification) use ($dbUser) {
            $mailMessage = $notification->toMail($dbUser);

            $this->assertSame(trans('user.mail.account_reset.subject'), $mailMessage->subject);
            $this->assertSame(trans('general.mail.greeting', ['name' => $dbUser->name]), $mailMessage->greeting);
            $this->assertSame($mailMessage->actionUrl, $notification->userResetUrl);

            $this
                ->get($notification->userResetUrl)
                ->assertStatus(200);

            return true;
        });
    }

    private function getAdminUser(): User|Collection|Model
    {
        $adminUser = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@exmple.com',
            'registered_at' => now(),
        ]);
        $adminUser->attachRole(Role::UserAdmin);
        $adminUser->markAsRegistered();
        return $adminUser;
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function testPasswordCanBeUpdated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'This@Is@A@New@Password#1245',
                'password_confirmation' => 'This@Is@A@New@Password#1245',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: 'user.flash.password_updated',
            ))
            ->assertRedirect('/profile');

        $this->assertTrue(Hash::check('This@Is@A@New@Password#1245', $user->refresh()->password));
    }

    public function testNewPasswordNeedsToBeValid(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword')
            ->assertSessionHasErrors(['password'], null, 'updatePassword')
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'user.flash.password_update_error',
            ))
            ->assertRedirect('/profile');
    }

    public function testCorrectPasswordMustBeProvidedToUpdatePassword(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertSessionHas('flash_notification', new FlashNotification(
                type: FlashNotificationTypeEnum::ERROR,
                message: 'user.flash.password_update_error',
            ))
            ->assertRedirect('/profile');
    }
}

<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use App\Models\User;
use App\Notifications\MailNotification;
use App\Services\UserService;
use Illuminate\Notifications\Messages\MailMessage;
use SensitiveParameter;

use function app;
use function trans;

class UserPasswordReset extends MailNotification
{
    protected readonly UserService $userService;

    public function __construct(#[SensitiveParameter] public string $token)
    {
        $this->userService = app(UserService::class);
    }

    public function toMail(User $notifiable): MailMessage
    {
        $emailForPasswordReset = $notifiable->getEmailForPasswordReset();

        $expirationTime = $this->userService->getDefaultExpirationTtl();
        $resetUrl = $this->userService->generateSignedPasswordResetUrl($this->token, $emailForPasswordReset);

        return (new MailMessage())
            ->subject(trans('user.mail.password_reset.subject'))
            ->greeting(trans('general.mail.greeting', ['name' => $notifiable->name]))
            ->line(trans('user.mail.password_reset.text'))
            ->action(trans('user.mail.password_reset.button_text'), $resetUrl)
            ->line(trans('user.mail.password_reset.link_expiration_text', ['count' => $expirationTime]))
            ->line(trans('user.mail.password_reset.disclaimer'))
            ->line(trans('general.mail.automated_message'));
    }
}

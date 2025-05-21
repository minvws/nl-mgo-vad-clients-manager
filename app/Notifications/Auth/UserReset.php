<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use App\Models\User;
use App\Notifications\MailNotification;
use Illuminate\Notifications\Messages\MailMessage;

use function trans;

class UserReset extends MailNotification
{
    public function __construct(public string $userResetUrl)
    {
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(trans('user.mail.account_reset.subject'))
            ->greeting(trans('general.mail.greeting', ['name' => $notifiable->name]))
            ->line(trans('user.mail.account_reset.text', ['appName' => $this->getAppName()]))
            ->action(trans('user.mail.account_reset.button_text'), $this->userResetUrl)
            ->line(trans('general.mail.automated_message'));
    }
}

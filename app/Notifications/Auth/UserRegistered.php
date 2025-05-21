<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use App\Models\User;
use App\Notifications\MailNotification;
use Illuminate\Notifications\Messages\MailMessage;

use function trans;

class UserRegistered extends MailNotification
{
    public function __construct(protected string $registrationUrl)
    {
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(trans('user.mail.registration.subject'))
            ->greeting(trans('general.mail.greeting', ['name' => $notifiable->name]))
            ->line(trans('user.mail.registration.text', ['appName' => $this->getAppName()]))
            ->action(trans('user.mail.registration.button_text'), $this->registrationUrl)
            ->line(trans('general.mail.automated_message'));
    }
}

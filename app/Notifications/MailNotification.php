<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Support\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MailNotification extends Notification
{
    use Queueable;

    /**
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['mail'];
    }

    protected function getAppName(): string
    {
        return Config::string('app.name');
    }
}

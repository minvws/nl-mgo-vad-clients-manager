<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Client;
use App\Support\I18n;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientSecretGenerated extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Client $client,
        public readonly string $clientSecret,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage())
            ->subject(I18n::trans('client.generated_mail.subject'))
            ->greeting(I18n::trans('client.generated_mail.greeting'))
            ->line(I18n::trans('client.generated_mail.generated_message'))
            ->line('**Client ID:** ' . $this->client->id)
            ->line('**Client Secret:** ' . $this->clientSecret)
            ->line(I18n::trans('client.generated_mail.store_secretly'))
            ->line(I18n::trans('client.generated_mail.usage'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'client_id' => $this->client->id,
        ];
    }
}

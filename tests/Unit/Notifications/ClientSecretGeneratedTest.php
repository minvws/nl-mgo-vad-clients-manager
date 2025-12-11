<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use App\Models\Client;
use App\Notifications\ClientSecretGenerated;
use Str;
use Tests\TestCase;

use function __;

class ClientSecretGeneratedTest extends TestCase
{
    public function testMailMessageContainsClientInformation(): void
    {
        $client = Client::factory()->create();
        $clientSecret = Str::uuid()->toString();
        $notification = new ClientSecretGenerated($client, $clientSecret);

        $mailMessage = $notification->toMail();

        $this->assertEquals(__('client.generated_mail.subject'), $mailMessage->subject);
        $mailData = $mailMessage->data();
        $introLines = $mailData['introLines'] ?? [];

        $this->assertContains(__('client.generated_mail.generated_message'), $introLines);
        $this->assertContains('**Client ID:** ' . $client->id, $introLines);
        $this->assertContains('**Client Secret:** ' . $clientSecret, $introLines);
    }

    public function testToArrayReturnsCorrectData(): void
    {
        $client = Client::factory()->create();

        $notification = new ClientSecretGenerated($client, Str::uuid()->toString());
        $array = $notification->toArray();

        $this->assertEquals([
            'client_id' => $client->id,
        ], $array);
    }
}

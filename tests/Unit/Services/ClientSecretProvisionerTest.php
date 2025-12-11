<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\TokenEndpointAuthMethod;
use App\Models\Client;
use App\Models\Organisation;
use App\Notifications\ClientSecretGenerated;
use App\Services\ClientSecret\ClientSecretGeneratorInterface;
use App\Services\ClientSecretProvisioner;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Throwable;

class ClientSecretProvisionerTest extends TestCase
{
    private ClientSecretGeneratorInterface $mockGenerator;
    private string $testSecret;
    private Organisation $organisation;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testSecret = $this->faker->uuid();
        $this->organisation = Organisation::factory()->create([
            'main_contact_email' => $this->faker->email(),
        ]);
        $this->client = Client::factory()->createQuietly([
            'organisation_id' => $this->organisation->id,
            'client_secret' => null,
        ]);

        $this->mockGenerator = $this->createMock(ClientSecretGeneratorInterface::class);
        $this->mockGenerator->expects($this->any())
            ->method('generate')
            ->willReturn($this->testSecret);
    }

    public function testGeneratesAndStoresClientSecret(): void
    {
        $service = new ClientSecretProvisioner($this->mockGenerator);

        $service->generateAndNotify($this->client);

        $this->client->refresh();
        $this->assertEquals($this->testSecret, $this->client->client_secret);
    }

    public function testSendsNotificationWithCorrectData(): void
    {
        Notification::fake();
        $service = new ClientSecretProvisioner($this->mockGenerator);

        $service->generateAndNotify($this->client);

        Notification::assertSentTo(
            Notification::route('mail', $this->organisation->main_contact_email),
            ClientSecretGenerated::class,
            fn($notification) => $notification->client->id === $this->client->id
                && $notification->clientSecret === $this->testSecret,
        );
    }

    public function testLogsSuccessInformation(): void
    {
        Log::spy();
        $service = new ClientSecretProvisioner($this->mockGenerator);

        $service->generateAndNotify($this->client);

        Log::shouldHaveReceived('info')
            ->with('Client secret generated and notification sent successfully', [
                'client_id' => (string) $this->client->id,
                'organisation_id' => (string) $this->client->organisation_id,
            ]);
    }

    public function testLogsErrorAndRethrowsOnException(): void
    {
        Log::spy();
        $exceptionMessage = 'Test exception during update';
        $failingClient = $this->createMock(Client::class);
        $failingClient->method('updateQuietly')->willThrowException(new Exception($exceptionMessage));
        $map = [
            'id' => $this->client->id,
            'organisation_id' => $this->organisation->id,
        ];
        $failingClient->method('__get')->willReturnCallback(fn($key) => $map[$key] ?? null);
        $service = new ClientSecretProvisioner($this->mockGenerator);
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage($exceptionMessage);

        $service->generateAndNotify($failingClient);

        Log::shouldHaveReceived('error')
            ->with('Error occurred during client secret generation', [
                'client_id' => (string) $this->client->id,
                'organisation_id' => (string) $this->organisation->id,
                'exception' => $exceptionMessage,
            ]);
    }

    public function testHandleAuthMethodChangeGeneratesSecretForClientSecret(): void
    {
        $client = Client::factory()->createQuietly([
            'organisation_id' => $this->organisation->id,
            'token_endpoint_auth_method' => TokenEndpointAuthMethod::CLIENT_SECRET,
        ]);
        $service = new ClientSecretProvisioner($this->mockGenerator);

        $service->handleAuthMethodChange($client);

        $client->refresh();
        $this->assertEquals($this->testSecret, $client->client_secret);
    }

    public function testHandleAuthMethodChangeClearsSecretForNone(): void
    {
        $client = Client::factory()->createQuietly([
            'organisation_id' => $this->organisation->id,
            'token_endpoint_auth_method' => TokenEndpointAuthMethod::NONE,
            'client_secret' => $this->testSecret,
        ]);
        $service = new ClientSecretProvisioner($this->mockGenerator);

        $service->handleAuthMethodChange($client);

        $client->refresh();
        $this->assertNull($client->client_secret);
    }

    public function testHandleAuthMethodChangeLogsErrorOnClearSecretException(): void
    {
        Log::spy();
        $exceptionMessage = 'Test exception during clear';
        $failingClient = $this->createMock(Client::class);
        $map = [
            'token_endpoint_auth_method' => TokenEndpointAuthMethod::NONE,
            'id' => $this->client->id,
            'organisation_id' => $this->organisation->id,
        ];
        $failingClient->method('__get')->willReturnCallback(fn($key) => $map[$key] ?? null);
        $failingClient->method('updateQuietly')->willThrowException(new Exception($exceptionMessage));
        $service = new ClientSecretProvisioner($this->mockGenerator);
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage($exceptionMessage);

        $service->handleAuthMethodChange($failingClient);

        Log::shouldHaveReceived('error')
            ->with('Error occurred during client secret clearing', [
                'client_id' => (string) $this->client->id,
                'organisation_id' => (string) $this->organisation->id,
                'exception' => $exceptionMessage,
            ]);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Commands;

use App\Models\Client;
use App\Models\Organisation;
use Illuminate\Database\UniqueConstraintViolationException;
use RuntimeException;
use Tests\TestCase;

use function fake;
use function implode;
use function sprintf;

class CreateClientCommandTest extends TestCase
{
    public function testHandleCreatesClient(): void
    {
        $organisation = Organisation::factory()->createOne();
        $redirectUri = fake()->url();
        $command = $this->generateCommand(
            (string) $organisation->id,
            [$redirectUri],
        );

        $this->artisan($command)
            ->expectsOutputToContain('Client created:')
            ->assertSuccessful();

        $this->assertDatabaseHas(Client::class, [
            'organisation_id' => $organisation->id,
            'redirect_uris' => '["' . $redirectUri . '"]',
            'active' => true,
        ]);
    }

    public function testHandleMarksClientInactiveIfOptionIsPassed(): void
    {
        $organisation = Organisation::factory()->createOne();
        $redirectUri = fake()->url();
        $command = $this->generateCommand(
            (string) $organisation->id,
            [$redirectUri],
            null,
            true,
        );

        $this->artisan($command)
            ->expectsOutputToContain('Client created:')
            ->assertSuccessful();

        $this->assertDatabaseHas(Client::class, [
            'organisation_id' => $organisation->id,
            'redirect_uris' => '["' . $redirectUri . '"]',
            'active' => false,
        ]);
    }

    public function testHandleCreatesClientWithGivenClientIdWhenProvided(): void
    {
        $organisation = Organisation::factory()->createOne();
        $redirectUri = fake()->url();
        $clientId = fake()->uuid();
        $command = $this->generateCommand(
            (string) $organisation->id,
            [$redirectUri],
            $clientId,
        );

        $this->artisan($command)
            ->expectsOutput('Client created: ' . $clientId)
            ->assertSuccessful();

        $this->assertDatabaseHas(Client::class, [
            'id' => $clientId,
            'organisation_id' => $organisation->id,
            'redirect_uris' => '["' . $redirectUri . '"]',
            'active' => true,
        ]);
    }

    public function testHandleThrowsErrorWhenProvidedClientIdIsInvalid(): void
    {
        $organisation = Organisation::factory()->createOne();
        $redirectUri = fake()->url();
        $command = $this->generateCommand(
            (string) $organisation->id,
            [$redirectUri],
            fake()->word(),
        );

        $this->artisan($command)
            ->expectsOutput('Client ID must be a valid UUID')
            ->assertFailed();
    }

    public function testHandleThrowsErrorWhenProvidedClientIdIsNotUnique(): void
    {
        $client = Client::factory()->createOne();
        $organisation = Organisation::factory()->createOne();
        $redirectUri = fake()->url();
        $command = $this->generateCommand(
            (string) $organisation->id,
            [$redirectUri],
            (string) $client->id,
        );

        $this->expectException(UniqueConstraintViolationException::class);
        $this->artisan($command);
    }

    public function testHandleFailsWhenRequiredArgumentsAreMissing(): void
    {
        $redirectUri = fake()->url();
        $command = $this->generateCommand(
            null,
            [$redirectUri],
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments');
        $this->artisan($command);
    }

    public function testHandleThrowsErrorWhenClientValidationsFail(): void
    {
        $organisation = Organisation::factory()->createOne();
        $redirectUri = 'invalid-uri';
        $command = $this->generateCommand(
            (string) $organisation->id,
            [$redirectUri],
        );

        $this->artisan($command)
            ->expectsOutputToContain('De URI invalid-uri is ongeldig.')
            ->assertFailed();
    }

    private function generateCommand(
        ?string $organisationId = null,
        ?array $redirectUris = null,
        ?string $clientId = null,
        bool $inactive = false,
    ): string {
        $command = 'client:create%s%s%s%s';

        return sprintf(
            $command,
            $organisationId ? ' ' . $organisationId : '',
            $redirectUris ? ' ' . implode(',', $redirectUris) : '',
            $clientId ? ' --client_id=' . $clientId : '',
            $inactive ? ' --inactive' : '',
        );
    }
}

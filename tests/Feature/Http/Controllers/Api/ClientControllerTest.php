<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Client;
use App\Repositories\Client\ClientRepository;
use Exception;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    public function testClientsIndexReturnsSuccessfulResponse(): void
    {
        Client::query()->delete();
        Client::factory()->active()->count(3)->create([
            'client_secret' => $this->faker->uuid,
        ]);

        $response = $this->getJson('/api/v1/clients');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'clients' => [
                '*' => [
                    'id',
                    'organisation_id',
                    'redirect_uris',
                    'active',
                    'client_secret',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $response->assertJsonCount(3, 'clients');

        $responseData = $response->json();
        foreach ($responseData['clients'] as $clientData) {
            $this->assertNotNull($clientData['client_secret']);
            $this->assertIsString($clientData['client_secret']);
            $this->assertNotEmpty($clientData['client_secret']);
        }
    }

    public function testClientsIndexReturnsEmptyArrayWhenNoClients(): void
    {
        $response = $this->getJson('/api/v1/clients');
        $response->assertStatus(200);
        $response->assertJson([
            'clients' => [],
        ]);
    }

    public function testClientsIndexHandlesClientsWithoutClientSecret(): void
    {
        Client::query()->delete();
        $client = Client::factory()->active()->create();

        $client->update(['client_secret' => null]);

        $response = $this->getJson('/api/v1/clients');
        $response->assertStatus(200);

        $responseData = $response->json();
        $this->assertNull($responseData['clients'][0]['client_secret']);
    }

    public function testClientsIndexReturns500WhenExceptionOccurs(): void
    {
        $mock = $this->mock(ClientRepository::class, function ($mock): void {
            $mock->shouldReceive('allMinimal')
                ->once()
                ->andThrow(new Exception('Database error'));
        });

        $this->app->instance(ClientRepository::class, $mock);

        $response = $this->getJson('/api/v1/clients');
        $response->assertStatus(500);
        $response->assertJson([
            'error' => 'Internal Server Error',
        ]);
    }
}

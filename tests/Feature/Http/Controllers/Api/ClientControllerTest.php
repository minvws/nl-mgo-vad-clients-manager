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
        Client::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/clients');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'clients' => [
                '*' => [
                    'id',
                    'organisation_id',
                    'redirect_uris',
                    'fqdn',
                    'active',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $response->assertJsonCount(3, 'clients');
    }

    public function testClientsIndexReturnsEmptyArrayWhenNoClients(): void
    {
        $response = $this->getJson('/api/v1/clients');
        $response->assertStatus(200);
        $response->assertJson([
            'clients' => [],
        ]);
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

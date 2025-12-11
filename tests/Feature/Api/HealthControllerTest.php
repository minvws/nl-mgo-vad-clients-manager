<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Services\Health\DatabaseHealthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testHealthEndpointReturnsCorrectStructure(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk();
        $response->assertJsonStructure([
            'healthy',
            'externals',
        ]);
        $response->assertJsonStructure([
            'externals' => [
                'database',
            ],
        ]);
    }

    public function testHealthEndpointReturnsHealthyWhenDatabaseServiceIsHealthy(): void
    {
        $mockService = $this->createMock(DatabaseHealthService::class);
        $mockService->method('isHealthy')->willReturn(true);
        $this->app->instance(DatabaseHealthService::class, $mockService);

        $response = $this->getJson('/api/health');

        $response->assertOk()
            ->assertJson([
                'healthy' => true,
                'externals' => [
                    'database' => true,
                ],
            ]);
    }

    public function testHealthEndpointReturnsUnhealthyWhenDatabaseServiceIsUnhealthy(): void
    {
        $mockService = $this->createMock(DatabaseHealthService::class);
        $mockService->method('isHealthy')->willReturn(false);
        $this->app->instance(DatabaseHealthService::class, $mockService);

        $response = $this->getJson('/api/health');

        $response->assertServiceUnavailable()
            ->assertJson([
                'healthy' => false,
                'externals' => [
                    'database' => false,
                ],
            ]);
    }
}

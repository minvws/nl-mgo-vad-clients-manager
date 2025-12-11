<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Health;

use App\Services\Health\DatabaseHealthService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PDOException;
use Tests\TestCase;

class DatabaseHealthServiceTest extends TestCase
{
    use DatabaseTransactions;

    private DatabaseHealthService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DatabaseHealthService();
    }

    public function testIsHealthyReturnsTrueWhenDatabaseIsAccessible(): void
    {
        $result = $this->service->isHealthy();

        $this->assertTrue($result);
    }

    public function testIsHealthyReturnsFalseWhenDatabaseIsInaccessible(): void
    {
        DB::shouldReceive('connection')
            ->once()
            ->andReturnSelf();

        DB::shouldReceive('getPdo')
            ->once()
            ->andThrow(new PDOException('Simulated connection failure'));

        $result = $this->service->isHealthy();

        $this->assertFalse($result);
    }
}

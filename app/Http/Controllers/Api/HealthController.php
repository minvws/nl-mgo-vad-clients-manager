<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Health\DatabaseHealthService;
use Illuminate\Http\JsonResponse;

use function response;

class HealthController extends Controller
{
    public function __construct(
        private readonly DatabaseHealthService $databaseHealthService,
    ) {
    }

    public function index(): JsonResponse
    {
        $databaseHealthy = $this->databaseHealthService->isHealthy();
        $totallyHealthy = $databaseHealthy; // future services can be added here

        return response()->json(
            data: [
                'healthy' => $totallyHealthy,
                'externals' => [
                    'database' => $databaseHealthy,
                ],
            ],
            status: $totallyHealthy ? 200 : 503,
        );
    }
}

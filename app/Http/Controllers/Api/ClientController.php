<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Client\ClientRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClientController extends Controller
{
    public function __construct(private readonly ClientRepository $clientRepository)
    {
    }

    public function index(): JsonResponse
    {
        Log::info('Received GET /api/clients request');

        try {
            $clients = $this->clientRepository->allMinimal();

            Log::info('GET /api/clients request processed', [
                'count' => $clients->count(),
                'client_ids' => $clients->pluck('id')->toArray(),
            ]);

            return new JsonResponse(['clients' => $clients], 200);
        } catch (Throwable $e) {
            Log::error('Error while retrieving clients', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }
}

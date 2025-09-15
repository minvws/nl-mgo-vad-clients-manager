<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;

use function collect;
use function filter_var;
use function now;

use const FILTER_VALIDATE_URL;

class ClientChangeNotifier
{
    /** @var Collection<int|string,string> */
    private Collection $notifiables;

    public function __construct(
        private readonly LoggerInterface $logger,
        string ...$notifiables,
    ) {
        $this->notifiables = collect($notifiables);
    }

    /**
     * Notify the client change to the specified URLs.
     */
    public function notify(): void
    {
        foreach ($this->notifiables as $url) {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $this->logger->warning('Invalid URL provided for client change notification', [
                    'url' => $url,
                ]);
                continue;
            }

            try {
                $response = Http::retry(3, 100)
                    ->post($url)
                    ->throw();

                $this->logger->debug('Notifying client change', [
                    'url' => $url,
                    'timestamp' => now()->toIso8601String(),
                    'response_status' => $response->status(),
                ]);
            } catch (RequestException | ConnectionException $e) {
                $this->logger->error('Failed to notify client change', [
                    'url' => $url,
                    'timestamp' => now()->toIso8601String(),
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }
    }
}

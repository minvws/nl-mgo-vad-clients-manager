<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\TokenEndpointAuthMethod;
use App\Models\Client;
use App\Services\ClientChangeNotifier;
use App\Services\ClientSecretProvisioner;

class ClientObserver
{
    public function __construct(
        private readonly ClientChangeNotifier $clientChangeNotifier,
        private readonly ClientSecretProvisioner $clientSecretProvisioner,
    ) {
    }

    public function created(Client $client): void
    {
        if ($client->token_endpoint_auth_method === TokenEndpointAuthMethod::CLIENT_SECRET) {
            $this->clientSecretProvisioner->generateAndNotify($client);
        }
        $this->clientChangeNotifier->notify();
    }

    public function updated(Client $client): void
    {
        if ($client->wasChanged('token_endpoint_auth_method')) {
            $this->clientSecretProvisioner->handleAuthMethodChange($client);
        }
        $this->clientChangeNotifier->notify();
    }

    public function deleted(): void
    {
        $this->clientChangeNotifier->notify();
    }
}

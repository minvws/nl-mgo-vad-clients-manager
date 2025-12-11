<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TokenEndpointAuthMethod;
use App\Models\Client;
use App\Notifications\ClientSecretGenerated;
use App\Services\ClientSecret\ClientSecretGeneratorInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class ClientSecretProvisioner
{
    public function __construct(
        private readonly ClientSecretGeneratorInterface $secretGenerator,
    ) {
    }

    public function handleAuthMethodChange(Client $client): void
    {
        if ($client->token_endpoint_auth_method === TokenEndpointAuthMethod::CLIENT_SECRET) {
            $this->generateAndNotify($client);
        } elseif ($client->token_endpoint_auth_method === TokenEndpointAuthMethod::NONE) {
            $this->clearSecret($client);
        }
    }

    private function clearSecret(Client $client): void
    {
        try {
            $client->updateQuietly(['client_secret' => null]);

            Log::info('Client secret cleared', [
                'client_id' => (string) $client->id,
                'organisation_id' => (string) $client->organisation_id,
            ]);
        } catch (Throwable $e) {
            Log::error('Error occurred during client secret clearing', [
                'client_id' => (string) $client->id,
                'organisation_id' => (string) $client->organisation_id,
                'exception' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function generateAndNotify(Client $client): void
    {
        $clientSecret = $this->secretGenerator->generate();

        try {
            DB::transaction(static function () use ($client, $clientSecret): void {
                $client->updateQuietly(['client_secret' => $clientSecret]);


                Notification::route('mail', $client->organisation->main_contact_email)
                    ->notify(new ClientSecretGenerated($client, $clientSecret));
            });

            Log::info('Client secret generated and notification sent successfully', [
                'client_id' => (string) $client->id,
                'organisation_id' => (string) $client->organisation_id,
            ]);
        } catch (Throwable $e) {
            Log::error('Error occurred during client secret generation', [
                'client_id' => (string) $client->id,
                'organisation_id' => (string) $client->organisation_id,
                'exception' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

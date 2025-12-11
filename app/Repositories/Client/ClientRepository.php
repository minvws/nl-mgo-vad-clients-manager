<?php

declare(strict_types=1);

namespace App\Repositories\Client;

use App\Models\Client;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class ClientRepository
{
    /**
     * For the VAD integration, we only want to fetch the minimal required data.
     *
     * @return Collection<int, Client>
     */
    public function allMinimal(): Collection
    {
        $clients = Client::query()
            ->select([
                'id',
                'organisation_id',
                'redirect_uris',
                'token_endpoint_auth_method',
                'active',
                'client_secret',
                'created_at',
                'updated_at',
            ])
            ->where('active', true)
            ->get();

        Assert::allIsInstanceOf($clients, Client::class);

        return $clients;
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Client;

use App\Models\Client;
use Illuminate\Support\Collection;

class ClientRepository
{
    /**
     * For the VAD integration, we only want to fetch the minimal required data.
     *
     * @return Collection<int,Client>
     */
    public function allMinimal(): Collection
    {
        return Client::all([
            'id',
            'organisation_id',
            'redirect_uris',
            'fqdn',
            'active',
            'created_at',
            'updated_at',
        ]);
    }
}

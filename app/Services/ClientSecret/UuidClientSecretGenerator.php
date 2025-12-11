<?php

declare(strict_types=1);

namespace App\Services\ClientSecret;

use Illuminate\Support\Str;

class UuidClientSecretGenerator implements ClientSecretGeneratorInterface
{
    public function generate(): string
    {
        return Str::uuid()->toString();
    }
}

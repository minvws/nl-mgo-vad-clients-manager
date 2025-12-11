<?php

declare(strict_types=1);

namespace App\Services\ClientSecret;

interface ClientSecretGeneratorInterface
{
    public function generate(): string;
}

<?php

declare(strict_types=1);

namespace App\Enums;

enum TokenEndpointAuthMethod: string
{
    case NONE = 'none';
    case CLIENT_SECRET = 'client_secret_post';

    /**
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        return [
            self::NONE->value => 'None',
            self::CLIENT_SECRET->value => 'Client Secret Post',
        ];
    }
}

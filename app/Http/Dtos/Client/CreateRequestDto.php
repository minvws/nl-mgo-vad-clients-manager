<?php

declare(strict_types=1);

namespace App\Http\Dtos\Client;

use App\Enums\TokenEndpointAuthMethod;
use App\Http\Dtos\BaseDto;

readonly class CreateRequestDto extends BaseDto
{
    public string $organisation_id;

    /** @var array<string> */
    public array $redirect_uris;
    public TokenEndpointAuthMethod $token_endpoint_auth_method;
    public bool $active;
}

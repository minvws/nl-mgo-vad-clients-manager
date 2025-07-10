<?php

declare(strict_types=1);

namespace App\Http\Dtos\Client;

use App\Http\Dtos\BaseDto;

readonly class CreateRequestDto extends BaseDto
{
    public string $organisation_id;

    /** @var array<string> */
    public array $redirect_uris;
    public string $fqdn;
    public bool $active;
}

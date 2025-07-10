<?php

declare(strict_types=1);

namespace App\Http\Dtos\RegistrationRequest;

use App\Http\Dtos\BaseDto;

readonly class CreateRequestDto extends BaseDto
{
    public string $organisation_name;
    public string $organisation_main_contact_email;
    public string $organisation_main_contact_name;
    public string $client_fqdn;

    public string $organisation_coc_number;

    /** @var array<int, string> */
    public array $client_redirect_uris;
}

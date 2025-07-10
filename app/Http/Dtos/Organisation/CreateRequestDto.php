<?php

declare(strict_types=1);

namespace App\Http\Dtos\Organisation;

use App\Http\Dtos\BaseDto;

readonly class CreateRequestDto extends BaseDto
{
    public string $name;
    public string $main_contact_email;
    public string $main_contact_name;
    public string $coc_number;
    public ?string $notes;
}

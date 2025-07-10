<?php

declare(strict_types=1);

namespace App\Http\Dtos\User;

use App\Http\Dtos\BaseDto;

readonly class CreateRequestDto extends BaseDto
{
    public string $name;
    public string $email;

    /** @var array<string> */
    public array $roles;
}

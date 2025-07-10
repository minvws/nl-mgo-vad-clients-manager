<?php

declare(strict_types=1);

namespace App\Http\Dtos;

readonly class PasswordUpdateRequestDto extends BaseDto
{
    public string $password;
}

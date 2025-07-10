<?php

declare(strict_types=1);

namespace App\Http\Dtos\User;

use App\Http\Dtos\BaseDto;

readonly class RegisterRequestDto extends BaseDto
{
    public string $name;
    public string $password;
    public string $two_factor_code;
    public ?string $token;
}

<?php

declare(strict_types=1);

namespace App\Http\Dtos\User;

use App\Http\Dtos\BaseDto;

readonly class StorePasswordRequestDto extends BaseDto
{
    public string $token;
    public string $email;
    public string $password;
    public string $password_confirmation;
    public string $two_factor_code;
}

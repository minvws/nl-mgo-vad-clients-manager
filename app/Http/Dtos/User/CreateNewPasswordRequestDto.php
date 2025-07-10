<?php

declare(strict_types=1);

namespace App\Http\Dtos\User;

use App\Http\Dtos\BaseDto;

readonly class CreateNewPasswordRequestDto extends BaseDto
{
    public readonly string $email;
}

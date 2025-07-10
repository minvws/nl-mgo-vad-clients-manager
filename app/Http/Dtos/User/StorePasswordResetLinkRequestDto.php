<?php

declare(strict_types=1);

namespace App\Http\Dtos\User;

use App\Http\Dtos\BaseDto;

readonly class StorePasswordResetLinkRequestDto extends BaseDto
{
    public string $email;
}

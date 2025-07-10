<?php

declare(strict_types=1);

namespace App\Http\Dtos\Profile;

use App\Http\Dtos\BaseDto;

readonly class TwoFactorResetDto extends BaseDto
{
    public string $code;
    public string $encrypted_secret;
}

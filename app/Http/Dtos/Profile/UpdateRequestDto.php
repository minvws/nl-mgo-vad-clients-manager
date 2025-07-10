<?php

declare(strict_types=1);

namespace App\Http\Dtos\Profile;

use App\Http\Dtos\BaseDto;

readonly class UpdateRequestDto extends BaseDto
{
    public string $name;
    public string $email;
}

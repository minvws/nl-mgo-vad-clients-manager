<?php

declare(strict_types=1);

namespace App\Http\Dtos\User;

use App\Http\Dtos\BaseDto;

readonly class FilterRequestDto extends BaseDto
{
    public ?string $filter;
    public string $sort;
    public string $direction;
}

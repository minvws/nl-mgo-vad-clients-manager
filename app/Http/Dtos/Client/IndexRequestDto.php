<?php

declare(strict_types=1);

namespace App\Http\Dtos\Client;

use App\Http\Dtos\BaseDto;

readonly class IndexRequestDto extends BaseDto
{
    public ?string $search;
    public string $sort;
    public string $direction;
    public ?bool $active;
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Dtos\Client;

use App\Http\Dtos\Client\CreateRequestDto;
use Tests\TestCase;
use TypeError;

class CreateRequestDtoTest extends TestCase
{
    public function testThrowsTypeErrorForInvalidEnumValue(): void
    {
        $this->expectException(TypeError::class);

        new CreateRequestDto([
            'organisation_id' => '123',
            'redirect_uris' => ['https://example.com'],
            'token_endpoint_auth_method' => 'invalid_value',
            'active' => true,
        ]);
    }
}

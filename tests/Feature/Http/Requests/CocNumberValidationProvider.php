<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Requests;

class CocNumberValidationProvider
{
    public static function validationProvider(): array
    {
        return [
            'valid coc_number validation is successfully' => ['12345678', true],
            'valid coc_number validation with leading zeros is successfully' => ['00345678', true],
            'too short coc_number validation fails' => ['1234567', false],
            'too long coc_number validation fails' => ['123456789', false],
            'non-digit characters in coc_number validation fails' => ['1234abcd', false],
            'empty coc_number validation fails' => ['', false],
            'null coc_number validation fails' => [null, false],
            'numeric coc_number validation fails' => [12_345_678, false],
        ];
    }
}

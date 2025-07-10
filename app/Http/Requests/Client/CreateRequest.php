<?php

declare(strict_types=1);

namespace App\Http\Requests\Client;

use App\Http\Dtos\Client\CreateRequestDto;
use App\Http\Requests\TypedRequest;
use App\Rules\FQDNMatchRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use TypeError;

class CreateRequest extends TypedRequest
{
    // This regex (FQDN_REG_EX) validates a Fully Qualified Domain Name (FQDN) with the following rules:
    // 1. `(?=^.{4,253}$)` - Positive lookahead to ensure the entire FQDN is between 4 and 253 characters long.
    // 2. `(^((?!-)[a-zA-Z0-9-]{1,63}(?<!-)\.)+)` - Matches one or more domain labels:
    //    - Each label must be 1 to 63 characters long.
    //    - Labels can contain alphanumeric characters and hyphens (`-`), but cannot start or end with a hyphen.
    //    - Labels are separated by dots (`.`).
    // 3. `[a-zA-Z]{2,63}$` - Ensures the FQDN ends with a valid top-level domain (TLD):
    //    - The TLD must be 2 to 63 alphabetic characters.
    public const string FQDN_REG_EX = '/(?=^.{4,253}$)(^((?!-)[a-zA-Z0-9-]{1,63}(?<!-)\.)+[a-zA-Z]{2,63}$)/';

    /**
     * @return array<string, string|array<int, string|Unique|FQDNMatchRule>>
     */
    public function rules(): array
    {
        return [
            'organisation_id' => [
                'required',
            ],
            'redirect_uris' => [
                'required',
                'array',
                'min:1',
            ],
            'redirect_uris.*' => [
                'required',
                'string',
                'distinct:strict',
                new FQDNMatchRule(),
            ],
            'fqdn' => [
                'required',
                Rule::unique('clients', 'fqdn'),
                'regex:' . self::FQDN_REG_EX,
            ],
            'active' => [
                'required',
                'boolean',
            ],
        ];
    }

    /**
     * @throws TypeError
     */
    public function getValidatedDto(): CreateRequestDto
    {
        return new CreateRequestDto($this->safe());
    }
}

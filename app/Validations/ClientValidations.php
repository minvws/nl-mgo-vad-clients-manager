<?php

declare(strict_types=1);

namespace App\Validations;

use App\Enums\TokenEndpointAuthMethod;
use App\Rules\UriValidatorRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class ClientValidations
{
    /**
     * @return array<string, string|array<int, string|UriValidatorRule|Enum>>
     */
    public static function rules(): array
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
                new UriValidatorRule(),
            ],
            'active' => [
                'required',
                'boolean',
            ],
            'token_endpoint_auth_method' => [
                'required',
                Rule::enum(TokenEndpointAuthMethod::class),
            ],
        ];
    }
}

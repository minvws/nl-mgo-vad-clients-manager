<?php

declare(strict_types=1);

namespace App\Http\Requests\RegistrationRequest;

use App\Http\Dtos\RegistrationRequest\CreateRequestDto;
use App\Http\Requests\TypedRequest;
use App\Rules\FQDNMatchRule;
use Illuminate\Contracts\Validation\ValidationRule;

class CreateRequest extends TypedRequest
{
    /**
     * @return array<string, string|array<int, string|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'organisation_name' => [
                'required',
                'string',
                'max:128',
            ],
            'organisation_main_contact_email' => [
                'required',
                'email',
                'max:128',
            ],
            'organisation_main_contact_name' => [
                'required',
                'string',
                'max:128',
            ],
            'organisation_coc_number' => [
                'required',
                'string',
                'digits:8',
            ],
            'client_fqdn' => [
                'required',
                'regex:' . \App\Http\Requests\Client\CreateRequest::FQDN_REG_EX,
            ],
            'client_redirect_uris' => [
                'required',
                'array',
                'min:1',
            ],
            'client_redirect_uris.*' => [
                'required',
                'string',
                'distinct:strict',
                new FQDNMatchRule(),
            ],
        ];
    }

    public function getValidatedDto(): CreateRequestDto
    {
        return new CreateRequestDto($this->safe());
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests\Client;

use App\Http\Dtos\Client\UpdateRequestDto;
use App\Http\Requests\TypedRequest;
use App\Models\Client;
use App\Rules\FQDNMatchRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use TypeError;
use Webmozart\Assert\Assert;

class UpdateRequest extends TypedRequest
{
    /**
     * @return array<string, string|array<int, string|Unique|ValidationRule>>
     */
    public function rules(): array
    {
        $client = $this->route('client');
        Assert::isInstanceOf($client, Client::class);

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
                Rule::unique('clients', 'fqdn')->ignore($client),
                'regex:' . CreateRequest::FQDN_REG_EX,
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
    public function getValidatedDto(): UpdateRequestDto
    {
        return new UpdateRequestDto($this->safe());
    }
}

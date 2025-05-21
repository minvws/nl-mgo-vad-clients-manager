<?php

declare(strict_types=1);

namespace App\Http\Requests\Client;

use App\Http\Requests\FormRequest;
use App\Models\Client;
use App\Rules\FQDNMatchRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Webmozart\Assert\Assert;

class UpdateRequest extends FormRequest
{
    /**
     * @return array<string, string|array<int, string|Unique|ValidationRule>>
     */
    public function rules(): array
    {
        $client = $this->route('client');
        Assert::isInstanceOf($client, Client::class);

        return [
            'organisation_id' => 'required',
            'fqdn' => [
                'required',
                Rule::unique('clients', 'fqdn')->ignore($client),
                'regex:' . CreateRequest::FQDN_REG_EX,
            ],
            'redirect_uris' => 'required|array|min:1',
            'redirect_uris.*' => ['required', 'string', 'distinct:strict', new FQDNMatchRule()],
            'active' => 'required|boolean',
        ];
    }
}

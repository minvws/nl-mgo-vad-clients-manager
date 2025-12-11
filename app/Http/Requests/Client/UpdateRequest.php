<?php

declare(strict_types=1);

namespace App\Http\Requests\Client;

use App\Http\Dtos\Client\UpdateRequestDto;
use App\Http\Requests\TypedRequest;
use App\Models\Client;
use App\Validations\ClientValidations;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Unique;
use TypeError;
use Webmozart\Assert\Assert;

class UpdateRequest extends TypedRequest
{
    /**
     * @return array<string, string|array<int, string|Unique|ValidationRule|Enum>>
     */
    public function rules(): array
    {
        $client = $this->route('client');
        Assert::isInstanceOf($client, Client::class);

        return ClientValidations::rules();
    }

    /**
     * @throws TypeError
     */
    public function getValidatedDto(): UpdateRequestDto
    {
        return new UpdateRequestDto($this->safe());
    }
}

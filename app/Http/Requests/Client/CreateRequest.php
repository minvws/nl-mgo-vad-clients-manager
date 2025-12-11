<?php

declare(strict_types=1);

namespace App\Http\Requests\Client;

use App\Http\Dtos\Client\CreateRequestDto;
use App\Http\Requests\TypedRequest;
use App\Rules\UriValidatorRule;
use App\Validations\ClientValidations;
use Illuminate\Validation\Rules\Enum;
use TypeError;

class CreateRequest extends TypedRequest
{
    /**
     * @return array<string, string|array<int, string|UriValidatorRule|Enum>>
     */
    public function rules(): array
    {
        return ClientValidations::rules();
    }

    /**
     * @throws TypeError
     */
    public function getValidatedDto(): CreateRequestDto
    {
        return new CreateRequestDto($this->safe());
    }
}

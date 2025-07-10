<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation;

use App\Http\Dtos\Organisation\CreateRequestDto;
use App\Http\Requests\TypedRequest;
use TypeError;

class CreateRequest extends TypedRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'unique:organisations,name',
                'max:128',
            ],
            'main_contact_email' => [
                'required',
                'email',
                'max:128',
            ],
            'main_contact_name' => [
                'required',
                'string',
                'max:128',
            ],
            'coc_number' => [
                'required',
                'string',
                'digits:8',
            ],
            'notes' => [
                'nullable',
                'string',
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

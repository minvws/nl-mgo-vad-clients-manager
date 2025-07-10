<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation;

use App\Http\Dtos\Organisation\UpdateRequestDto;
use App\Http\Requests\TypedRequest;
use TypeError;

class UpdateRequest extends TypedRequest
{
    public function rules(): array
    {
        $organisationId = $this->route('organisation')?->id ?? '';

        return [
            'name' => [
                'required',
                'string',
                'max:128',
                'unique:organisations,name,' . $organisationId,
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
    public function getValidatedDto(): UpdateRequestDto
    {
        return new UpdateRequestDto($this->safe());
    }
}

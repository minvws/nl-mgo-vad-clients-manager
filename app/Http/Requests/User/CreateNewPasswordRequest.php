<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Dtos\User\CreateNewPasswordRequestDto;
use App\Http\Requests\TypedRequest;

class CreateNewPasswordRequest extends TypedRequest
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
            ],
        ];
    }

    public function getValidatedDto(): CreateNewPasswordRequestDto
    {
        return new CreateNewPasswordRequestDto($this->safe());
    }
}

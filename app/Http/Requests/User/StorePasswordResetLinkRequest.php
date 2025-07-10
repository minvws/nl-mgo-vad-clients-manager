<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Dtos\User\StorePasswordResetLinkRequestDto;
use App\Http\Requests\TypedRequest;

class StorePasswordResetLinkRequest extends TypedRequest
{
    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
            ],
        ];
    }

    public function getValidatedDto(): StorePasswordResetLinkRequestDto
    {
        return new StorePasswordResetLinkRequestDto($this->safe());
    }
}

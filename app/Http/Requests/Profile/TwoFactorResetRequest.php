<?php

declare(strict_types=1);

namespace App\Http\Requests\Profile;

use App\Http\Dtos\Profile\TwoFactorResetDto;
use App\Http\Requests\TypedRequest;
use TypeError;

/**
 * @property string $code
 * @property string $encrypted_secret
 */
class TwoFactorResetRequest extends TypedRequest
{
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'digits:6',
            ],
            'encrypted_secret' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * @throws TypeError
     */
    public function getValidatedDto(): TwoFactorResetDto
    {
        return new TwoFactorResetDto($this->safe());
    }
}

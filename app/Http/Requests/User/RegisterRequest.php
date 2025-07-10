<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Actions\Fortify\PasswordValidationRules;
use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Http\Dtos\User\RegisterRequestDto;
use App\Http\Requests\TypedRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use TypeError;

class RegisterRequest extends TypedRequest
{
    use PasswordValidationRules;

    /**
     * @return array<string, array<int, Password|string>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'password' => $this->passwordRules(),
            'two_factor_code' => [
                'required',
                'string',
            ],
            'token' => [
                'sometimes',
                'string',
            ],
        ];
    }

    /**
     * @throws TypeError
     */
    public function getValidatedDto(): RegisterRequestDto
    {
        return new RegisterRequestDto($this->safe());
    }

    protected function failedValidation(Validator $validator): void
    {
        Session::flash(FlashNotification::SESSION_KEY, new FlashNotification(
            type: FlashNotificationTypeEnum::ERROR,
            message: 'user.flash.register_error',
        ));

        parent::failedValidation($validator);
    }
}

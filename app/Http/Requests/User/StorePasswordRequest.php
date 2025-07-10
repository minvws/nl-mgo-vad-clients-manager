<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Actions\Fortify\PasswordValidationRules;
use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Http\Dtos\User\StorePasswordRequestDto;
use App\Http\Requests\TypedRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use TypeError;

/**
 * @property string $password
 * @property string $two_factor_code
 */
class StorePasswordRequest extends TypedRequest
{
    use PasswordValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, Password|string>>
     */
    public function rules(): array
    {
        return [
            'token' => [
                'required',
            ],
            'email' => [
                'required',
                'email',
            ],
            'password' => $this->passwordRules(),
            'password_confirmation' => [
                'required',
                'same:password',
            ],
            'two_factor_code' => [
                'required',
                'string',
                'digits:6',
                'numeric',
            ],
        ];
    }

    /**
     * @throws TypeError
     */
    public function getValidatedDto(): StorePasswordRequestDto
    {
        return new StorePasswordRequestDto($this->safe());
    }

    protected function failedValidation(Validator $validator): void
    {
        Session::flash(FlashNotification::SESSION_KEY, new FlashNotification(
            type: FlashNotificationTypeEnum::ERROR,
            message: 'authentication.forgot_password.reset_error',
        ));

        parent::failedValidation($validator);
    }
}

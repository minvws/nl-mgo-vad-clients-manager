<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Actions\Fortify\PasswordValidationRules;
use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Http\Dtos\PasswordUpdateRequestDto;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use TypeError;

class PasswordUpdateRequest extends TypedRequest
{
    use PasswordValidationRules;

    protected $errorBag = 'updatePassword'; //phpcs:ignore

    /**
     * @return array<string, array<int, Password|string>>
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                'current_password:web',
            ],
            'password' => $this->passwordRules(),
        ];
    }

    /**
     * @throws TypeError
     */
    public function getValidatedDto(): PasswordUpdateRequestDto
    {
        return new PasswordUpdateRequestDto($this->safe());
    }

    protected function failedValidation(Validator $validator): void
    {
        Session::flash(FlashNotification::SESSION_KEY, new FlashNotification(
            type: FlashNotificationTypeEnum::ERROR,
            message: 'user.flash.password_update_error',
        ));

        parent::failedValidation($validator);
    }
}

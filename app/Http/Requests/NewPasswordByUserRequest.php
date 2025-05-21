<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Actions\Fortify\PasswordValidationRules;
use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;

/**
 * @property string $password
 * @property string $two_factor_code
 */
class NewPasswordByUserRequest extends FormRequest
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
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => $this->passwordRules(),
        ];
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

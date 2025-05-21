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
 */
class PasswordUpdateRequest extends FormRequest
{
    use PasswordValidationRules;

    protected $errorBag = 'updatePassword'; //phpcs:ignore

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, Password|string>>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ];
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

<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Enums\Role;
use App\Models\User;
use App\Support\Auth;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Unique;

class UserCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::userIfAuthenticated()?->can('create', User::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<ValidationRule|Enum|Unique|string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email:strict',
                'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'roles' => ['required', 'array'],
            'roles.*' => ['string', Rule::enum(Role::class)],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Session::flash(FlashNotification::SESSION_KEY, new FlashNotification(
            type: FlashNotificationTypeEnum::ERROR,
            message: 'user.flash.created_error',
        ));

        parent::failedValidation($validator);
    }
}

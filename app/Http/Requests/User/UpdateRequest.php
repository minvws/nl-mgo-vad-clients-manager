<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\Enums\Role;
use App\Http\Dtos\User\UpdateRequestDto;
use App\Http\Requests\TypedRequest;
use App\Models\User;
use App\Support\Auth;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use TypeError;
use Webmozart\Assert\Assert;

/**
 * @property array<string> $roles
 */
class UpdateRequest extends TypedRequest
{
    public function authorize(
        #[RouteParameter('user')]
        User $user,
    ): bool {
        return Auth::userIfAuthenticated()?->can('update', $user) ?? false;
    }

    /**
     * @return array<string, array<ValidationRule|Enum|string>>
     */
    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');
        Assert::isInstanceOf($user, User::class);

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email:strict',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
            'roles' => [
                'required',
                'array',
            ],
            'roles.*' => [
                'string',
                Rule::enum(Role::class),
            ],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        Session::flash(FlashNotification::SESSION_KEY, new FlashNotification(
            type: FlashNotificationTypeEnum::ERROR,
            message: 'user.flash.update_error',
        ));

        parent::failedValidation($validator);
    }

    /**
     * @throws TypeError
     */
    public function getValidatedDto(): UpdateRequestDto
    {
        return new UpdateRequestDto($this->safe());
    }
}

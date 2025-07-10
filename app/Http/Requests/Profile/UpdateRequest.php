<?php

declare(strict_types=1);

namespace App\Http\Requests\Profile;

use App\Http\Dtos\Profile\UpdateRequestDto;
use App\Http\Requests\TypedRequest;
use App\Models\User;
use App\Support\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use TypeError;

class UpdateRequest extends TypedRequest
{
    public function authorize(): bool
    {
        $user = Auth::userIfAuthenticated();

        return $user?->can('updateOwnData', $user) ?? false;
    }

    /**
     * @return array<string, array<Rule|Unique|string>>
     */
    public function rules(): array
    {
        $user = Auth::user();

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ];
    }

    /**
     * @throws TypeError
     */
    public function getValidatedDto(): UpdateRequestDto
    {
        return new UpdateRequestDto($this->safe());
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use App\Support\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::userIfAuthenticated();

        return $user?->can('updateOwnData', $user) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
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
}

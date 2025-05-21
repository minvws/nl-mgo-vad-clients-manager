<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use App\Support\Auth;
use Illuminate\Contracts\Validation\ValidationRule;

class UserFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::userIfAuthenticated()?->can('index', User::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'filter' => ['nullable', 'string'],
            'sort' => ['nullable', 'string', 'in:email,name'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }
}

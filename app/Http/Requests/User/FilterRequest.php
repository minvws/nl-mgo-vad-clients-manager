<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Dtos\User\FilterRequestDto;
use App\Http\Requests\TypedRequest;
use App\Models\User;
use App\Support\Auth;
use Illuminate\Contracts\Validation\ValidationRule;
use TypeError;

class FilterRequest extends TypedRequest
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
            'filter' => [
                'nullable',
                'string',
            ],
            'sort' => [
                'nullable',
                'string',
                'in:email,name',
            ],
            'direction' => [
                'nullable',
                'string',
                'in:asc,desc',
            ],
        ];
    }

    /**
     * @throws TypeError
     */
    public function getValidatedDto(): FilterRequestDto
    {
        $data = $this->safe();
        $data['direction'] = $data['direction'] ?? 'asc';
        $data['sort'] = $data['sort'] ?? 'updated_at';

        return new FilterRequestDto($data);
    }
}

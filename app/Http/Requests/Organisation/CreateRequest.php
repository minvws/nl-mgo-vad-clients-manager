<?php

declare(strict_types=1);

namespace App\Http\Requests\Organisation;

use App\Http\Requests\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:organisations,name|max:128',
            'main_contact_email' => 'required|email|max:128',
            'main_contact_name' => 'required|string|max:128',
            'coc_number' => 'required|string|size:8',
            'notes' => 'nullable|string',
        ];
    }
}

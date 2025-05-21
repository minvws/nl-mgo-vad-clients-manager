<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @property string $code
 * @property string $encrypted_secret
 */
class Profile2FAResetRequest extends FormRequest
{
    /**
     * @return array<string, string|array<string, mixed>|Rule>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|size:6',
            'encrypted_secret' => 'required|string',
        ];
    }
}

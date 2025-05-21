<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, Password|string>
     */
    protected function passwordRules(): array
    {
        $passwordRule = Password::min(12)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();

        return ['required', 'string', $passwordRule, 'confirmed'];
    }
}

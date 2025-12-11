<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\App;

use function filter_var;
use function is_string;
use function preg_match;
use function str_starts_with;

use const FILTER_VALIDATE_URL;

class UriValidatorRule implements ValidationRule
{
    private const string ERROR_INVALID_URI = 'validation.uri.invalid_uri';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail(self::ERROR_INVALID_URI)->translate(['uri' => $value]);
            return;
        }

        $isValidUrl = filter_var($value, FILTER_VALIDATE_URL);
        $deeplinkRegex = '/^[a-zA-Z][a-zA-Z0-9+.-]*:\/\/.+$/';
        $isValidDeeplink = preg_match($deeplinkRegex, $value);

        if (!$isValidUrl && !$isValidDeeplink) {
            $fail(self::ERROR_INVALID_URI)->translate(['uri' => $value]);
            return;
        }

        if (!App::isLocal() && !App::runningUnitTests() && str_starts_with($value, 'http://')) {
            $fail(self::ERROR_INVALID_URI)->translate(['uri' => $value]);
        }
    }
}

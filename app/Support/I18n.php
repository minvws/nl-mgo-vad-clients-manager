<?php

declare(strict_types=1);

namespace App\Support;

use Webmozart\Assert\Assert;

use function __;

class I18n
{
    /**
     * @param array<string, mixed> $attributes The attributes to replace in the translation string.
     */
    public static function trans(string $key, array $attributes = []): string
    {
        $translation = __($key, $attributes);

        Assert::string($translation);

        return $translation;
    }
}

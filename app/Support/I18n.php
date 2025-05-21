<?php

declare(strict_types=1);

namespace App\Support;

use Webmozart\Assert\Assert;

use function __;

class I18n
{
    public static function trans(string $key): string
    {
        $translation = __($key);

        Assert::string($translation);

        return $translation;
    }
}

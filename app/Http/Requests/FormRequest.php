<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as IlluminateFormRequest;
use Webmozart\Assert\Assert;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class FormRequest extends IlluminateFormRequest
{
    public function getString(string $key): string
    {
        $value = $this->get($key);
        Assert::string($value);

        return $value;
    }

    public function getStringWithDefault(string $key, ?string $default): string
    {
        $value = $this->get($key) ?? $default;
        Assert::string($value);

        return $value;
    }

    public function getStringOrNull(string $key): ?string
    {
        $value = $this->get($key);
        Assert::nullOrString($value);

        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function getValidatedAttributes(): array
    {
        /**
         * @var array<string, mixed> $attributes
         */
        $attributes = $this->validated();

        return $attributes;
    }
}

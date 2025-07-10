<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Webmozart\Assert\Assert;

use function array_map;
use function is_string;
use function parse_url;
use function strcasecmp;
use function trim;

class FQDNMatchRule implements ValidationRule, DataAwareRule
{
    private const string ERROR_INVALID_REDIRECT_URI = 'validation.dependent_fqdn.invalid_redirect_uri';
    private const string ERROR_HOST_MISMATCH = 'validation.dependent_fqdn.host_mismatch';

    /** @var array<string, mixed> */
    protected array $data = [];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $fqdn = $this->getFqdn();
        if ($fqdn === null || $value === null) {
            return;
        }

        $values = is_string($value) ? [$value] : $value;
        Assert::allString($values);

        $this->validateRedirectUris($fqdn, (array) $values, $fail);
    }

    private function getFqdn(): ?string
    {
        if (isset($this->data['fqdn'])) {
            Assert::string($this->data['fqdn']);
            return $this->data['fqdn'];
        }
        if (isset($this->data['client_fqdn'])) {
            Assert::string($this->data['client_fqdn']);
            return $this->data['client_fqdn'];
        }
        return null;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param array<string> $values The redirect URIs to validate.
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    private function validateRedirectUris(string $fqdnHost, array $values, Closure $fail): void
    {
        $redirectUris = $this->prepareRedirectUris($values);

        foreach ($redirectUris as $uri) {
            $this->validateSingleRedirectUri($uri, $fqdnHost, $fail);
        }
    }

    /**
     * @param array<mixed> $values The raw redirect URIs to prepare.
     *
     * @return array<int, string> The trimmed redirect URIs.
     */
    private function prepareRedirectUris(array $values): array
    {
        return array_map(
            static fn (mixed $item): string => is_string($item) ? trim($item) : '',
            $values,
        );
    }

    /**
     * @param string $uri The redirect URI to validate.
     * @param string $fqdnHost The FQDN host to compare against.
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    private function validateSingleRedirectUri(string $uri, string $fqdnHost, Closure $fail): void
    {
        $parsedUri = parse_url($uri);
        if ($parsedUri === false || !isset($parsedUri['host'])) {
            $fail(self::ERROR_INVALID_REDIRECT_URI)->translate(['uri' => $uri]);
            return;
        }

        $uriHost = (string) $parsedUri['host'];
        if (strcasecmp($uriHost, $fqdnHost) === 0) {
            return;
        }

        $fail(self::ERROR_HOST_MISMATCH)->translate([
            'uri' => $uri,
            'fqdnHost' => $fqdnHost,
        ]);
    }
}

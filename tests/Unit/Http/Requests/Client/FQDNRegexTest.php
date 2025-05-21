<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests\Client;

use App\Http\Requests\Client\CreateRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function preg_match;
use function sprintf;
use function str_repeat;

class FQDNRegexTest extends TestCase
{
    /**
     * @return array<string, array{string, bool}>
     */
    public static function fqdnProvider(): array
    {
        return [
            'valid domain' => ['example.com', true],
            'valid subdomain' => ['sub.example.com', true],
            'valid multi-level subdomain' => ['sub.sub.example.com', true],
            'valid domain with numbers' => ['example123.com', true],
            'valid domain with hyphens' => ['example-domain.com', true],
            'valid domain with mixed case' => ['Example.com', true],
            'valid domain with maximum length' => ['a' . str_repeat('.a', 63) . '.com', true],
            'invalid domain too short' => ['a.c', false],
            'invalid domain with underscore' => ['example_domain.com', false],
            'invalid domain with spaces' => ['example domain.com', false],
            'invalid domain with special chars' => ['example@domain.com', false],
            'invalid domain starting with hyphen' => ['-example.com', false],
            'invalid domain ending with hyphen' => ['example-.com', false],
            'invalid domain with invalid tld' => ['example.123', false],
            'invalid domain with single character tld' => ['example.a', false],
            'invalid domain with empty parts' => ['example..com', false],
            'Valid domain with protocol is not allowed' => ['https://example..com', false],
        ];
    }

     #[DataProvider('fqdnProvider')]
    public function testFQDNRegex(string $fqdn, bool $expected): void
    {
        $this->assertSame(
            $expected,
            (bool) preg_match(CreateRequest::FQDN_REG_EX, $fqdn),
            sprintf('FQDN "%s" should %s be valid', $fqdn, $expected ? '' : 'not'),
        );
    }
}

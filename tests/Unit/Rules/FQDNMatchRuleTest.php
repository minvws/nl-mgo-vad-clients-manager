<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\FQDNMatchRule;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FQDNMatchRuleTest extends TestCase
{
    /**
     * @param array<mixed> $errors Reference to an array to collect error details.
     */
    protected function getFailCallback(array &$errors): Closure
    {
        return function ($message) use (&$errors) {
            return new class ($message, $errors) {
                private string $message;
                private array $errors;

                /** @param array<mixed> $errors */
                public function __construct(string $message, array &$errors)
                {
                    $this->message = $message;
                    $this->errors = &$errors;
                }

                /** @param array<str,str> $replacements */
                public function translate(array $replacements = []): void
                {
                    $this->errors[] = [
                        'message' => $this->message,
                        'replacements' => $replacements,
                    ];
                }
            };
        };
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function dataProvider(): array
    {
        return [
            'no fqdn' => [
                [],
                '',
                0,
                '',
                [],
            ],

            'double redirect uri is not allowed' => [
                [
                    'fqdn' => 'example.com',
                ],
                ['http://example.com/1', 'http://example.com/1'],
                0,
                '',
                [],
            ],
            'invalid redirect uri' => [
                [
                    'fqdn' => 'example.com',
                ],
                ['http://example.com', 'invalid-uri'],
                1,
                'validation.dependent_fqdn.invalid_redirect_uri',
                ['uri' => 'invalid-uri'],
            ],
            'host mismatch' => [
                [
                    'fqdn' => 'example.com',
                ],
                ['http://example.com', 'http://mismatch.com'],
                1,
                'validation.dependent_fqdn.host_mismatch',
                ['uri' => 'http://mismatch.com', 'fqdnHost' => 'example.com'],
            ],
            'valid redirect uris' => [
                [
                    'fqdn' => 'example.com',
                ],
                ['http://example.com', 'http://example.com'],
                0,
                '',
                [],
            ],
        ];
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $expectedReplacements
     */
    #[DataProvider('dataProvider')]
    public function testDependentFQDNRule(
        array $data,
        mixed $value,
        int $expectedErrorCount,
        string $expectedMessage,
        array $expectedReplacements,
    ): void {
        $errors = [];
        $rule = new FQDNMatchRule();
        $rule->setData($data);
        $rule->validate('attribute', $value, $this->getFailCallback($errors));

        $this->assertCount($expectedErrorCount, $errors);
        if ($expectedErrorCount <= 0) {
            return;
        }

        $this->assertEquals($expectedMessage, $errors[0]['message']);
        $this->assertEquals($expectedReplacements, $errors[0]['replacements']);
    }
}

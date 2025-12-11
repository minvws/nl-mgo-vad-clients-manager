<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\UriValidatorRule;
use Closure;
use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

use function app;

class UriValidatorRuleTest extends TestCase
{
    use InteractsWithContainer;

    /**
     * @param array<array{message: string, replacements: array<string, string>}> &$errors
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

                /** @param array<mixed> $replacements */
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
            'valid http url in development' => [
                'http://example.com',
                'local',
                0,
            ],
            'valid http url in testing' => [
                'http://example.com',
                'local',
                0,
            ],
            'valid http url in production' => [
                'http://example.com',
                'production',
                1,
            ],
            'valid https url' => [
                'https://example.com/path',
                'production',
                0,
            ],
            'valid deeplink' => [
                'myapp://path',
                'production',
                0,
            ],
            'invalid url' => [
                'invalid-uri',
                'production',
                1,
            ],
            'not a string' => [
                123,
                'production',
                1,
            ],
            'valid deeplink in production' => [
                'customscheme://some/path',
                'production',
                0,
            ],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testUriValidatorRule(mixed $value, string $environment, int $expectedErrorCount): void
    {
        app()->instance('env', $environment);

        $errors = [];
        $rule = new UriValidatorRule();
        $rule->validate('attribute', $value, $this->getFailCallback($errors));

        $this->assertCount($expectedErrorCount, $errors);
    }

    protected function tearDown(): void
    {
        App::clearResolvedInstances();

        parent::tearDown();
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Http\CspPolicy;
use App\Support\Environment;
use Illuminate\Foundation\Vite;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Helpers\ConfigHelper;
use Tests\TestCase;

use function config_path;
use function route;

class CspPolicyTest extends TestCase
{
    #[DataProvider('requiredLocalCspHeaders')]
    public function testHeaderContainsCspPolicy(string $requiredCspHeader): void
    {
        ConfigHelper::set('csp.enabled', true);

        $response = $this->get(route('login'));
        $headers = $response->headers;
        $cspHeaders = $headers->get('Content-Security-Policy');

        $this->assertStringContainsString($requiredCspHeader, $cspHeaders);
        $this->assertStringNotContainsString('upgrade-insecure-requests', $cspHeaders);
    }

    public static function requiredLocalCspHeaders(): array
    {
        return [
            ["base-uri 'none'"],
            ["block-all-mixed-content"],
            ["connect-src 'self' data:"],
            ["default-src 'self' data:"],
            ["font-src 'self' data:"],
            ["form-action 'self'"],
            ["frame-src 'self'"],
            ["frame-ancestors 'self'"],
            ["img-src 'self' data: blob:"],
            ["media-src 'self'"],
            ["object-src 'none'"],
            ["script-src 'self'"],
            ["style-src 'self'"],
            ["worker-src 'self'"],
        ];
    }

    public function testCspDisabled(): void
    {
        ConfigHelper::set('csp.enabled', false);

        $response = $this->get(route('login'));
        $headers = $response->headers;
        $cspHeaders = $headers->get('Content-Security-Policy');

        $this->assertNull($cspHeaders);
    }

    public function testCspHeadersForLocalDevelopment(): void
    {
        /** @var Environment&MockInterface $environment */
        $environment = $this->mock(Environment::class);
        $environment->expects('isDevelopmentOrTesting')
            ->times(3)
            ->andReturn(true);

        /** @var Vite&MockInterface $vite */
        $vite = $this->mock(Vite::class);
        $vite->expects('hotFile')
            ->andReturn(config_path('csp.php')); // return path to some "random" file to satisfy is_file()

        $cspPolicy = new CspPolicy($environment, $vite);
        $policy = $cspPolicy->configure();

        $this->assertStringContainsString('http://127.0.0.1:5173', $policy->__toString());
        $this->assertStringContainsString('ws://127.0.0.1:5173', $policy->__toString());
        $this->assertStringNotContainsString('upgrade-insecure-requests', $policy->__toString());
    }

    public function testCspHeadersForProduction(): void
    {
        /** @var Environment&MockInterface $environment */
        $environment = $this->mock(Environment::class);
        $environment->expects('isDevelopmentOrTesting')
            ->times(3)
            ->andReturn(false);

        /** @var Vite&MockInterface $vite */
        $vite = $this->mock(Vite::class);

        $cspPolicy = new CspPolicy($environment, $vite);
        $policy = $cspPolicy->configure();

        $this->assertStringNotContainsString('http://127.0.0.1:5173', $policy->__toString());
        $this->assertStringNotContainsString('ws://127.0.0.1:5173', $policy->__toString());
        $this->assertStringContainsString('upgrade-insecure-requests', $policy->__toString());
    }
}

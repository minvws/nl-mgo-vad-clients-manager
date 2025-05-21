<?php

declare(strict_types=1);

namespace Tests\Feature\Support;

use App\Support\ExceptionRenderer;
use Illuminate\Foundation\Vite;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

use function config_path;

class ExceptionRendererTest extends TestCase
{
    #[Test]
    public function testCssMethod(): void
    {
        $css = ExceptionRenderer::css();

        $this->assertStringContainsString('<style nonce="', $css);
    }

    #[Test]
    public function testJsMethod(): void
    {
        $viteMock = $this->createMock(Vite::class);
        $viteMock->method('hotFile')->willReturn(config_path('csp.php'));
        $this->app->instance(Vite::class, $viteMock);

        $js = ExceptionRenderer::js();

        $this->assertStringContainsString('<script nonce="', $js);
    }
}

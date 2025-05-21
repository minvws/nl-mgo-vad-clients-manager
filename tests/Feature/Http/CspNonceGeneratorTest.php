<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Http\CspNonceGenerator;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

class CspNonceGeneratorTest extends TestCase
{
    public function testNonceShouldEqualViteCspNonce(): void
    {
        /** @var CspNonceGenerator $cspNonceGenerator */
        $cspNonceGenerator = $this->app->get(CspNonceGenerator::class);
        $nonce = $cspNonceGenerator->generate();

        $this->assertEquals($nonce, Vite::cspNonce());
    }
}

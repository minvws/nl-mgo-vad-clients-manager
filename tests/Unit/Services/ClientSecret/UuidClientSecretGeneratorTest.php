<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ClientSecret;

use App\Services\ClientSecret\UuidClientSecretGenerator;
use PHPUnit\Framework\TestCase;

use function strlen;

class UuidClientSecretGeneratorTest extends TestCase
{
    public function testGeneratesDifferentSecretsOnMultipleCalls(): void
    {
        $generator = new UuidClientSecretGenerator();
        $secret1 = $generator->generate();
        $secret2 = $generator->generate();

        $this->assertNotEquals($secret1, $secret2);
    }

    public function testGeneratedSecretIsExpectedSize(): void
    {
        $generator = new UuidClientSecretGenerator();

        $secret = $generator->generate();

        $this->assertTrue(strlen($secret) === 36);
    }
}

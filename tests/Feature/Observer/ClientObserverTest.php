<?php

declare(strict_types=1);

namespace Tests\Feature\Observer;

use App\Observers\ClientObserver;
use App\Services\ClientChangeNotifier;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(ClientObserver::class)]
class ClientObserverTest extends TestCase
{
    #[DataProvider('observerMethodsProvider')]
    public function testNotifierIsTriggeredForObserverMethods(string $method): void
    {
        $mockNotifier = $this->getMockBuilder(ClientChangeNotifier::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['notify'])
            ->getMock();



        $observer = new ClientObserver($mockNotifier, 'http://example.com/notify');

        $mockNotifier->expects($this->once())
            ->method('notify');

        $observer->{$method}();
    }

    public static function observerMethodsProvider(): array
    {
        return [
            ['created'],
            ['updated'],
            ['deleted'],
        ];
    }
}

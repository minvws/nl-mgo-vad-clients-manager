<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Services\ClientChangeNotifier;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

use function array_key_exists;
use function in_array;

class ClientChangeNotifierTest extends TestCase
{
    public function testNotifySendsPostAndLogs(): void
    {
        $urls = [
            'https://example.com/notify1',
            'https://example.com/notify2',
        ];

        Http::fake([
            'https://example.com/notify1' => Http::response('', 200),
            'https://example.com/notify2' => Http::response('', 200),
        ]);

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->expects($this->exactly(2))
            ->method('debug')
            ->with(
                $this->equalTo('Notifying client change'),
                $this->callback(function ($context) {
                    return
                        isset($context['url'])
                        && isset($context['timestamp'])
                        && $context['response_status'] === 200;
                }),
            );

        $notifier = new ClientChangeNotifier($logger, true, $urls[0], $urls[1]);
        $notifier->notify();

        Http::assertSentCount(2);
        Http::assertSent(function ($request) use ($urls) {
            return in_array($request->url(), $urls, true);
        });
    }

    public function testNotifyHandlesRequestException(): void
    {
        $urls = [
            'https://example.com/notify1',
            'https://example.com/notify2',
        ];

        Http::fake([
            'https://example.com/notify1' => Http::response('', 500),
            'https://example.com/notify2' => Http::response('', 200),
        ]);

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->expects($this->once())
            ->method('error')
            ->with(
                $this->equalTo('Failed to notify client change'),
                $this->callback(function ($context) {
                    return
                        $context['url'] === 'https://example.com/notify1'
                        && isset($context['timestamp'])
                        && isset($context['error']);
                }),
            );

        $notifier = new ClientChangeNotifier($logger, true, ...$urls);
        $notifier->notify();

        Http::assertSentCount(4);
        Http::assertSent(function ($request) use ($urls) {
            return in_array($request->url(), $urls, true);
        });
    }

    public function testNotifyHandlesInvalidUrl(): void
    {
        $invalidUrl = 'not-a-valid-url';
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->expects($this->once())
            ->method('warning')
            ->with(
                $this->equalTo('Invalid URL provided for client change notification'),
                $this->callback(function ($context) use ($invalidUrl) {
                    return $context['url'] === $invalidUrl;
                }),
            );

        $notifier = new ClientChangeNotifier($logger, true, $invalidUrl);
        $notifier->notify();

        Http::assertNothingSent();
    }

    #[DataProvider('verifySslDataProvider')]
    public function testNotifyWithDifferentVerifySslSettings(bool $verifySsl): void
    {
        $url = 'https://example.com/notify';
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->expects($this->once())
            ->method('debug')
            ->with(
                $this->equalTo('Notifying client change'),
                $this->callback(function ($context) {
                    return
                        isset($context['url'])
                        && isset($context['timestamp'])
                        && $context['response_status'] === 200;
                }),
            );

        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('status')->andReturn(200);
        $responseMock->shouldReceive('throw')->andReturnSelf();

        $pendingRequestMock = Mockery::mock(PendingRequest::class);
        $pendingRequestMock->shouldReceive('retry')->andReturnSelf();
        $pendingRequestMock->shouldReceive('post')->with($url)->andReturn($responseMock);

        Http::shouldReceive('withOptions')
            ->once()
            ->with(Mockery::on(fn($options) => array_key_exists('verify', $options) && $options['verify'] === $verifySsl))
            ->andReturn($pendingRequestMock);

        $notifier = new ClientChangeNotifier($logger, $verifySsl, $url);
        $notifier->notify();
    }

    /**
     * @return array<string, array<bool>>
     */
    public static function verifySslDataProvider(): array
    {
        return [
            'verify SSL enabled' => [true],
            'verify SSL disabled' => [false],
        ];
    }
}

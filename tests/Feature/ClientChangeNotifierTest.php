<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\ClientChangeNotifier;
use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

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

        $notifier = new ClientChangeNotifier($logger, $urls[0], $urls[1]);
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

        $notifier = new ClientChangeNotifier($logger, ...$urls);
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

        $notifier = new ClientChangeNotifier($logger, $invalidUrl);
        $notifier->notify();

        Http::assertNothingSent();
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class FlashNotificationTest extends TestCase
{
    #[TestWith([FlashNotificationTypeEnum::CONFIRMATION], 'Confirmation flash notification')]
    #[TestWith([FlashNotificationTypeEnum::WARNING], 'Warning flash notification')]
    #[TestWith([FlashNotificationTypeEnum::ERROR], 'Error flash notification')]
    #[TestWith([FlashNotificationTypeEnum::EXPLANATION], 'Explanation flash notification')]
    public function testFlash(FlashNotificationTypeEnum $type): void
    {
        $flashNotification = new FlashNotification(type: $type, message: 'message');

        $this->assertSame($type, $flashNotification->getType());
        $this->assertSame('message', $flashNotification->getMessage());
    }

    public function testAdditionalMessages(): void
    {
        $additionalMessages = [
            'additional message a',
            'additional message b',
        ];

        $flashNotification = new FlashNotification(
            type: FlashNotificationTypeEnum::WARNING,
            message: 'message',
            additionMessages: $additionalMessages,
        );

        $this->assertTrue($flashNotification->hasAdditionalMessages());
        $this->assertSame($additionalMessages, $flashNotification->getAdditionalMessages());
    }
}

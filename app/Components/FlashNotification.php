<?php

declare(strict_types=1);

namespace App\Components;

use App\Enums\FlashNotificationTypeEnum;

use function count;

class FlashNotification
{
    public const SESSION_KEY = 'flash_notification';

    /**
     * @param array<string> $additionMessages
     */
    public function __construct(
        protected FlashNotificationTypeEnum $type,
        protected string $message,
        protected array $additionMessages = [],
    ) {
    }

    public function getType(): FlashNotificationTypeEnum
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function hasAdditionalMessages(): bool
    {
        return count($this->additionMessages) > 0;
    }

    /**
     * @return array<string>
     */
    public function getAdditionalMessages(): array
    {
        return $this->additionMessages;
    }
}

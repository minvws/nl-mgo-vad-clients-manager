<?php

declare(strict_types=1);

namespace Tests\Unit\View\Components;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use App\View\Components\Flash;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

use function sprintf;
use function trans;

class FlashTest extends TestCase
{
    #[TestWith(
        [
            FlashNotificationTypeEnum::CONFIRMATION,
            'confirmation',
            'general.flash.success',
            'general.flash.aria-label.success',
        ],
        'Confirmation flash notification',
    )]
    #[TestWith(
        [
            FlashNotificationTypeEnum::WARNING,
            'warning',
            'general.flash.warning',
            'general.flash.aria-label.warning',
        ],
        'Warning flash notification',
    )]
    #[TestWith(
        [
            FlashNotificationTypeEnum::ERROR,
            'error',
            'general.flash.error',
            'general.flash.aria-label.error',
        ],
        'Error flash notification',
    )]
    #[TestWith(
        [
            FlashNotificationTypeEnum::EXPLANATION,
            'explanation',
            'general.flash.info',
            'general.flash.aria-label.info',
        ],
        'Explanation flash notification',
    )]
    public function testFlashSuccess(
        FlashNotificationTypeEnum $type,
        string $cssStyle,
        string $translationKey,
        string $ariaLabel,
    ): void
    {
        $flashNotification = new FlashNotification(type: $type, message: 'message');
        $this->withSession([FlashNotification::SESSION_KEY => $flashNotification]);

        $view = $this->component(Flash::class);
        $view->assertSeeText(trans($translationKey));
        $view->assertSee(sprintf('class="%s"', $cssStyle), false);
        $view->assertSee(sprintf('aria-label="%s"', trans($ariaLabel)), false);
    }
}

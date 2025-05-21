<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Components\FlashNotification;
use App\Enums\FlashNotificationTypeEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;

use function assert;
use function trans;

class Flash extends Component
{
    public function render(): View
    {
        $flash = Session::get('flash_notification');
        assert($flash instanceof FlashNotification);

        $type = $flash->getType();

        return $this->view('components.flash', [
            'cssStyles' => $this->getStyleClasses($type),
            'ariaLabel' => $this->getAriaLabel($type),
            'header' => $this->getHeader($type),
            'flash' => $flash,
        ]);
    }

    public function shouldRender(): bool
    {
        return Session::has(FlashNotification::SESSION_KEY);
    }

    public function getStyleClasses(FlashNotificationTypeEnum $type): string
    {
        return match ($type) {
            FlashNotificationTypeEnum::ERROR => 'error',
            FlashNotificationTypeEnum::CONFIRMATION => 'confirmation',
            FlashNotificationTypeEnum::WARNING => 'warning',
            FlashNotificationTypeEnum::EXPLANATION => 'explanation',
        };
    }

    public function getHeader(FlashNotificationTypeEnum $type): string
    {
        return match ($type) {
            FlashNotificationTypeEnum::ERROR => trans('general.flash.error'),
            FlashNotificationTypeEnum::WARNING => trans('general.flash.warning'),
            FlashNotificationTypeEnum::CONFIRMATION => trans('general.flash.success'),
            FlashNotificationTypeEnum::EXPLANATION => trans('general.flash.info'),
        };
    }

    public function getAriaLabel(FlashNotificationTypeEnum $type): string
    {
        return match ($type) {
            FlashNotificationTypeEnum::ERROR => trans('general.flash.aria-label.error'),
            FlashNotificationTypeEnum::WARNING => trans('general.flash.aria-label.warning'),
            FlashNotificationTypeEnum::CONFIRMATION => trans('general.flash.aria-label.success'),
            FlashNotificationTypeEnum::EXPLANATION => trans('general.flash.aria-label.info'),
        };
    }
}

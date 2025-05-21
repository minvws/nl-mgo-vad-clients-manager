<?php

declare(strict_types=1);

namespace App\Enums;

enum FlashNotificationTypeEnum: string
{
    case ERROR = 'error';
    case WARNING = 'warning';
    case CONFIRMATION = 'confirmation';
    case EXPLANATION = 'explanation';
}

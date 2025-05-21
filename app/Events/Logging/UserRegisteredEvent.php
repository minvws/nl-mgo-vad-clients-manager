<?php

declare(strict_types=1);

namespace App\Events\Logging;

class UserRegisteredEvent extends AppLogEvent
{
    public const EVENT_CODE = '2044';
    public const EVENT_KEY = 'user_registered';

    public function __construct()
    {
        parent::__construct();

        $this->actionCode = self::AC_UPDATE;
    }
}

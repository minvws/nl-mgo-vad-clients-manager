<?php

declare(strict_types=1);

namespace App\Events\Logging;

class UserPasswordResetEvent extends AppLogEvent
{
    public const EVENT_CODE = '2045';
    public const EVENT_KEY = 'user_password_reset';

    public function __construct()
    {
        parent::__construct();

        $this->actionCode = self::AC_UPDATE;
    }
}

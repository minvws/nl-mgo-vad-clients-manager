<?php

declare(strict_types=1);

namespace App\Events\Logging;

class UpdateUserEvent extends AppLogEvent
{
    public const EVENT_CODE = '2043';
    public const EVENT_KEY = 'update_user';

    public function __construct()
    {
        parent::__construct();

        $this->actionCode = self::AC_UPDATE;
    }
}

<?php

declare(strict_types=1);

namespace App\Events\Logging;

class ViewUserEvent extends AppLogEvent
{
    public const EVENT_CODE = '2042';
    public const EVENT_KEY = 'view_user';

    public function __construct()
    {
        parent::__construct();

        $this->actionCode = self::AC_READ;
    }
}

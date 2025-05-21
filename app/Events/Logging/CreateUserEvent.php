<?php

declare(strict_types=1);

namespace App\Events\Logging;

class CreateUserEvent extends AppLogEvent
{
    public const EVENT_CODE = '2041';
    public const EVENT_KEY = 'create_user';

    public function __construct()
    {
        parent::__construct();

        $this->actionCode = self::AC_CREATE;
    }
}

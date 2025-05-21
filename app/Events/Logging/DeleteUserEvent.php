<?php

declare(strict_types=1);

namespace App\Events\Logging;

class DeleteUserEvent extends AppLogEvent
{
    public const EVENT_CODE = '2044';
    public const EVENT_KEY = 'delete_user';

    public function __construct()
    {
        parent::__construct();

        $this->actionCode = self::AC_DELETE;
    }
}

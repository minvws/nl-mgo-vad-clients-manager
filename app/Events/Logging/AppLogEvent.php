<?php

declare(strict_types=1);

namespace App\Events\Logging;

use MinVWS\AuditLogger\Events\Logging\GeneralLogEvent;

use function assert;
use function config;
use function is_string;

abstract class AppLogEvent extends GeneralLogEvent
{
    public function __construct()
    {
        parent::__construct();

        $source = config('app.name');
        assert(is_string($source));

        $this->source = $source;
    }
}

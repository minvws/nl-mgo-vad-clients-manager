<?php

declare(strict_types=1);

namespace App\Support;

use App\Exception\AppException;
use Illuminate\Support\Facades\App;

use function is_bool;

class Environment
{
    /**
     * @throws AppException
     */
    public function isDevelopment(): bool
    {
        return self::isEnvironment(['dev', 'development', 'local']);
    }

    /**
     * @throws AppException
     */
    public function isProduction(): bool
    {
        return self::isEnvironment(['production']);
    }

    /**
     * @throws AppException
     */
    public function isTesting(): bool
    {
        return self::isEnvironment(['test', 'testing']);
    }

    /**
     * @throws AppException
     */
    public function isDevelopmentOrTesting(): bool
    {
        return $this->isDevelopment() || $this->isTesting();
    }

    /**
     * @param array<string> $environmentNames
     *
     * @throws AppException
     */
    private static function isEnvironment(array $environmentNames): bool
    {
        $environment = App::environment($environmentNames);

        if (!is_bool($environment)) {
            throw new AppException('Unable to determine environment');
        }

        return $environment;
    }
}

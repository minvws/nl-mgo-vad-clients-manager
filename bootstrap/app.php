<?php

declare(strict_types=1);

use App\Support\Config;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Csp\AddCspHeaders;
use Webmozart\Assert\Assert;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__ . '/../routes/web.php', api: __DIR__ . '/../routes/api.php', health: '/up')
    ->withMiddleware(static function (Middleware $middleware): void {
        $middleware->trustHosts(static function (): array {
            $trustedHosts = Config::array('app.trusted_hosts');
            Assert::allString($trustedHosts);

            return $trustedHosts;
        });
        $middleware->web(append: [
            AddCspHeaders::class,
        ]);
    })
    //phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    ->withExceptions(static function (Exceptions $exceptions): void {
    })
    ->create();

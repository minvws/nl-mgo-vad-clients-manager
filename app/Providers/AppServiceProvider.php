<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use App\Services\ClientChangeNotifier;
use App\Support\ExceptionRenderer;
use App\Support\Str\Initials;
use Barryvdh\Debugbar\Facades\Debugbar;
use Barryvdh\Debugbar\LaravelDebugbar;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Exceptions\Renderer\Listener;
use Illuminate\Foundation\Exceptions\Renderer\Mappers\BladeMapper;
use Illuminate\Foundation\Exceptions\Renderer\Renderer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Throwable;

use function app;
use function class_exists;
use function config;
use function csp_nonce;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        /**
         * @var Initials $initials
         */
        $initials = $this->app->make(Initials::class);

        Str::macro('initials', $initials());
        Stringable::macro('initials', $initials());

        Vite::useCspNonce();

        // this will allow us to use our own custom error views
        if ($this->app->environment('local') !== true) {
            return;
        }

        // @codeCoverageIgnoreStart
        // This bit of code is only used in local environment so it's not necessary to test it
        $this->app->bind(Renderer::class, static function (Application $app) {
            $errorRenderer = new HtmlErrorRenderer($app->hasDebugModeEnabled());
            /**
             * @var Factory $factory
             */
            $factory = $app->make(Factory::class);
            /**
             * @var Listener $listener
             */
            $listener = $app->make(Listener::class);
            /**
             * @var BladeMapper $bladeMapper
             */
            $bladeMapper = $app->make(BladeMapper::class);

            return new ExceptionRenderer($factory, $listener, $errorRenderer, $bladeMapper, $app->basePath());
        });

        View::prependNamespace(
            'laravel-exceptions-renderer',
            [__DIR__ . '/../../resources/views/exceptions/renderer'],
        );
        // @codeCoverageIgnoreEnd
    }

    /**
     * Bootstrap any application services.
     *
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);

        Paginator::defaultView('pagination.default');

        // @codeCoverageIgnoreStart
        // This bit of code is only used in local environment so it's not necessary to test it
        // Set CSP nonce for Laravel Debugbar during development
        if (class_exists(Debugbar::class) && $this->app->environment('local') === true && app()->bound('debugbar')) {
            /**
             * @var LaravelDebugbar $debugBar
             */
            $debugBar = $this->app->make('debugbar');
            $debugBar->getJavascriptRenderer()->setCspNonce(csp_nonce());
        }
        // @codeCoverageIgnoreEnd

        if ($this->app->environment('local') === true && config('app.skip_authentication') === true) {
            try {
                $user = User::query()->firstOrFail();
                Auth::loginUsingId($user->id);
            } catch (Throwable) {
                // This is just to make sure we don't crash the application if we can't find user 1 for some reason
                Log::warning('Skipping authentication failed: no user found. Please create a user with ID 1.');
            }
        }

        $this->app->when(ClientChangeNotifier::class)
            ->needs('$notifiables')
            ->giveConfig('services.vad.notifiables', []);
    }
}

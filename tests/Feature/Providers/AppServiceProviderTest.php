<?php

declare(strict_types=1);

namespace Tests\Feature\Providers;

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

use function config;

class AppServiceProviderTest extends TestCase
{
    public function testAutoLoginWhenInLocalEnvironmentWithSkipAuthentication(): void
    {
        $user = User::factory()->create();

        $this->app->detectEnvironment(function () {
            return 'local';
        });
        Config::set('app.skip_authentication', true);

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        $this->assertEquals($user->id, Auth::id());
    }

    public function testNoAutoLoginWhenNotInLocalEnvironment(): void
    {
        User::factory()->create();

        $this->app->detectEnvironment(function () {
            return 'testing';
        });
        Config::set('app.skip_authentication', true);

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        $this->assertNull(Auth::id());
    }

    public function testNoAutoLoginWhenSkipAuthenticationIsFalse(): void
    {
        User::factory()->create();

        $this->app->detectEnvironment(function () {
            return 'local';
        });
        Config::set('app.skip_authentication', false);

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        $this->assertNull(Auth::id());
    }

    public function testAutoLoginCatchBlock(): void
    {
        config(['app.skip_authentication' => true]);
        $this->app['env'] = 'local';
        User::truncate();

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        $this->assertFalse(Auth::check());
    }
}

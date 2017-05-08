<?php

namespace Timonline\AuthPubtkt;

use Illuminate\Support\ServiceProvider;

class AuthPubtktServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->bootstrapConfigs();
        $this->subscribe();
        $this->addMiddleware();
    }

    /**
     * Event listeners
     */
    protected function subscribe()
    {
        // \Event::listen('Illuminate\Auth\Events\Registered', Listeners\Registered::class);
        // \Event::listen('Illuminate\Auth\Events\Attempting', Listeners\Attempting::class);
        // \Event::listen('Illuminate\Auth\Events\Authenticated', Listeners\Authenticated::class);
        \Event::listen('Illuminate\Auth\Events\Login', Listeners\Login::class);
        // \Event::listen('Illuminate\Auth\Events\Failed', Listeners\Failed::class);
        \Event::listen('Illuminate\Auth\Events\Logout', Listeners\Logout::class);
        // \Event::listen('Illuminate\Auth\Events\Lockout', Listeners\Lockout::class);
    }

    /**
     * Load and publishes configs.
     */
    protected function bootstrapConfigs()
    {
        $configFile = realpath(__DIR__.'/../config/authpubtkt.php');

        $this->mergeConfigFrom($configFile, 'authpubtkt');
        $this->publishes([$configFile => $this->app['path.config'].'/authpubtkt.php'], 'config');
    }

    protected function addMiddleware()
    {
        $this->app['Illuminate\Contracts\Http\Kernel']
            ->pushMiddleware(Http\Middleware\AuthPubtktCookie::class);
    }

}

<?php

namespace Drox\Rdw;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class RdwServiceProvider extends ServiceProvider
{
    /**
     * @param Filesystem $filesystem
     * @return void
     */
    public function boot(Filesystem $filesystem)
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Rdw::class, function ($app) {
            return new Rdw($app);
        });

        $this->app->alias(Rdw::class, 'rdw');
    }
}

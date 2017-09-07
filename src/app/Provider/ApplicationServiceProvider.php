<?php
namespace AdrianTilita\ResourceExposer\Provider;

use AdrianTilita\ResourceExposer\Console\GenerateCommand;
use Illuminate\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    /**
     * Register routes, translations, views and publishers.
     *
     * @return void
     */
    public function boot()
    {
        $app = $this->app;
        if (!$this->app->routesAreCached()) {
            require realpath(__DIR__.'/../Http/web.php');
        }
        $this->commands([
            GenerateCommand::class
        ]);
    }
}
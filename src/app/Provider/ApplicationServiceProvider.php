<?php
namespace AdrianTilita\ResourceExposer\Provider;

use AdrianTilita\ResourceExposer\Console\GenerateCommand;
use AdrianTilita\ResourceExposer\Service\ClassSearchService;
use AdrianTilita\ResourceExposer\Service\ModelListService;
use AdrianTilita\ResourceExposer\Service\RequestHandler;
use Illuminate\Database\Eloquent\Model;
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
        // model list service
        $this->app->bind('AdrianTilita\ResourceExposer\Service\ModelListService', function ($app) {
            return new ModelListService(
                new ClassSearchService(app_path(), Model::class)
            );
        });
        $this->app->make('AdrianTilita\ResourceExposer\Service\ModelListService');

        // request handler
        $this->app->bind('AdrianTilita\ResourceExposer\Service\RequestHandler', function ($app) {
            return new RequestHandler($app->make('AdrianTilita\ResourceExposer\Service\ModelListService'));
        });
        $this->app->make('AdrianTilita\ResourceExposer\Service\RequestHandler');
    }
}
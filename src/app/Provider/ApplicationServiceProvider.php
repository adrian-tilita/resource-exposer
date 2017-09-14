<?php
namespace AdrianTilita\ResourceExposer\Provider;

use AdrianTilita\ResourceExposer\Console\SearchModelsCommand;
use AdrianTilita\ResourceExposer\Middleware\RouteMiddleware;
use AdrianTilita\ResourceExposer\Service\ModelListService;
use AdrianTilita\ResourceExposer\Service\RequestHandler;
use Illuminate\Routing\Events\RouteMatched;
use NeedleProject\Common\ClassFinder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

/**
 * Class ApplicationServiceProvider
 * @package AdrianTilita\ResourceExposer\Provider
 */
class ApplicationServiceProvider extends ServiceProvider
{
    /**
     * @const string    Default application identifier - used for route names
     */
    const APPLICATION_IDENTIFIER = 'exposure';

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
            SearchModelsCommand::class
        ]);

        // model list service
        $this->app->bind(ModelListService::class, function () {
            return new ModelListService(
                new ClassFinder(app_path(), Model::class)
            );
        });
        $this->app->make(ModelListService::class);

        // request handler
        $this->app->bind(RequestHandler::class, function ($app) {
            return new RequestHandler($app->make(ModelListService::class));
        });
        $this->app->make(RequestHandler::class);

        // register route middle-ware so we can validate authentication
        $app['router']->matched(function (RouteMatched $routeMatched) {
            if ($routeMatched->route->getName() === static::APPLICATION_IDENTIFIER) {
                $routeMatched->route->middleware(['before' => RouteMiddleware::class]);
            }
        });
    }
}

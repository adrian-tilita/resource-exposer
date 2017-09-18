<?php
use AdrianTilita\ResourceExposer\Service\RequestHandler;
use AdrianTilita\ResourceExposer\Provider\ApplicationServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for the module.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
/**
 * Index route
 */
Route::get('/exposure', function () {
    return new RedirectResponse(URL::to('/') . '/exposure/list', JsonResponse::HTTP_MOVED_PERMANENTLY);
})->name(ApplicationServiceProvider::APPLICATION_IDENTIFIER);

/**
 * Handle /exposure/list route for any
 */
Route::any('/exposure/list', function (HttpRequest $request, RequestHandler $requestHandler) {
    if ($request->getMethod() !== HttpRequest::METHOD_GET) {
        return new JsonResponse(
            "Method not allowed!",
            JsonResponse::HTTP_METHOD_NOT_ALLOWED
        );
    }

    list($response, $statusCode) = $requestHandler->listResources();
    return new JsonResponse($response, $statusCode);
})->name(ApplicationServiceProvider::APPLICATION_IDENTIFIER);

/**
 * Individual resource
 */
Route::get('exposure/{resourceName}/{id}', function (RequestHandler $requestHandler, string $resourceName, int $id) {
    list($response, $statusCode) = $requestHandler->getResource($resourceName, $id);
    return new JsonResponse($response, $statusCode);
})->name(ApplicationServiceProvider::APPLICATION_IDENTIFIER)
    ->where('resourceName', '([A-Za-z0-9_-]+)')
    ->where('id', '[(0-9)+]');

/**
 * Collection list
 */
Route::get('exposure/{resourceName}', function (RequestHandler $requestHandler, HttpRequest $request, $resourceName) {

    $limit  = $request->request->get('limit', 100);
    $offset = $request->request->get('offset', 0);
    $sortBy = $request->request->get('sortby', 'id');
    $order  = $request->request->get('order', 'asc');

    list($response, $statusCode) = $requestHandler->getResourceCollection(
        $resourceName,
        $limit,
        $offset,
        $sortBy,
        $order
    );

    return new JsonResponse($response, $statusCode);
})->name(ApplicationServiceProvider::APPLICATION_IDENTIFIER)
    ->where('resourceName', '([A-Za-z0-9_-]+)');

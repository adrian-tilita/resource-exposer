<?php
use AdrianTilita\ResourceExposer\Service\RequestHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    return new \Symfony\Component\HttpFoundation\RedirectResponse(
        URL::to('/') . '/exposure/list',
        \Symfony\Component\HttpFoundation\Response::HTTP_MOVED_PERMANENTLY
    );
});

/**
 * Handle /exposure/list route for any
 */
Route::any('/exposure/list', function (Request $request, RequestHandler $requestHandler) {
    if ($request->getMethod() !== Request::METHOD_GET) {
        return new JsonResponse(
            "Method not allowed!",
            \Symfony\Component\HttpFoundation\Response::HTTP_METHOD_NOT_ALLOWED
        );
    }

    list($response, $statusCode) = $requestHandler->listResources();
    return new JsonResponse($response, $statusCode);
});

/**
 * Individual resource
 */
Route::get('exposure/{resourceName}/{id}', function (RequestHandler $requestHandler, string $resourceName, int $id) {

    list($response, $statusCode) = $requestHandler->getResource($resourceName, $id);
    return new JsonResponse($response, $statusCode);

});//->where('resourceName', '([A-Za-z0-9_-]+)')
   // ->where('id', '(0-9]+)');

/**
 * Collection list
 */
Route::get('exposure/{resourceName}', function (RequestHandler $requestHandler, Request $request, $resourceName) {

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

})->where('resourceName', '([A-Za-z0-9_-]+)');





Route::get(
    'exposure/ss{resourceName}/{id?}',
    function (RequestHandler $requestHandler, Request $request, $resourceName, int $id = null) {

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

/*
        $per_page = 100;

        if (!empty($extraFields)) {
            if (preg_match('/per_page\/([0-9]+)/msu', $extraFields, $per_page_result)) {
                $per_page = isset($per_page_result[1]) ? $per_page_result[1] : $per_page;
            }
            if (preg_match('/\/page\/([0-9]+)/msu', $extraFields, $page_nr_result)) {
                $page_nr = isset($page_nr_result[1]) ? $page_nr_result[1] : $page_nr;
            }
        }
        list($response, $statusCode) = $requestHandler->handleFilter(
            $resourceName,
            $filterKey,
            $filterValue,
            $page_nr,
            $per_page
        );
        return new JsonResponse($response, $statusCode);*/
    }
)->where('id', '([0-9]+)');


Route::options('exposure/info', function () {
    return new JsonResponse([
        'routes' => [
            'index' => [
                'url' => '/exposure/list',
                'description' => 'List available exposed resources',
            ],
            'resources' => [
                'url' => '/exposure/filter/[string:resourceName]/[filters]',
                'description' => 'Exposed content for each resource',
                'parameters' => [
                    'resourceName' => [
                        'type' => 'string',
                        'example' => '/exposure/filter/users/[filters]'
                    ],
                    'filter' => [
                        'type' => 'query-string',
                        'allowed_filters' => [
                            'id'    => 'unsigned int',
                            'newer_than' => 'UTC timestamp'
                        ],
                        'example' => [
                            "/exposure/filter/users/id/1/page/2/per_page/1newer_than/",
                            "/exposure/filter/users/newer_than/" .
                                (new DateTime('now', new DateTimeZone('UTC')))->format('U') .
                                "/page/2/per_page/1"
                        ],
                        "optional" => [
                            "page" => "default 1",
                            "per_page" => "default 100"
                        ]
                    ]
                ]
            ]
        ],
        'responses' => [
            'type' => 'json',
            'success' => [
                'status_code' => 200,
                'body' => [
                    'content' => "array",
                    'total' => "int",
                    'current_page' => "int",
                    'per_page' => "int",
                    'page_count' => "int"
                ]
            ],
            'error' => [
                'status_code' => '500|400|404',
                'body' => [
                    'error' => 'string'
                ]
            ]
        ]
    ]);
});

/*
Route::get('exposure/list', function (RequestHandler $requestHandler, Request $request) {
    list($response, $statusCode) = $requestHandler->handleList($request);
    return new JsonResponse($response, $statusCode);
});*/

Route::get(
    'exposure/filter/{resourceName}/{filterKey}/{filterValue}{extraFields?}',
    function (RequestHandler $requestHandler, $resourceName, $filterKey, $filterValue, $extraFields = '') {
        $page_nr  = 1;
        $per_page = 100;

        if (!empty($extraFields)) {
            if (preg_match('/per_page\/([0-9]+)/msu', $extraFields, $per_page_result)) {
                $per_page = isset($per_page_result[1]) ? $per_page_result[1] : $per_page;
            }
            if (preg_match('/\/page\/([0-9]+)/msu', $extraFields, $page_nr_result)) {
                $page_nr = isset($page_nr_result[1]) ? $page_nr_result[1] : $page_nr;
            }
        }
        list($response, $statusCode) = $requestHandler->handleFilter(
            $resourceName,
            $filterKey,
            $filterValue,
            $page_nr,
            $per_page
        );
        return new JsonResponse($response, $statusCode);
    }
)->where('extraFields', '(.*)');

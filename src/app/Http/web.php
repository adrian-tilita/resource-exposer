<?php
use AdrianTilita\ResourceExposer\Service\RequestHandler;
use Symfony\Component\HttpFoundation\JsonResponse;

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
Route::options('exposure/info', function() {
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


Route::get('exposure/list', function(RequestHandler $requestHandler) {
    list($response, $statusCode) = $requestHandler->handleList();
    return new JsonResponse($response, $statusCode);
});

Route::get(
    'exposure/filter/{resourceName}/{filterKey}/{filterValue}{extraFields?}',
    function(RequestHandler $requestHandler, $resourceName, $filterKey, $filterValue, $extraFields = '') {
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
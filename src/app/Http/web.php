<?php
use Illuminate\Support\Facades\Cache;
use AdrianTilita\ResourceExposer\Console\GenerateCommand;
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
Route::get('resource-api/list-resources', function() {
    $resources = Cache::get(GenerateCommand::STORE_KEY);
    return new JsonResponse(array_keys($resources));
});

Route::get('resource-api/get-resource/{resourceName}/{id}', function($resourceName, $id) {
    $resources = Cache::get(GenerateCommand::STORE_KEY);
    /** @var \Illuminate\Database\Eloquent\Model $class */
    $class = "\\" . $resources[$resourceName];
    $resources = ($class::all())->toArray();
    return new JsonResponse($resources);
});
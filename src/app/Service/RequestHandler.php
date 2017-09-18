<?php
namespace AdrianTilita\ResourceExposer\Service;

use AdrianTilita\ResourceExposer\Bridge\ConfigBridge;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RequestHandler
 * @package AdrianTilita\ResourceExposer\Service
 */
class RequestHandler
{
    /**
     * @const string
     */
    const DEFAULT_EXCEPTION_MESSAGE = 'Temporary unavailable!';
    const ORDER_DESC = 'desc';

    /**
     * @var null|ModelListService
     */
    private $modelListService;

    /**
     * RequestHandler constructor.
     * @param ModelListService $modelListService
     */
    public function __construct(ModelListService $modelListService)
    {
        $this->modelListService = $modelListService;
    }

    /**
     * List all application defined resource
     * @return array
     */
    public function listResources()
    {
        try {
            $response = array_keys(
                $this->modelListService->fetchAll()
            );
            $response = $this->adaptList($response);
        } catch (\Throwable $e) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['error' => static::DEFAULT_EXCEPTION_MESSAGE];
        }

        return [
            $response,
            !isset($statusCode) ? Response::HTTP_OK : $statusCode
        ];
    }

    /**
     *
     * @param string $resourceName
     * @return array
     */
    /**
     * Get resource collection
     * @param string $resourceName
     * @param int $limit
     * @param int $offset
     * @param string $sortBy
     * @param string $order
     * @return array
     */
    public function getResourceCollection(
        string $resourceName,
        int $limit,
        int $offset,
        string $sortBy,
        string $order
    ): array {
        try {
            $resources = $this->modelListService->fetchAll();
            // invalid resource name
            if (!isset($resources[$resourceName])) {
                return [
                    ['error' => sprintf('Resource %s does not exists!', $resourceName)],
                    Response::HTTP_NOT_FOUND
                ];
            }

            /** @var Model $model */
            $model = $resources[$resourceName];
            $collection = $model::all();
            if ($order === static::ORDER_DESC) {
                $collection = $collection->sortByDesc($sortBy);
            } else {
                $collection = $collection->sortBy($sortBy);
            }
            $collection = $collection->slice($offset, $limit);

            $response = $this->adaptContent($collection, $resources[$resourceName]);
        } catch (\Throwable $e) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['error' => $e->getMessage() . static::DEFAULT_EXCEPTION_MESSAGE];
        }

        return [
            $response,
            !isset($statusCode) ? Response::HTTP_OK : $statusCode
        ];
    }

    /**
     * Get individual Resource
     * @param string $resourceName
     * @param int $resourceId
     * @return array
     */
    public function getResource(string $resourceName, int $resourceId): array
    {
        try {
            $resources = $this->modelListService->fetchAll();
            // invalid resource name
            if (!isset($resources[$resourceName])) {
                return [
                    ['error' => sprintf('Resource %s does not exists!', $resourceName)],
                    Response::HTTP_NOT_FOUND
                ];
            }

            /** @var Model $model */
            $model = (new $resources[$resourceName])->find($resourceId);
            if (is_null($model)) {
                return [
                    [
                        'error' => sprintf(
                            'Resource %s identified by %d could not be found!',
                            $resourceName,
                            $resourceId
                        )
                    ],
                    Response::HTTP_NOT_FOUND
                ];
            }

            $response = $this->adaptContent(Collection::make([$model]), $resources[$resourceName]);
            $response = array_pop($response);
        } catch (\Throwable $e) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['error' => $e->getMessage() . static::DEFAULT_EXCEPTION_MESSAGE];
        }

        return [
            $response,
            !isset($statusCode) ? Response::HTTP_OK : $statusCode
        ];
    }

    /**
     * @param Collection $content
     * @param string $modelName
     * @return array
     */
    private function adaptContent(Collection $content, string $modelName): array
    {
        $transformerList = ConfigBridge::getInstance()->get(ConfigBridge::CONFIG_KEY_TRANSFORMERS);

        // return plain array if no transformer is defined
        if (false === key_exists($modelName, $transformerList)) {
            return $content->toArray();
        }
        $transformer = $transformerList[$modelName];

        $transformerClass = new $transformer;

        $modelData = $content->all();
        foreach ($modelData as $key => $model) {
            $modelData[$key] = $transformerClass->transform($model);
        }
        return $modelData;
    }

    /**
     * Adapt content for resource listing
     * @param array $list
     * @return array
     */
    private function adaptList(array $list)
    {
        $baseUrl = URL::to('/');
        foreach ($list as $key => $resourceName) {
            $list[$key] = [
                'resource_name' => $resourceName,
                'url' => sprintf("%s/exposure/%s", $baseUrl, $resourceName)
            ];
        }
        return $list;
    }
}

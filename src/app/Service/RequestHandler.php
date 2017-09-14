<?php
namespace AdrianTilita\ResourceExposer\Service;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;

class RequestHandler
{
    /**
     * @const string
     */
    const EXPOSE_CONFIG_KEY = 'expose';
    const FILTER_TYPE_DATE = 'newer_than';

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
     * @param Request $request
     * @return array
     */
    public function handleList(Request $request)
    {
        try {
            $response = array_keys(
                $this->modelListService->fetchAll()
            );
            $response = $this->adaptList(
                $response,
                $this->getBaseUrl($request)
            );
        } catch (\Throwable $e) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['error' => $e->getMessage()];
        }

        return [
            $response,
            !isset($statusCode) ? Response::HTTP_OK : $statusCode
        ];
    }

    /**
     * Handle filters
     *
     * @param string $resourceName
     * @param string $filterKey
     * @param string $filterValue
     * @param int $pageNr
     * @param int $perPage
     * @return array
     */
    public function handleFilter(
        string $resourceName,
        string $filterKey,
        string $filterValue,
        int $pageNr,
        int $perPage
    ) {
        try {
            $resources = $this->modelListService->fetchAll();
            // invalid resource name
            if (!isset($resources[$resourceName])) {
                return [
                    ['error' => sprintf('Resource %s does not exists!', $resourceName)],
                    Response::HTTP_BAD_REQUEST
                ];
            }

            /** @var Model $model */
            $model = $resources[$resourceName];
            switch ($filterKey) {
                case static::FILTER_TYPE_DATE:
                    $query = $model::where('created_at', '>=', Carbon::createFromTimestamp($filterValue))
                        ->orWhere('updated_at', '>=', Carbon::createFromTimestamp($filterValue));
                    break;
                default:
                    $query = $model::where($filterKey, $filterValue);
            }
            $totalItems = $query->count();

            $content = $query->limit($perPage)
                ->skip(($pageNr * $perPage) - $perPage)
                ->get();

            $content = $this->adaptContent($content, $resources[$resourceName]);

            $response = [
                'content' => $content,
                'total' => $totalItems,
                'current_page' => $pageNr,
                'per_page' => $perPage,
                'page_count' => ceil($totalItems / $perPage)
            ];
        } catch (\Throwable $e) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $response = ['error' => $e->getMessage()];
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
        $keyExists = Config::has(static::EXPOSE_CONFIG_KEY);
        $transformIsDefined = $keyExists && key_exists($modelName, Config::get(static::EXPOSE_CONFIG_KEY));

        // return plain array if no transformer is defined
        if (!$keyExists || !$transformIsDefined) {
            return $content->toArray();
        }

        $transformerList = Config::get(static::EXPOSE_CONFIG_KEY);
        $transformer = $transformerList[$modelName];

        $transformerClass = new $transformer;
        $modelData = $content->all();
        foreach ($modelData as $key => $model) {
            $modelData[$key] = $transformerClass->transform($model);
        }
        return $modelData;
    }

    /**
     * @param array $list
     * @param string $baseUrl
     * @return array
     */
    private function adaptList(array $list, string $baseUrl)
    {
        foreach ($list as $key => $resourceName) {
            $list[$key] = [
                'resource_name' => $resourceName,
                'available_routes' => [
                    Request::METHOD_GET => [
                        sprintf("%s/exposure/filter/%s/id/[int]", $baseUrl, $resourceName),
                        sprintf("%s/exposure/filter/%s/[field_name]/[field_value]", $baseUrl, $resourceName),
                        sprintf("%s/exposure/filter/%s/id/[int]", $baseUrl, $resourceName)
                    ]
                ]
            ];
        }
        return $list;
    }

    /**
     * Return the base-url from the Request
     * @param Request $request
     * @return string
     */
    private function getBaseUrl(Request $request): string
    {
        return $request->getSchemeAndHttpHost();
    }
}

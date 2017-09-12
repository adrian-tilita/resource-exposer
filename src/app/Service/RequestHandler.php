<?php
namespace AdrianTilita\ResourceExposer\Service;

use AdrianTilita\ResourceExposer\Console\GenerateCommand;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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
     * @var null|GenerateCommand
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
     * @return array
     */
    public function handleList()
    {
        try {
            $response = array_keys(
                $this->modelListService->fetchAll()
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
     * @param int $page_nr
     * @param int $per_page
     * @return array
     */
    public function handleFilter(string $resourceName, string $filterKey, string $filterValue, int $page_nr, int $per_page)
    {
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
                    $query = $model::where(
                        'created_at', '>=', Carbon::createFromTimestamp($filterValue)
                    )->orWhere(
                        'updated_at', '>=', Carbon::createFromTimestamp($filterValue)
                    );
                    break;
                default:
                    $query = $model::where($filterKey, $filterValue);
            }
            $totalItems = $query->count();

            $content = $query->limit($per_page)
                ->skip(($page_nr * $per_page) - $per_page)
                ->get();

            $content = $this->adaptContent($content);

            $response = [
                'content' => $content,
                'total' => $totalItems,
                'current_page' => $page_nr,
                'per_page' => $per_page,
                'page_count' => ceil($totalItems / $per_page)
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

    private function adaptContent(Collection $content): Collection
    {
        if (false === Config::has(static::EXPOSE_CONFIG_KEY)) {
            return $content;
        }
        return $content;
    }
}

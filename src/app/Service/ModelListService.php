<?php
namespace AdrianTilita\ResourceExposer\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ModelListService
{
    /**
     * @const string    Storage key for cache
     */
    const STORE_KEY = 'resource_exposer';

    /**
     * @var ClassSearchService|null
     */
    private $classSearchService = null;

    /**
     * ModelListService constructor.
     * @param ClassSearchService $classSearchService
     */
    public function __construct(ClassSearchService $classSearchService)
    {
        $this->classSearchService = $classSearchService;
    }

    /**
     * Search and cache all models in app
     */
    public function search()
    {
        $definedModels = $this->classSearchService->findClasses();
        Cache::forever(static::STORE_KEY, $this->formatClassReferences($definedModels));
    }

    /**
     * @return array
     */
    public function fetchAll(): array
    {
        $list = Cache::get(static::STORE_KEY);
        if (is_null($list)) {
            $this->search();
            $list = Cache::get(static::STORE_KEY);
        }
        return $list;
    }

    public function getModel(string $modelAliasName)#: Model
    {

    }

    /**
     * Format stored models in [resource_name => resource_class] format
     * @param array $definedModels
     * @return array
     */
    private function formatClassReferences(array $definedModels): array
    {
        foreach ($definedModels as $key => $modelName) {
            $split = explode("\\", $modelName);
            $resourceName = strtolower(end($split));
            if (isset($definedModels[$resourceName])) {
                $resourceName = $resourceName . '_' . $this->generateRandomString();
            }
            $definedModels[$resourceName] = $modelName;
            unset($definedModels[$key]);
        }
        return $definedModels;
    }

    /**
     * @return string
     */
    private function generateRandomString(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

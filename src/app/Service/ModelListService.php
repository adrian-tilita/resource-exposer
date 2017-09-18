<?php
namespace AdrianTilita\ResourceExposer\Service;

use AdrianTilita\ResourceExposer\Base\CacheInterface;
use AdrianTilita\ResourceExposer\Bridge\CacheBridge;
use NeedleProject\Common\ClassFinder;

class ModelListService
{
    /**
     * @const string    Storage key for cache
     */
    const STORE_KEY = 'resource_exposer';

    /**
     * @var ClassFinder|null
     */
    private $classFinder = null;

    /**
     * @var CacheInterface|CacheBridge|null
     */
    private $cacheManager = null;

    /**
     * ModelListService constructor.
     * @param ClassFinder           $classFinder
     * @param null|CacheInterface   $cache
     */
    public function __construct(ClassFinder $classFinder, CacheInterface $cache = null)
    {
        $this->classFinder = $classFinder;
        if (is_null($cache)) {
            $cache = new CacheBridge();
        }
        $this->cacheManager = $cache;
    }

    /**
     * Search and cache all models in app
     */
    public function search()
    {
        $definedModels = $this->classFinder->findClasses();
        $this->cacheManager->store(
            static::STORE_KEY,
            $this->formatClassReferences($definedModels)
        );
    }

    /**
     * @return array
     */
    public function fetchAll(): array
    {
        if (false === $this->cacheManager->has(static::STORE_KEY)) {
            $this->search();
        }
        return $this->cacheManager->get(static::STORE_KEY);
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

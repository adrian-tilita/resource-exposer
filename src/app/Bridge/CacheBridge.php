<?php
namespace AdrianTilita\ResourceExposer\Bridge;

use AdrianTilita\ResourceExposer\Base\CacheInterface;
use Illuminate\Support\Facades\Cache;

/**
 * Class CacheBridge
 * @package AdrianTilita\ResourceExposer\Bridge
 */
class CacheBridge implements CacheInterface
{
    /**
     * Store data in cache
     * @param string $key
     * @param array $data
     */
    public function store(string $key, array $data)
    {
        Cache::forever($key, $data);
    }

    /**
     * Get the content from cache
     * @param string $key
     * @return array
     */
    public function get(string $key)
    {
        try {
            return Cache::get($key);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Verify if a key exists in cache
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        return !is_null(Cache::get($key));
    }
}

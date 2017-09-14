<?php
namespace AdrianTilita\ResourceExposer\Base;

/**
 * Interface CacheInterface
 * @package AdrianTilita\ResourceExposer\Base
 */
interface CacheInterface
{
    /**
     * Store data in cache
     * @param string $key
     * @param array $data
     * @return void
     */
    public function store(string $key, array $data);

    /**
     * Get the content from cache
     * @param string $key
     * @return array
     */
    public function get(string $key);

    /**
     * Verify if a key exists in cache
     * @param string $key
     * @return bool
     */
    public function has(string $key);
}

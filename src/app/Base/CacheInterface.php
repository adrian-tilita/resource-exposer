<?php
/**
 * Created by PhpStorm.
 * User: adrian-tilita
 * Date: 9/12/17
 * Time: 3:10 PM
 */

namespace AdrianTilita\ResourceExposer\Base;


interface CacheInterface
{
    /**
     * Store data in cache
     * @param string $key
     * @param array $data
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

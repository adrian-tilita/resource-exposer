<?php
namespace AdrianTilita\ResourceExposer\Adapter;

/**
 * Interface AdapterInterface
 * @package AdrianTilita\ResourceExposer\Adapter
 */
interface AdapterInterface
{
    /**
     * @param array $data
     * @return array
     */
    public static function adapt(array $data): array;
}

<?php
namespace AdrianTilita\ResourceExposer\Adapter;

/**
 * Class ListResponseAdapter
 * @package AdrianTilita\ResourceExposer\Adapter
 */
class ListResponseAdapter implements AdapterInterface
{
    /**
     * @param array $data
     * @return array
     */
    public static function adapt(array $data): array
    {
        $adaptedData = [];
        foreach ($data as $resourceAliasName => $modelClassName) {
            $adaptedData[] = [
                'url'           => '',
                'resource_name' => $resourceAliasName
            ];
        }
        return $adaptedData;
    }
}

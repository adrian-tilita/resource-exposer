<?php
namespace AdrianTilita\ResourceExposer\Adapter;

class ListResponseAdapter implements AdapterInterface
{
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
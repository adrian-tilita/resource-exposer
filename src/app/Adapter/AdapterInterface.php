<?php
namespace AdrianTilita\ResourceExposer\Adapter;

interface AdapterInterface
{
    public static function adapt(array $data): array;
}
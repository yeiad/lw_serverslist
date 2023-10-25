<?php

namespace App\Service\Servers;

use App\Entity\Server;
use App\Helper\RamHelper;
use App\Helper\SizeHelper;
use App\Helper\StorageHelper;
use App\Repository\ServerRepository;
use App\ServerFilter\Filter;
use App\ServerFilter\IdsFilter;
use App\ServerFilter\LocationFilter;
use App\ServerFilter\MinimumStorageFilter;
use App\ServerFilter\RamFilter;
use App\ServerFilter\TypeFilter;

class ServersService
{
    public function __construct(private readonly ServerRepository $serverRepository)
    {
    }

    public function filter(
        array   $servers = null,
        ?string $location = null,
        ?string $storage = null,
        ?array  $ram = null,
        ?string $minimumStorage = null,
        ?array  $ids = null,
    ): array {

        $requestSize      = null;
        $requestUnitIndex = null;

        if ($minimumStorage) {
            [
                'request_unit_index' => $requestUnitIndex,
                'request_size'       => $requestSize,
            ] = $this->getMinimumStorageData($minimumStorage);
        }


        return array_filter(
            $servers ?? $this->getServerEntitiesFromDataArray(
            $this->serverRepository->getServersList()
        ),
            function ($server) use ($location, $storage, $ram, $minimumStorage, $requestSize, $requestUnitIndex, $ids) {

                foreach (
                    $this->getFilterConditions(
                        $server,
                        $location,
                        $storage,
                        $ram,
                        $minimumStorage,
                        $requestSize,
                        $requestUnitIndex,
                        $ids
                    ) as $filter
                ) {
                    if ($filter->isFilterConditionMet()) {
                        if (!$filter->isLazyLoadedTestPassing()) {
                            return false;
                        }
                    }
                }

                return true;
            }
        );
    }

    private function getMinimumStorageData(string $minimumStorage): array
    {
        preg_match(
            '/^(?<size>\d+)(?<unit>[KMGTP]?B)/',
            StorageHelper::STORAGE_OPTIONS[$minimumStorage],
            $request
        );
        ['size' => $requestSize, 'unit' => $requestUnits] = $request;
        $requestUnitIndex = array_search($requestUnits, SizeHelper::UNITS);
        return [
            'request_unit_index' => $requestUnitIndex,
            'request_size'       => $requestSize,
        ];
    }


    /**
     * @return Filter[]
     */
    private function getFilterConditions(
        Server  $server,
        ?string $location,
        ?string $storage,
        ?array  $ram,
        ?string $minimumStorage,
        ?int    $requestSize,
        ?int    $requestUnitIndex,
        ?array  $ids
    ): array {
        return [
            new IdsFilter($ids, $server->id),
            new TypeFilter($storage, $server->storageType),
            new MinimumStorageFilter(
                $minimumStorage,
                $server->storageSizeUnits,
                $requestUnitIndex,
                $server->storageSize,
                $server->storageCount,
                $requestSize,
                SizeHelper::UNITS
            ),
            new LocationFilter($server->location, $location),
            new RamFilter(RamHelper::RAM_OPTIONS, $server->ram, $ram)
        ];
    }

    private function getServerEntitiesFromDataArray(array $getServersList): array
    {
        $servers = [];
        foreach ($getServersList as $serversData) {
            $servers[] = $this->getServerFromDataArray($serversData);
        }
        return $servers;
    }

    private function getServerFromDataArray(array $serversData): Server
    {
        $server = new Server();

        foreach ($serversData as $key => $value) {
            $tokens = explode('_', $key);
            $length = count($tokens);
            for ($i = 1; $i < $length; $i++) {
                $tokens[$i] = ucfirst($tokens[$i]);
            }
            $property = implode('', $tokens);

            $server->$property = $value;
        }

        return $server;

    }
}
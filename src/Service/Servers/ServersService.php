<?php

namespace App\Service\Servers;

use App\Helper\RamHelper;
use App\Helper\StorageHelper;
use App\Repository\ServerRepository;

class ServersService
{

    private array $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

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
            $servers ?? $this->serverRepository->getServersList(),
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
                    ) as ['filter_execution_condition' => $isFilterConditionMet, 'filter_test' =>
                    $isLazyLoadedTestPassing]
                ) {
                    if ($isFilterConditionMet) {
                        if (!$isLazyLoadedTestPassing()) {
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
        $requestUnitIndex = array_search($requestUnits, $this->units);
        return [
            'request_unit_index' => $requestUnitIndex,
            'request_size'       => $requestSize,
        ];
    }

    private function isCorrectType(string $storage, mixed $type): bool
    {
        return $storage === $type ||
               str_starts_with($type, $storage);
    }

    private function isLocationMatching(mixed $serverLocation, string $location): bool
    {
        return str_contains(strtoupper($serverLocation), mb_strtoupper($location));
    }

    private function isAdequateStorage(
        mixed $unit,
        ?int  $requestUnitIndex,
        mixed $size,
        mixed $multiplier,
        ?int  $requestSize
    ): bool {
        $unitIndex = array_search($unit, $this->units);

        return $unitIndex > $requestUnitIndex || (
                $unitIndex === $requestUnitIndex && ($size * $multiplier) >= $requestSize
            );
    }

    private function isRamMatch(mixed $serverRam, array $blackList): bool
    {
        $blackList = array_diff(RamHelper::RAM_OPTIONS, $blackList);
        foreach ($blackList as $excludedRam) {
            if (str_starts_with($serverRam, $excludedRam)) {
                return false;
            }
        }

        return true;

    }

    private function getFilterConditions(
        array   $server,
        ?string $location,
        ?string $storage,
        ?array  $ram,
        ?string $minimumStorage,
        ?int    $requestSize,
        ?int    $requestUnitIndex,
        ?array  $ids
    ): array {

        [
            'location'           => $serverLocation,
            'storage_size'       => $size,
            'storage_size_units' => $unit,
            'storage_count'      => $multiplier,
            'storage_type'       => $type,
            'ram'                => $serverRam,
            'id'                 => $serverId,
        ] = $server;


        return [

            [
                'filter_execution_condition' => null !== $ids,
                'filter_test'                => function () use ($ids, $serverId) {
                    return $this->isIdMatch($ids, $serverId);
                }
            ],
            [
                'filter_execution_condition' => $storage,
                'filter_test'                => function () use ($storage, $type) {
                    return $this->isCorrectType($storage, $type);
                }
            ],
            [
                'filter_execution_condition' => $minimumStorage,
                'filter_test'                => function () use (
                    $unit,
                    $requestUnitIndex,
                    $size,
                    $multiplier,
                    $requestSize
                ) {
                    return $this->isAdequateStorage(
                        $unit,
                        $requestUnitIndex,
                        $size,
                        $multiplier,
                        $requestSize
                    );
                }
            ],
            [
                'filter_execution_condition' => $location,
                'filter_test'                => function () use ($serverLocation, $location) {
                    return $this->isLocationMatching($serverLocation, $location);
                }
            ],
            [
                'filter_execution_condition' => null !== $ram && array_diff(RamHelper::RAM_OPTIONS, $ram),
                'filter_test'                => function () use ($serverRam, $ram) {
                    return $this->isRamMatch($serverRam, $ram);
                }
            ],

        ];
    }

    private function isIdMatch(array $ids, int $serverId): bool
    {
        return in_array($serverId, $ids);
    }
}
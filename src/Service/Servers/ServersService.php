<?php

namespace App\Service\Servers;

use App\Helper\StorageHelper;
use App\Repository\ServerRepository;

class ServersService
{

    private $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

    public function __construct(private ServerRepository $serverRepository)
    {
    }

    public function filter(
        ?string $location = null,
        ?string $storage = null,
        ?array  $ram = null,
        ?string $minimumStorage = null
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
            $this->serverRepository->getServersList(),
            function ($server) use ($location, $storage, $ram, $minimumStorage, $requestSize, $requestUnitIndex) {

                foreach (
                    $this->getFilterConditions(
                        $server,
                        $location,
                        $storage,
                        $ram,
                        $minimumStorage,
                        $requestSize,
                        $requestUnitIndex
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

    private function isRamMatch(mixed $serverRam, array $ram): bool
    {
        preg_match('/^(?<ram_size>\d+GB)/', $serverRam, $ramSize);
        ['ram_size' => $ramSize] = $ramSize;
        return in_array($ramSize, $ram);

    }

    private function getFilterConditions(
        array  $server,
        ?string $location,
        ?string $storage,
        ?array  $ram,
        ?string $minimumStorage,
        ?int   $requestSize,
        ?int   $requestUnitIndex
    ): array {

        [
            'location'           => $serverLocation,
            'storage_size'       => $size,
            'storage_size_units' => $unit,
            'storage_count'      => $multiplier,
            'storage_type'       => $type,
            'ram'                => $serverRam
        ] = $server;


        return [
            [
                'filter_execution_condition' => $storage,
                'filter_test'                => function () use ($storage, $type) {
                    return $this->isCorrectType($storage, $type);
                }
            ],
            [
                'filter_execution_condition' => null !== $location,
                'filter_test'                => function () use ($serverLocation, $location) {
                    return $this->isLocationMatching($serverLocation, $location);
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
                'filter_execution_condition' => null !== $ram,
                'filter_test'                => function () use ($serverRam, $ram) {
                    return $this->isRamMatch($serverRam, $ram);
                }
            ],

        ];
    }
}
<?php

namespace App\Service\Servers;

use App\Helper\StorageHelper;
use App\Repository\ServerRepository;

class ServersService
{
    public function __construct(private ServerRepository $serverRepository)
    {
    }

    public function filter(
        ?string $location = null,
        ?string $storage = null,
        ?array  $ram = null,
        ?string $minimumStorage = null
    ): array {


        return array_filter(
            $this->serverRepository->getServersList(),
            static function ($server) use ($location, $minimumStorage, $storage, $ram) {

                [
                    'location'           => $serverLocation,
                    'storage_size'       => $size,
                    'storage_size_units' => $unit,
                    'storage_count'      => $multiplier,
                    'storage_type'       => $type,
                    'ram'                => $serverRam
                ] = $server;
                $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];


                if (null !== $location) {
                    $isLocation = str_contains(strtoupper($serverLocation), mb_strtoupper($location));

                    if (!$isLocation) {
                        return false;
                    }
                }

                if ($minimumStorage) {
                    $lol = 1;

                    preg_match(
                        '/^(?<size>\d+)(?<unit>[KMGTP]?B)/',
                        StorageHelper::STORAGE_OPTIONS[$minimumStorage],
                        $request
                    );
                    ['size' => $requestSize, 'unit' => $requestUnits] = $request;

                    $unitIndex        = array_search($unit, $units);
                    $requestUnitIndex = array_search($requestUnits, $units);

                    $isAdequateStorage = $unitIndex > $requestUnitIndex || (
                            $unitIndex === $requestUnitIndex && ($size * $multiplier) >= $requestSize
                        );

                    if (!$isAdequateStorage) {
                        return false;
                    }
                }

                if ($storage) {
                    $lol = 1;

                    $isCorrectType = $storage === $type ||
                                     str_starts_with($type, $storage);

                    if (!$isCorrectType) {
                        return false;
                    }

                }

                if (null !== $ram) {

                    preg_match('/^(?<ram_size>\d+GB)/', $serverRam, $ramSize);
                    ['ram_size' => $ramSize] = $ramSize;
                    $isRamMatch = in_array($ramSize, $ram);

                    if (!$isRamMatch) {
                        return false;
                    }

                }

                return true;
            }
        );
    }
}
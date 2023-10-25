<?php

namespace App\Twig\Components;

use App\Helper\LocationHelper;
use App\Helper\RamHelper;
use App\Helper\StorageHelper;
use App\Service\Servers\ServersService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class ServerSearch
{
    use DefaultActionTrait;


    #[LiveProp(writable: true)]
    public array $ids = [];
    #[LiveProp(writable: true)]
    public string $totalStorageSize = '0';
    #[LiveProp(writable: true)]
    public array $ram = RamHelper::RAM_OPTIONS;
    #[LiveProp(writable: true)]
    public string $storage = '';
    #[LiveProp(writable: true)]
    public string $location = '';

    #[LiveProp()]
    public array $ramOptions = RamHelper::RAM_OPTIONS;
    #[LiveProp()]
    public array $storageTypeOptions = StorageHelper::STORAGE_TYPE_OPTIONS;
    #[LiveProp()]
    public array $locationOptions = LocationHelper::LOCATION_OPTIONS;
    #[LiveProp()]
    public array $storageOptions = StorageHelper::STORAGE_OPTIONS;
    private array $serversList;
    private array $comparables;

    public function __construct(private ServersService $serversService)
    {

    }

    public function getServers(): array
    {
        return $this->serversList ?? $this->serversList = $this->serversService->filter(null, $this->location, $this->storage, $this->ram, $this->totalStorageSize);

    }

    public function getComparables(): array
    {
        return $this->comparables ?? $this->comparables = $this->serversService->filter(
            $this->serversList ?? $this->getServers(),
            null,
            null,
            null,
            null ,
            $this->ids
        );

    }

}
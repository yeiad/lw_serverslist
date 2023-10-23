<?php

namespace App\Twig\Components;

use App\Service\Servers\ServersService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class ServerSearch
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(private ServersService $serversService)
    {
    }

    public function getServers(): array
    {
        return $this->serversService->searchByLocation($this->query);
    }

}
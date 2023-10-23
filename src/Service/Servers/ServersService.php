<?php

namespace App\Service\Servers;

use App\Repository\ServerRepository;

class ServersService
{
    public function __construct(private ServerRepository $serverRepository)
    {
    }

    public function searchByLocation(?string $query = null): array
    {
        if (null === $query){

            return $this->serverRepository->getServersList();
        }

        return array_filter($this->serverRepository->getServersList(),static function ($server) use ($query) {
            return str_contains(strtoupper($server['location']), mb_strtoupper($query));
        });

    }
}
<?php

namespace App\Entity;

class Server
{
    public ?int $id = null;
    public ?string $model = null;
    public ?string $ram = null;
    public ?int $ramSize = null;
    public ?string $ramUnit = null;
    public ?string $ramType = null;
    public ?string $storage = null;
    public ?int $storageCount = null;
    public ?int $storageSize = null;
    public ?string $storageSizeUnits = null;
    public ?string $storageType = null;
    public ?string $location = null;
    public ?string $currency = null;
    public ?float $price = null;

}
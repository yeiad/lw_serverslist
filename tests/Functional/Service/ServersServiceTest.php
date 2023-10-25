<?php

namespace App\Tests\Functional\Service;

use App\Helper\StorageHelper;
use App\Service\Servers\ServersService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ServersServiceTest extends KernelTestCase
{
    public function testLocationFilter(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $serversService = $container->get(ServersService::class);
        $results        = $serversService->filter(null, 'Frankfurt');

        $this->assertFalse(array_search('Washington D.C.WDC-01', array_column($results, 'location')));
        $this->assertTrue(0 === array_search('FrankfurtFRA-10', array_column($results, 'location')));

    }

    public function testMinimumStorageFilter(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $serversService = $container->get(ServersService::class);
        $results        = $serversService->filter(
            null,
            null,
            null,
            null,
            array_search('24TB', StorageHelper::STORAGE_OPTIONS)
        );

        $this->assertFalse(array_search('2x1TBSATA2', array_column($results, 'storage')));
        $this->assertTrue(is_int(array_search('24x1TBSATA2', array_column($results, 'storage'))));

    }

    public function testStorageFilter(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $serversService = $container->get(ServersService::class);
        $results        = $serversService->filter(
            null,
            null,
            'SAS'
        );

        $this->assertFalse(array_search('2x1TBSATA2', array_column($results, 'storage')));
        $this->assertTrue(is_int(array_search('8x300GBSAS', array_column($results, 'storage'))));

    }
}
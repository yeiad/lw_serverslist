<?php

namespace App\Tests\ServerFilter;

use App\ServerFilter\MinimumStorageFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MinimumStorageFilterTest extends KernelTestCase
{

    /** @dataProvider isisFilterConditionMetMetDataProvider */
    public function testIsFilterConditionMetMet($input, $expected)
    {
        $filter = new MinimumStorageFilter(
            $input['minimumStorage'],
            $input['server_size_unit'],
            $input['requestUnitIndex'],
            $input['server_size_per_storage_device'],
            $input['server_number_of_storage_devices'],
            $input['minimum_total_size_number'],
            $input['units'],
        );
        $this->assertTrue($expected['isFilterConditionMet'] === $filter->isFilterConditionMet());
        $this->assertTrue($expected['isLazyLoadedTestPassing'] === $filter->isLazyLoadedTestPassing());
    }

    public function isisFilterConditionMetMetDataProvider(): array
    {
        return [
            [
                'input'    => [
                    'units'                            => ['GB', 'TB'],
                    'minimumStorage'                   => '12TB',
                    'requestUnitIndex'                 => 1,
                    'minimum_total_size_number'        => 12,
                    'server_size_unit'                 => 'TB',
                    'server_size_per_storage_device'   => 2,
                    'server_number_of_storage_devices' => 1,
                ],
                'expected' => [
                    'isFilterConditionMet'    => true,
                    'isLazyLoadedTestPassing' => false
                ]
            ],
            [
                'input'    => [
                    'units'                            => ['GB', 'TB'],
                    'minimumStorage'                   => '1TB',
                    'requestUnitIndex'                 => 1,
                    'minimum_total_size_number'        => 1,
                    'server_size_unit'                 => 'TB',
                    'server_size_per_storage_device'   => 2,
                    'server_number_of_storage_devices' => 1,
                ],
                'expected' => [
                    'isFilterConditionMet'    => true,
                    'isLazyLoadedTestPassing' => true
                ]
            ],
            [
                'input'    => [
                    'units'                            => ['GB', 'TB'],
                    'minimumStorage'                   => '1GB',
                    'requestUnitIndex'                 => 0,
                    'minimum_total_size_number'        => 1,
                    'server_size_unit'                 => 'TB',
                    'server_size_per_storage_device'   => 2,
                    'server_number_of_storage_devices' => 1,
                ],
                'expected' => [
                    'isFilterConditionMet'    => true,
                    'isLazyLoadedTestPassing' => true
                ]
            ],
            [
                'input'    => [
                    'units'                            => ['GB', 'TB'],
                    'minimumStorage'                   => '2TB',
                    'requestUnitIndex'                 => 1,
                    'minimum_total_size_number'        => 2,
                    'server_size_unit'                 => 'GB',
                    'server_size_per_storage_device'   => 250,
                    'server_number_of_storage_devices' => 4,
                ],
                'expected' => [
                    'isFilterConditionMet'    => true,
                    'isLazyLoadedTestPassing' => false
                ]
            ],
            [
                'input'    => [
                    'units'                            => ['GB', 'TB'],
                    'minimumStorage'                   => '1TB',
                    'requestUnitIndex'                 => 1,
                    'minimum_total_size_number'        => 1,
                    'server_size_unit'                 => 'GB',
                    'server_size_per_storage_device'   => 250,
                    'server_number_of_storage_devices' => 4,
                ],
                'expected' => [
                    'isFilterConditionMet'    => true,
                    'isLazyLoadedTestPassing' => true
                ]
            ],
            [
                'input'    => [
                    'units'                            => ['GB', 'TB'],
                    'minimumStorage'                   => '1TB',
                    'requestUnitIndex'                 => 1,
                    'minimum_total_size_number'        => 1,
                    'server_size_unit'                 => 'GB',
                    'server_size_per_storage_device'   => 250,
                    'server_number_of_storage_devices' => 3,
                ],
                'expected' => [
                    'isFilterConditionMet'    => true,
                    'isLazyLoadedTestPassing' => false
                ]
            ],
        ];
    }
}

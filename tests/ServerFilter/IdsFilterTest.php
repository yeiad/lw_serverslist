<?php

namespace App\Tests\ServerFilter;

use App\ServerFilter\IdsFilter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IdsFilterTest extends KernelTestCase
{
    /** @dataProvider isFilterConditionMetMetDataProvider */
    public function testIsFilterConditionMetMet($input, $expected)
    {
        $idFilter = new IdsFilter($input['ids'], $input['id']);
        $this->assertTrue($expected['isFilterConditionMet'] === $idFilter->isFilterConditionMet());
        $this->assertTrue($expected['isLazyLoadedTestPassing'] === $idFilter->isLazyLoadedTestPassing());
    }

    public function isFilterConditionMetMetDataProvider(): array
    {
        return [
            [
                'input'    => [
                    'ids' => [11, 2, 3, 4],
                    'id'  => 1,
                ],
                'expected' => [
                    'isFilterConditionMet'    => true,
                    'isLazyLoadedTestPassing' => false
                ]
            ],
            [
                'input'    => [
                    'ids' => null,
                    'id'  => 1,
                ],
                'expected' => [
                    'isFilterConditionMet'    => false,
                    'isLazyLoadedTestPassing' => false
                ]

            ],
        ];
    }
}
<?php

namespace App\ServerFilter;

class MinimumStorageFilter implements Filter
{


    public function __construct(
        private readonly ?string $minimumStorage,
        private readonly mixed   $unit,
        private readonly ?int    $requestUnitIndex,
        private readonly mixed   $size,
        private readonly mixed   $multiplier,
        private readonly ?int    $requestSize,
        private readonly ?array  $units,
    ) {
    }

    public function isFilterConditionMet(): bool
    {
        return (bool) $this->minimumStorage;
    }

    public function isLazyLoadedTestPassing(): bool
    {
        $unitIndex = array_search($this->unit, $this->units);

        return $this->isFilterConditionMet() &&
               (
                   pow(10, $this->requestUnitIndex * 3 ) * $this->requestSize <=
                   pow(10, $unitIndex * 3) * $this->size * $this->multiplier
               );

    }
}
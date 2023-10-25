<?php

namespace App\ServerFilter;

class LocationFilter implements Filter
{


    private bool $conditionIsMet;

    public function __construct(
        private readonly mixed   $serverLocation,
        private readonly ?string $location
    ) {
    }

    public function isFilterConditionMet(): bool
    {
        return $this->conditionIsMet ?? $this->conditionIsMet = (bool) $this->location;
    }

    public function isLazyLoadedTestPassing(): bool
    {
        return $this->isFilterConditionMet() && str_contains(strtoupper($this->serverLocation), mb_strtoupper($this->location));
    }
}
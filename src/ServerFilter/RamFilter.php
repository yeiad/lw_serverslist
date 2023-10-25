<?php

namespace App\ServerFilter;

class RamFilter implements Filter
{
    private bool $conditionIsMet;

    public function __construct(
        private readonly array  $ramOptions,
        private readonly mixed  $serverRam,
        private readonly ?array $ram
    ) {
    }

    public function isFilterConditionMet(): bool
    {
        return $this->conditionIsMet
               ??
               $this->conditionIsMet = null !== $this->ram && array_diff($this->ramOptions, $this->ram);
    }

    public function isLazyLoadedTestPassing(): bool
    {
        if (!$this->isFilterConditionMet()) {
            return false;
        }

        $blackList = array_diff($this->ramOptions, $this->ram);

        foreach ($blackList as $excludedRam) {
            if (str_starts_with($this->serverRam, $excludedRam)) {
                return false;
            }
        }

        return true;
    }
}
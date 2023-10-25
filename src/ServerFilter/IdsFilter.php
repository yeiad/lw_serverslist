<?php

namespace App\ServerFilter;

class IdsFilter implements Filter
{
    private bool $conditionIsMet;

    public function __construct(
        private readonly ?array $ids,
        private readonly ?int $id
    ) {
    }

    public function isFilterConditionMet(): bool
    {
        return $this->conditionIsMet ?? $this->conditionIsMet = null !== $this->ids;
    }

    public function isLazyLoadedTestPassing(): bool
    {
        return $this->isFilterConditionMet() && in_array($this->id, $this->ids);
    }
}
<?php

namespace App\ServerFilter;

class TypeFilter implements Filter
{
    public function __construct(
        private readonly ?string $storage,
        private readonly ?string $type
    ) {
    }

    public function isFilterConditionMet(): bool
    {
        return (bool)$this->storage;
    }

    public function isLazyLoadedTestPassing(): bool
    {
        return $this->isFilterConditionMet() &&
               ($this->storage === $this->type ||
                str_starts_with($this->type, $this->storage));
    }
}
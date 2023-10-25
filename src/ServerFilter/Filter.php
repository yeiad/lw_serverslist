<?php

namespace App\ServerFilter;

interface Filter
{
    public function isFilterConditionMet(): bool;

    public function isLazyLoadedTestPassing(): bool;
}
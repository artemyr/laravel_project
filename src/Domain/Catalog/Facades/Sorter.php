<?php

namespace Domain\Catalog\Facades;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Domain\Catalog\Sorters\Sorter
 *
 * @method static Builder run(Builder $query)
 */
class Sorter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Domain\Catalog\Sorters\Sorter::class;
    }
}

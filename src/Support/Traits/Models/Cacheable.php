<?php

namespace Support\Traits\Models;

use App\Observers\ModelCacheObserver;

trait Cacheable
{
    protected static function bootCacheable()
    {
        $model = static::class;
        $model::observe(ModelCacheObserver::class);
    }

    public function getCacheKeys(): array
    {
        return [];
    }
}

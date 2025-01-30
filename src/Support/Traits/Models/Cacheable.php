<?php

namespace Support\Traits\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    protected static function bootCacheable()
    {
        static::created(function (Model $item) {
            $item->clearCache();
        });

        static::updated(function (Model $item) {
            $item->clearCache();
        });

        static::deleted(function (Model $item) {
            $item->clearCache();
        });
    }

    protected function getCacheKeys(): array
    {
        return [];
    }

    protected function clearCache(): void
    {
        foreach ($this->getCacheKeys() as $cacheKey) {
            Cache::forget($cacheKey);
        }
    }
}

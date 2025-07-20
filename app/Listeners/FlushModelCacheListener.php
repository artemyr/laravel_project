<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class FlushModelCacheListener
{
    public function handle(object $event): void
    {
        if (method_exists($event->model,'getCacheKeys')) {
            foreach ($event->model->getCacheKeys() as $cacheKey) {
                Cache::forget($cacheKey);
            }
        }
    }
}

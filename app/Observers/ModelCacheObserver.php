<?php

namespace App\Observers;

use App\Events\ModelCreatedEvent;
use App\Events\ModelDeletedEvent;
use App\Events\ModelUpdatedEvent;
use Illuminate\Database\Eloquent\Model;

class ModelCacheObserver
{
    public function created(Model $item): void
    {
        event(new ModelCreatedEvent($item));
    }

    public function updated(Model $item): void
    {
        event(new ModelUpdatedEvent($item));
    }

    public function deleted(Model $item): void
    {
        event(new ModelDeletedEvent($item));
    }
}

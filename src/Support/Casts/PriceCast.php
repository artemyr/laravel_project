<?php

namespace Support\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Support\ValueObjects\Price;

class PriceCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): Price
    {
        return Price::make($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        if ( ! $value instanceof Price) {
            $value = Price::make($value);
        }
        return $value->row();
    }
}

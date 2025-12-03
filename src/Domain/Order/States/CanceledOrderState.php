<?php

namespace Domain\Order\States;

use Domain\Order\Enums\OrderStatuses;

class CanceledOrderState extends OrderState
{
    protected array $allowedTransitions = [];

    public function canBeChanged(): bool
    {
        return false;
    }

    public function value(): string
    {
        return OrderStatuses::Canceled->value;
    }

    public function humanValue(): string
    {
        return 'Отменен';
    }
}

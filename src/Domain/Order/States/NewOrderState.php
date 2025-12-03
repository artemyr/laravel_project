<?php

namespace Domain\Order\States;

use Domain\Order\Enums\OrderStatuses;

class NewOrderState extends OrderState
{
    protected array $allowedTransitions = [
        PendingOrderState::class,
        CanceledOrderState::class,
    ];

    public function canBeChanged(): bool
    {
        return true;
    }

    public function value(): string
    {
        return OrderStatuses::New->value;
    }

    public function humanValue(): string
    {
        return 'Новый заказ';
    }
}

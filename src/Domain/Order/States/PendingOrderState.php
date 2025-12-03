<?php

namespace Domain\Order\States;

use Domain\Order\Enums\OrderStatuses;

class PendingOrderState extends OrderState
{
    protected array $allowedTransitions = [
        PayedOrderState::class,
        CanceledOrderState::class,
    ];

    public function canBeChanged(): bool
    {
        return true;
    }

    public function value(): string
    {
        return OrderStatuses::Pending->value;
    }

    public function humanValue(): string
    {
        return 'В обработке';
    }
}

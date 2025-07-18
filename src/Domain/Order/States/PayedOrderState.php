<?php

namespace Domain\Order\States;

use Domain\Order\Enums\OrderStatuses;

class PayedOrderState extends OrderState
{
    protected array $allowedTransitions = [
        CanceledOrderState::class
    ];

    public function canBeChanged(): bool
    {
        return true;
    }

    public function value(): string
    {
        return OrderStatuses::Payed->value;
    }

    public function humanValue(): string
    {
        return 'Оплачен ';
    }
}

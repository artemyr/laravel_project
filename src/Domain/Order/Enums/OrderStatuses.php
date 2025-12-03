<?php

namespace Domain\Order\Enums;

use Domain\Order\Models\Order;
use Domain\Order\States\CanceledOrderState;
use Domain\Order\States\NewOrderState;
use Domain\Order\States\OrderState;
use Domain\Order\States\PayedOrderState;
use Domain\Order\States\PendingOrderState;

enum OrderStatuses: string
{
    case New = 'new';
    case Pending = 'pending';
    case Payed = 'payed';
    case Canceled = 'canceled';

    public function createState(Order $order): OrderState
    {
        return match ($this) {
            OrderStatuses::New => new NewOrderState($order),
            OrderStatuses::Pending => new PendingOrderState($order),
            OrderStatuses::Payed => new PayedOrderState($order),
            OrderStatuses::Canceled => new CanceledOrderState($order),
        };
    }
}

<?php

namespace Domain\Order\States\Payment;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

class PaymentState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(PendingPaymentState::class)
            ->allowTransition(PendingPaymentState::class, PayedPaymentState::class)
            ->allowTransition(PendingPaymentState::class, CancelledPaymentState::class);
    }
}

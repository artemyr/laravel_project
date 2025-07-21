<?php

namespace Domain\Order\Payment;

use Illuminate\Support\Collection;
use Support\ValueObjects\Price;

class PaymentData
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly string $returnUrl,
        public readonly Price $amount,
        public readonly Collection $meta,
    )
    {
    }
}

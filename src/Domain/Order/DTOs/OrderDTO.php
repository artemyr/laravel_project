<?php

namespace Domain\Order\DTOs;

use Support\Traits\Makeable;

class OrderDTO
{
    use Makeable;

    public function __construct(
        public readonly string $payment_method_id,
        public readonly string $delivery_type_id,
        public readonly string $password,
    ) {
    }
}

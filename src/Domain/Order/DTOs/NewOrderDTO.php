<?php

namespace Domain\Order\DTOs;

use Illuminate\Http\Request;
use Support\Traits\Makeable;

class NewOrderDTO
{
    use Makeable;

    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly string $password,
        public readonly int $paymentMethodId,
        public readonly int $deliveryTypeId,
        public readonly bool $createAccount,
    ) {
    }

    public static function fromRequest(Request $request)
    {
        $args = $request->only(
            'customer.first_name',
            'customer.last_name',
            'email',
            'password',
        );

        $args[] = $request->integer('payment_method_id');
        $args[] = $request->integer('delivery_type_id');
        $args[] = $request->boolean('create_account');

        return static::make(...$args);
    }
}

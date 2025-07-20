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
        public readonly string $phone,
        public readonly string $city,
        public readonly string $address,
        public readonly int $paymentMethodId,
        public readonly int $deliveryTypeId,
        public readonly bool $createAccount,
    ) {
    }

    public static function fromRequest(Request $request)
    {
        $customer = $request->get('customer');

        $args['firstName'] = $customer['first_name'];
        $args['lastName'] = $customer['last_name'];
        $args['email'] = $customer['email'];
        $args['password'] = $request->get('password') ?? '';
        $args['phone'] = $customer['phone'];
        $args['city'] = $customer['city'];
        $args['address'] = $customer['address'];
        $args['paymentMethodId'] = $request->integer('payment_method_id');
        $args['deliveryTypeId'] = $request->integer('delivery_type_id');
        $args['createAccount'] = $request->boolean('create_account');

        return static::make(...$args);
    }
}

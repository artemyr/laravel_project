<?php

namespace Domain\Order\Processes;

use Domain\Order\Contracts\OrderProcessContract;
use Domain\Order\DTOs\NewOrderDTO;
use Domain\Order\Models\Order;

class AssignCustomer implements OrderProcessContract
{
    public function __construct(protected NewOrderDTO $customer)
    {
    }

    public function handle(Order $order, $next)
    {
        $order->orderCustomer()
            ->create([
                'order_id' => $order->id,
                'first_name' => $this->customer->firstName,
                'last_name' => $this->customer->lastName,
                'email' => $this->customer->email,
                'phone' => $this->customer->phone,
                'address' => $this->customer->address,
                'city' => $this->customer->city,
            ]);

        return $next($order);
    }
}

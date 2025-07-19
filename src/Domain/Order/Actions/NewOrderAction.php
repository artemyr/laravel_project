<?php

namespace Domain\Order\Actions;

use Domain\Auth\Contracts\RegisterNewUserContract;
use Domain\Auth\DTOs\NewUserDTO;
use Domain\Order\DTOs\NewOrderDTO;
use Domain\Order\Models\Order;

class NewOrderAction
{
    public function __invoke(NewOrderDTO $data): Order
    {
        $registerAction = app(RegisterNewUserContract::class);

        if ($data->createAccount) {
            $registerAction(NewUserDTO::make(
                $data->firstName . ' ' . $data->lastName,
                $data->email,
               $data->password
            ));
        }

        return Order::query()
            ->create([
//                'user_id' => auth()->id(),
                'payment_method_id' => $data->paymentMethodId,
                'delivery_type_id' => $data->deliveryTypeId,
            ]);
    }
}

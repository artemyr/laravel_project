<?php

namespace Domain\Order\Contracts;

use Domain\Order\DTOs\NewOrderDTO;

interface NewOrderContract
{
    public function __invoke(NewOrderDTO $data);
}

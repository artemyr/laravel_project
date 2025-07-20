<?php

namespace Domain\Order\Processes;

use Domain\Order\Events\OrderCreated;
use Domain\Order\Models\Order;
use DomainException;
use Illuminate\Pipeline\Pipeline;
use Throwable;
use Support\Transaction;

class OrderProcess
{
    protected array $processes = [];

    public function __construct(
        protected Order $order
    )
    {
    }

    public function processes(array $processes): self
    {
        $this->processes = $processes;
        return $this;
    }

    /**
     * @throws Throwable
     */
    public function run()
    {
        return Transaction::run(function () {
            return app(Pipeline::class)
                ->send($this->order)
                ->through($this->processes)
                ->thenReturn();
        }, function (Order $order) {
            flash()->info("Заказ №{$order->id} создан");
            event(new OrderCreated($order));
        }, function (Throwable $e) {

            $message = app()->isLocal()
                ? $e->getMessage()
                : 'Что-то полшло не так';

            throw new DomainException($message);
        });
    }
}

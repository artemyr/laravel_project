<?php

namespace Services\UnitPay;

/**
 * TODO реализовать сервис
 */

class UnitPayPaymentGateway
{
    public function __construct(array $config)
    {
    }

    public function response(): string
    {
    }

    public function createPayment(
        string $id,
        float|int $value,
        string $description,
        string $currency,
        array $items,
        string $email,
        string $returnUrl,
        string $returnUrl1,
        string $phone
    ): string
    {
    }

    public function handle(float|int $value, string $currency)
    {
    }

    public function isPaySuccess(): bool
    {
    }

    public function errorMessage(): string
    {
    }
}

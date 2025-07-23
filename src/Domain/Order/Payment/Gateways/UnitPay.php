<?php

namespace Domain\Order\Payment\Gateways;

use Domain\Order\Contracts\PaymentGatewayContract;
use Domain\Order\Payment\PaymentData;
use Illuminate\Http\JsonResponse;
use Services\UnitPay\UnitPayPaymentGateway;

class UnitPay implements PaymentGatewayContract
{
    protected UnitPayPaymentGateway $client;

    protected PaymentData $paymentData;

    protected string $errorMessage;

    public function __construct(array $config)
    {
        $this->configure($config);
    }

    public function paymentId(): string
    {
        return $this->paymentData->id;
    }

    public function configure(array $config): void
    {
        $this->client = new UnitPayPaymentGateway(...$config);
    }

    public function data(PaymentData $data): PaymentGatewayContract
    {
        $this->paymentData = $data;

        return $this;
    }

    public function request(): mixed
    {
        return request()->all();
    }

    public function response(): JsonResponse
    {
        return response()->json(
            $this->client->response()
        );
    }

    public function url(): string
    {
        return $this->client->createPayment(
            $this->paymentData->id,
            $this->paymentData->amount->value(),
            $this->paymentData->description,
            $this->paymentData->amount->currency(),
            [
                $this->client->cashItem(
                    $this->paymentData->description,
                    1,
                    $this->paymentData->amount->value()
                )
            ],
            $this->paymentData->meta->get('email', ''),
            $this->paymentData->returnUrl,
            $this->paymentData->returnUrl,
            $this->paymentData->meta->get('phone', '')
        );
    }

    public function validate(): bool
    {
        $this->client->handle(
            $this->paymentData->amount->value(),
            $this->paymentData->amount->currency(),
        )->isSuccess();
    }

    public function paid(): bool
    {
        return $this->client->isPaySuccess();
    }

    public function errorMessage(): string
    {
        return $this->client->errorMessage();
    }
}

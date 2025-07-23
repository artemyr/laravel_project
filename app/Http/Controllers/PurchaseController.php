<?php

namespace App\Http\Controllers;

use Domain\Order\Exceptions\PaymentProviderException;
use Domain\Order\Payment\PaymentData;
use Domain\Order\Payment\PaymentSystem;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Support\ValueObjects\Price;

class PurchaseController extends Controller
{
    /**
     * TODO необходимо реализовать роутинг и прием данных для платежа
     * @return Application|RedirectResponse|Redirector
     * @throws PaymentProviderException
     */
    public function index()
    {
        $price = 10000;

        return redirect(PaymentSystem::create(new PaymentData(
            '',
            '',
            '',
            Price::make($price),
            collect([])
        ))->url());
    }

    /**
     * NOTE: ручку необходимо внести в исклчение проверки csrf токена
     * @throws PaymentProviderException
     */
    public function callback(): JsonResponse
    {
        return PaymentSystem::validate()->response();
    }
}

<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpUndefinedFieldInspection */

declare(strict_types=1);

namespace App\Http\Requests\Payment;

use App\Contracts\Http\Requests\Payment\PaymentProcessInterface;
use App\Enums\ProductPackageEnum;
use App\Exceptions\Payment\InvalidPaymentGatewayException;
use App\Exceptions\Payment\InvalidPaymentUrlException;
use App\Exceptions\Product\InvalidProductPackageException;
use App\Services\PaymentGateways\Stripe\PaymentGateway;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

final class PaymentProcessRequest extends FormRequest implements PaymentProcessInterface
{
    public function __invoke(): RedirectResponse
    {
        try
        {
            $gateway = $this->gateway;

            if (! is_string(value: $gateway))
            {
                throw new InvalidPaymentGatewayException;
            }

            $processor = match ($gateway)
            {
                'stripe' => PaymentGateway::class,
                default  => throw new InvalidPaymentGatewayException,
            };

            $packageValue = $this->package;

            if (! is_numeric(value: $packageValue))
            {
                throw new InvalidProductPackageException;
            }

            $package = ProductPackageEnum::from(value: (int) $packageValue);

            $redirectTo = $this->buildPaymentLink(
                processor: $processor,
                package  : $package
            );

            if ($redirectTo === null || ! notEmpty(value: $redirectTo))
            {
                throw new InvalidPaymentUrlException;
            }

            return redirect()->away(path: $redirectTo);
        }
        catch (Exception $e)
        {
            return $this->paymentError(
                message: $e->getMessage()
            );
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [];
    }

    public function paymentError(string $message): RedirectResponse
    {
        Session::put('payment-error', $message);

        return redirect()->route(route: 'account');
    }

    public function getGateway(): string
    {
        /** @var string $gateway */
        $gateway = $this->gateway;

        return $gateway;
    }

    public function getPackage(): ProductPackageEnum
    {
        $packageValue = $this->package;

        if (! is_numeric(value: $packageValue))
        {
            throw new InvalidProductPackageException;
        }

        return ProductPackageEnum::from(value: (int) $packageValue);
    }

    /**
     * @param  class-string  $processor
     *
     * @throws BindingResolutionException
     */
    private function buildPaymentLink(string $processor, ProductPackageEnum $package): ?string
    {
        /** @var object $instance */
        $instance = app()->makeWith(abstract: $processor, parameters: [
            'package' => $package,
            'user'    => $this->user(),
        ]);

        if (method_exists(object_or_class: $instance, method: 'process'))
        {
            /** @var string|null $url */
            $url = $instance->process();

            return $url;
        }

        return null;
    }
}

<?php

/** @noinspection SenselessProxyMethodInspection */

declare(strict_types=1);

namespace App\Http\Requests\Webhooks;

use App\Actions\Orders\ProcessAction;
use App\Contracts\Actions\Orders\ProcessActionInterface;
use App\Contracts\Http\Requests\Webhooks\WebhookRequestInterface;
use App\Contracts\Services\PaymentGateways\PaymentWebhookInterface;
use App\Services\PaymentGateways\Stripe\PaymentWebhook;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Stripe\Exception\SignatureVerificationException;
use Stripe\WebhookSignature;

final class StripeWebhookRequest extends FormRequest implements WebhookRequestInterface
{
    private ?PaymentWebhookInterface $paymentWebhook = null;

    private ?ProcessActionInterface $processAction = null;

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $request
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $cookies
     * @param  array<string, mixed>  $files
     * @param  array<string, mixed>  $server
     */
    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct(
            query     : $query,
            request   : $request,
            attributes: $attributes,
            cookies   : $cookies,
            files     : $files,
            server    : $server,
            content   : $content
        );
    }

    /** @throws BindingResolutionException */
    public function __invoke(): JsonResponse
    {
        $paymentWebhook = $this->paymentWebhook ?? new PaymentWebhook(
            stripePayload: $this->getContent()
        );

        $error = $paymentWebhook->getError();

        if (notEmpty(value: $error))
        {
            return response()->json(data: [
                'error' => $error,
            ]);
        }

        /** @var array<string, mixed>|null $paymentData */
        $paymentData = $paymentWebhook->getPaymentData();

        if (! notEmpty(value: $paymentData))
        {
            return response()->json(data: [
                'error' => trans(key: 'exception.stripe.invalid.charge'),
            ]);
        }

        /** @var array<string, mixed> $data */
        $data = $paymentData;

        return ($this->processAction ?? new ProcessAction())->handle(
            paymentData: $data,
        );
    }

    public function setPaymentWebhook(?PaymentWebhookInterface $paymentWebhook): self
    {
        $this->paymentWebhook = $paymentWebhook;

        return $this;
    }

    public function setProcessAction(?ProcessActionInterface $processAction): self
    {
        $this->processAction = $processAction;

        return $this;
    }

    public function authorize(): bool
    {
        try
        {
            $header = $this->header(key: 'stripe-signature');
            $secret = config(key: 'cashier.webhook.secret');

            if ($header === null || ! is_string(value: $secret))
            {
                return false;
            }

            WebhookSignature::verifyHeader(
                payload: $this->getContent(),
                header : $header,
                secret : $secret
            );

            return true;
        }
        catch (SignatureVerificationException $e)
        {
            report(exception: $e);

            return false;
        }
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [];
    }
}

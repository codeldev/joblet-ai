<?php

declare(strict_types=1);

namespace App\Actions\Orders;

use App\Contracts\Actions\Orders\OrderActionInterface;
use App\Contracts\Actions\Orders\PaymentActionInterface;
use App\Contracts\Actions\Orders\ProcessActionInterface;
use App\Models\Order;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;

final class ProcessAction implements ProcessActionInterface
{
    /** @param array<string, mixed> $paymentData
     * @throws BindingResolutionException
     */
    public function handle(array $paymentData): JsonResponse
    {
        /** @var Order|null $order */
        $order = app()->make(abstract: OrderActionInterface::class)
            ->handle(paymentData: $paymentData);

        if ($order !== null)
        {
            return app()->make(abstract: PaymentActionInterface::class)
                ->handle(order: $order, paymentData: $paymentData);
        }

        return response()->json(data: [
            'error' => trans(key: 'payment.webhook.error.order.failed'),
        ]);
    }
}

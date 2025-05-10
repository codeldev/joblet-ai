<?php

declare(strict_types=1);

namespace App\Actions\Orders;

use App\Contracts\Actions\Orders\PaymentActionInterface;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class PaymentAction implements PaymentActionInterface
{
    /** @param array<string,mixed> $paymentData */
    public function handle(Order $order, array $paymentData): JsonResponse // implements PaymentActionInterface
    {
        /** @var User $user */
        $user = $paymentData['user'];

        $order->payment()->create(attributes: [
            'user_id'        => $user->id,
            'amount'         => $paymentData['amount'],
            'gateway'        => $paymentData['gateway'],
            'card_type'      => $paymentData['card_type']      ?? null,
            'card_last4'     => $paymentData['card_last4']     ?? null,
            'event_id'       => $paymentData['event_id']       ?? null,
            'intent_id'      => $paymentData['intent_id']      ?? null,
            'charge_id'      => $paymentData['charge_id']      ?? null,
            'transaction_id' => $paymentData['transaction_id'] ?? null,
            'receipt_url'    => $paymentData['receipt_url']    ?? null,
            'payment_token'  => $paymentData['payment_token']  ?? null,
        ]);

        return response()->json(data: [
            'success' => trans(key: 'payment.webhook.success.order.payment'),
        ]);
    }
}

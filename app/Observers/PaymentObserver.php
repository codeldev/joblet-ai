<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Accounting;
use App\Models\Payment;

final class PaymentObserver
{
    public function creating(Payment $payment): void
    {
        if (empty($payment->invoice_number))
        {
            $payment->invoice_number = $this->getNextInvoiceNumber();
        }
    }

    public function created(Payment $payment): void
    {
        if (Accounting::where(
            column  : 'payment_id',
            operator: '=',
            value   : $payment->id
        )->exists())
        {
            return;
        }

        /** @var array<string,string> $userData */
        $userData = [
            'user_name'      => $payment->user->name,
            'user_email'     => $payment->user->email,
            'payment_id'     => $payment->id,
            'invoice_number' => $payment->invoice_number,
        ];

        /** @var list<string> $paymentFields */
        $paymentFields = [
            'order_id', 'amount', 'gateway', 'card_type', 'card_last4',
            'event_id', 'intent_id', 'charge_id', 'transaction_id',
            'receipt_url', 'payment_token',
        ];

        /** @var list<string> $orderFields */
        $orderFields = [
            'package_id', 'package_name', 'package_description', 'price', 'tokens',
        ];

        /** @var array<string,mixed> $accountingData */
        $accountingData = collect(value: $payment->only(attributes: $paymentFields))
            ->merge(items: collect(value: $payment->order->only(attributes: $orderFields)))
            ->merge(items: $userData)
            ->toArray();

        Accounting::create(attributes: $accountingData);
    }

    private function getNextInvoiceNumber(): int
    {
        /** @var int $defaultNumber */
        $defaultNumber = config(key: 'settings.invoices.initial');

        $maxResult = Accounting::query()
            ->lockForUpdate()
            ->max(column: 'invoice_number');

        if ($maxResult === null)
        {
            return $defaultNumber + 1;
        }

        $currentMax = is_numeric(value: $maxResult)
            ? (int) $maxResult
            : $defaultNumber;

        return $currentMax + 1;
    }
}

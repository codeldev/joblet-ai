<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace App\Models;

use App\Observers\PaymentObserver;
use Carbon\CarbonImmutable;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Cashier\Cashier;

/**
 * @property-read string $id
 * @property-read string $order_id
 * @property-read string $user_id
 * @property int $invoice_number
 * @property-read int $amount
 * @property-read string $gateway
 * @property-read null|string $card_type
 * @property-read null|string $card_last4
 * @property-read null|string $event_id
 * @property-read null|string $intent_id
 * @property-read null|string $charge_id
 * @property-read null|string $transaction_id
 * @property-read null|string $receipt_url
 * @property-read null|string $payment_token
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read string $formatted_amount
 * @property-read string $payment_method
 * @property-read string $formatted_invoice_number
 * @property-read Order $order
 * @property-read User $user
 */
#[ObservedBy(PaymentObserver::class)]
final class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    /** @see HasUuids */
    use HasUuids;

    /** @var string */
    protected $table = 'payments';

    /** @var array<string, string> */
    protected $casts = [
        'invoice_number' => 'integer',
        'gateway'        => 'encrypted',
        'card_type'      => 'encrypted',
        'card_last4'     => 'encrypted',
        'amount'         => 'integer',
    ];

    /** @return BelongsTo<Order, $this> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(
            related: Order::class
        );
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class
        );
    }

    /** @return Attribute<string, never> */
    public function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Cashier::formatAmount(amount: $this->amount)
        );
    }

    /** @return Attribute<string, never> */
    public function paymentMethod(): Attribute
    {
        return Attribute::make(
            get: fn (): string => trans(key: 'orders.payment.type.card', replace: [
                'type' => $this->card_type  ?? '',
                'last' => $this->card_last4 ?? '',
            ]),
        );
    }

    /** @return Attribute<string, never> */
    public function formattedInvoiceNumber(): Attribute
    {
        return Attribute::make(get: function (): string
        {
            /** @var int $numberPadding */
            $numberPadding = config(key: 'settings.invoices.padding');

            $invoiceNumber = (string) $this->invoice_number;

            return str(string: $invoiceNumber)
                ->padLeft(length: $numberPadding, pad: '0')
                ->toString();
        });
    }
}

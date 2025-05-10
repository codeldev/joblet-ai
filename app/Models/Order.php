<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Cashier\Cashier;

/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read int $package_id
 * @property-read string $package_name
 * @property-read string $package_description
 * @property-read int $price
 * @property-read int $tokens
 * @property-read bool $free
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read string $formatted_price
 */
final class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    /** @see HasUuids */
    use HasUuids;

    /** @var string */
    protected $table = 'orders';

    /** @var array<string, string> */
    protected $casts = [
        'price'      => 'integer',
        'tokens'     => 'integer',
        'free'       => 'boolean',
        'package_id' => 'integer',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class
        );
    }

    /** @return HasOne<Payment, $this> */
    public function payment(): HasOne
    {
        return $this->hasOne(
            related: Payment::class
        );
    }

    /** @return Attribute<string, never> */
    public function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Cashier::formatAmount(amount: $this->price)
        );
    }
}

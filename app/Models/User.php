<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\SupportUserScope;
use App\Notifications\Auth\LoginLinkNotification;
use App\Observers\UserObserver;
use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

/**
 * @property-read string $id
 * @property-read string $name
 * @property string $email
 * @property string $password
 * @property-read string $rememberToken
 * @property-read null|string $cv_filename
 * @property-read null|string $cv_content
 * @property-read null|string $stripe_id
 * @property-read null|string $pm_type
 * @property-read null|string $pm_last_four
 * @property-read null|CarbonImmutable $trial_ends_at
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Collection<int, Generated> $generated
 * @property-read Collection<int, Order> $orders
 * @property-read Collection<int, Usage> $usage
 */
#[ObservedBy(UserObserver::class)]
#[ScopedBy(SupportUserScope::class)]
final class User extends Authenticatable
{
    /** @see Billable */
    use Billable;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    /** @see HasUuids */
    use HasUuids;

    /** @see Notifiable */
    use Notifiable;

    /** @var string */
    protected $table = 'users';

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function sendLoginLinkNotification(): void
    {
        $this->notify(
            instance: new LoginLinkNotification
        );
    }

    /** @return HasMany<Generated, $this> */
    public function generated(): HasMany
    {
        return $this->hasMany(
            related: Generated::class
        );
    }

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(
            related: Order::class
        );
    }

    /** @return HasMany<Usage, $this> */
    public function usage(): HasMany
    {
        return $this->hasMany(
            related: Usage::class
        );
    }
}

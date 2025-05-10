<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\UserHistoryFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $id
 * @property-read string $email
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 */
final class UserHistory extends Model
{
    /** @use HasFactory<UserHistoryFactory> */
    use HasFactory;

    /** @see HasUuids */
    use HasUuids;

    /** @var string */
    protected $table = 'users_history';
}

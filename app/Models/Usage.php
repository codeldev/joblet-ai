<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\UsageFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $id
 * @property-read string $user_id
 * @property-read string $generated_id
 * @property-read int $word_count
 * @property-read int $tokens_used
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Generated $asset
 * @property-read User $user
 */
final class Usage extends Model
{
    /** @use HasFactory<UsageFactory> */
    use HasFactory;

    /** @see HasUuids */
    use HasUuids;

    /** @var string */
    protected $table = 'usage';

    /** @var array<string, string> */
    protected $casts = [
        'word_count'  => 'integer',
        'tokens_used' => 'integer',
    ];

    /** @return BelongsTo<Generated, $this> */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(
            related   : Generated::class,
            foreignKey: 'generated_id'
        );
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class
        );
    }
}

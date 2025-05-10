<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MessageTypeEnum;
use Carbon\CarbonImmutable;
use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $id
 * @property-read string|null $name
 * @property-read string|null $email
 * @property-read string|null $user_id
 * @property-read string $message
 * @property-read int $type
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 */
final class Message extends Model
{
    /** @use HasFactory<MessageFactory> */
    use HasFactory;

    /** @see HasUuids */
    use HasUuids;

    /** @var string */
    protected $table = 'messages';

    /** @var array<string, string> */
    protected $casts = [
        'name'    => 'encrypted',
        'email'   => 'encrypted',
        'message' => 'encrypted',
        'type'    => MessageTypeEnum::class,
    ];
}

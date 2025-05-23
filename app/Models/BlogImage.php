<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BlogImageTypeEnum;
use Carbon\CarbonImmutable;
use Database\Factories\BlogImageFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $id
 * @property-read string $post_id
 * @property-read int $type
 * @property-read string $description
 * @property-read string $files
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read BlogPost $post
 */
final class BlogImage extends Model
{
    /** @use HasFactory<BlogImageFactory> */
    use HasFactory;

    /** @see HasUuids */
    use HasUuids;

    /** @var string */
    protected $table = 'blog_images';

    /** @var array<string, string> */
    protected $casts = [
        'type'  => BlogImageTypeEnum::class,
        'files' => AsArrayObject::class,
    ];

    /** @return BelongsTo<BlogPost, $this> */
    public function post(): BelongsTo
    {
        return $this->belongsTo(
            related   : BlogPost::class,
            foreignKey: 'post_id',
            ownerKey  : 'id'
        );
    }
}

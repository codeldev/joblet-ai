<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\BlogPromptFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read string $id
 * @property-read string $meta_title
 * @property-read string $meta_description
 * @property-read string $post_content
 * @property-read string $post_summary
 * @property-read string $image_prompt
 * @property-read string $system_prompt
 * @property-read string $user_prompt
 * @property-read string $content_images
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read BlogPost $post
 */
final class BlogPrompt extends Model
{
    /** @use HasFactory<BlogPromptFactory> */
    use HasFactory;

    /** @see HasUuids */
    use HasUuids;

    /** @var string */
    protected $table = 'blog_prompts';

    /** @var array<string, string> */
    protected $casts = [
        'content_images' => AsArrayObject::class,
    ];

    /** @return HasOne<BlogPost, $this> */
    public function post(): HasOne
    {
        return $this->hasOne(
            related    : BlogPost::class,
            foreignKey : 'prompt_id',
            localKey   : 'id'
        );
    }
}

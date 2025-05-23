<?php

declare(strict_types=1);

use App\Enums\PostStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'blog_posts', callback: static function (Blueprint $table): void
        {
            $table->uuid(column: 'id')
                ->primary();

            $table->foreignUuid(column: 'idea_id')
                ->index()
                ->constrained(table: 'blog_ideas')
                ->cascadeOnDelete();

            $table->foreignUuid(column: 'prompt_id')
                ->index()
                ->constrained(table: 'blog_prompts')
                ->cascadeOnDelete();

            $table->string(column: 'title');

            $table->string(column: 'slug')
                ->unique();

            $table->string(column: 'description');

            $table->mediumText(column: 'summary');

            $table->longText(column: 'content');

            $table->unsignedTinyInteger(column: 'status')
                ->default(value: PostStatusEnum::DRAFT);

            $table->timestamp(column: 'published_at')
                ->nullable();

            $table->timestamp(column: 'scheduled_at')
                ->nullable();

            $table->boolean(column: 'has_featured_image')
                ->default(value: false);

            $table->unsignedSmallInteger(column: 'word_count');

            $table->unsignedTinyInteger(column: 'read_time');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'blog_posts');
    }
};

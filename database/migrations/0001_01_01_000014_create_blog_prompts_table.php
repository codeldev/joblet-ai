<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'blog_prompts', callback: static function (Blueprint $table): void
        {
            $table->uuid(column: 'id')
                ->primary();

            $table->text(column: 'meta_title');

            $table->text(column: 'meta_description');

            $table->longText(column: 'post_content');

            $table->mediumText(column: 'post_summary');

            $table->longText(column: 'image_prompt');

            $table->longText(column: 'system_prompt');

            $table->longText(column: 'user_prompt');

            $table->json(column: 'content_images');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'blog_prompts');
    }
};

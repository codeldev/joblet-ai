<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'blog_images', callback: static function (Blueprint $table): void
        {
            $table->uuid(column: 'id')
                ->primary();

            $table->foreignUuid(column: 'post_id')
                ->index()
                ->constrained(table: 'blog_posts')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger(column: 'type');

            $table->text(column: 'description');

            $table->json(column: 'files');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'blog_images');
    }
};

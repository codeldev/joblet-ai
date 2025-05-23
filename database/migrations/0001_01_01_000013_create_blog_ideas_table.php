<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'blog_ideas', callback: static function (Blueprint $table): void
        {
            $table->uuid(column: 'id')
                ->primary();

            $table->text(column: 'topic');

            $table->text(column: 'keywords');

            $table->longText(column: 'focus');

            $table->longtext(column: 'requirements');

            $table->longText(column: 'additional');

            $table->dateTime(column: 'schedule_date');

            $table->dateTime(column: 'queued_at')
                ->nullable();

            $table->dateTime(column: 'processed_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'blog_ideas');
    }
};

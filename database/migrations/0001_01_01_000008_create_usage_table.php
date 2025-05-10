<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'usage', callback: static function (Blueprint $table): void
        {
            $table->uuid(column: 'id')
                ->primary();

            $table->foreignUuid(column: 'user_id')
                ->index()
                ->constrained(table: 'users')
                ->cascadeOnDelete();

            $table->foreignUuid(column: 'generated_id')
                ->index()
                ->constrained(table: 'generated')
                ->cascadeOnDelete();

            $table->unsignedInteger(column: 'word_count')
                ->default(value: 0);

            $table->unsignedInteger(column: 'tokens_used')
                ->default(value: 0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'usage');
    }
};

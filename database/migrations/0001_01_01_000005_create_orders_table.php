<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'orders', callback: static function (Blueprint $table): void
        {
            $table->uuid(column: 'id')
                ->primary();

            $table->foreignUuid(column: 'user_id')
                ->index()
                ->constrained(table: 'users')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger(column: 'package_id');

            $table->string(column: 'package_name');

            $table->string(column: 'package_description');

            $table->unsignedSmallInteger(column: 'price');

            $table->unsignedSmallInteger(column: 'tokens');

            $table->boolean(column: 'free')
                ->default(value: false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'orders');
    }
};

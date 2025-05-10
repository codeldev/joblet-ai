<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'users_history', callback: static function (Blueprint $table): void
        {
            $table->uuid(column: 'id')
                ->primary();

            $table->string(column: 'email')
                ->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'users_history');
    }
};

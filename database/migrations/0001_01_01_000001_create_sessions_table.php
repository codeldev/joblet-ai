<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'sessions', callback: static function (Blueprint $table): void
        {
            $table->string(column: 'id')
                ->primary();

            $table->foreignUuid(column: 'user_id')
                ->nullable()
                ->index();

            $table->string(column: 'ip_address', length: 45)
                ->nullable();

            $table->text(column: 'user_agent')
                ->nullable();

            $table->longText(column: 'payload');

            $table->integer(column: 'last_activity')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'sessions');
    }
};

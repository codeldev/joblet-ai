<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'messages', callback: static function (Blueprint $table): void
        {
            $table->uuid(column: 'id')
                ->primary();

            $table->text(column: 'name')
                ->nullable();

            $table->text(column: 'email')
                ->nullable();

            $table->mediumText(column: 'message');

            $table->unsignedTinyInteger(column: 'type');

            $table->uuid(column: 'user_id')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'messages');
    }
};

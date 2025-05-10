<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'pan_analytics', callback: static function (Blueprint $table): void
        {
            $table->id();

            $table->string(column: 'name');

            $table->unsignedBigInteger(column: 'impressions')
                ->default(value: 0);

            $table->unsignedBigInteger(column: 'hovers')
                ->default(value: 0);

            $table->unsignedBigInteger(column: 'clicks')
                ->default(value: 0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'pan_analytics');
    }
};

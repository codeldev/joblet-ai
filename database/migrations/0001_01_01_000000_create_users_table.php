<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'users', callback: static function (Blueprint $table): void
        {
            $table->uuid(column: 'id')
                ->primary();

            $table->string(column: 'name');

            $table->string(column: 'email')
                ->unique();

            $table->string(column: 'password');

            $table->rememberToken();

            $table->text(column: 'cv_filename')
                ->nullable();

            $table->longText(column: 'cv_content')
                ->nullable();

            $table->string(column: 'stripe_id')
                ->nullable()
                ->index();

            $table->string(column: 'pm_type')
                ->nullable();

            $table->string(column: 'pm_last_four', length: 4)
                ->nullable();

            $table->timestamp(column: 'trial_ends_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'users');
    }
};

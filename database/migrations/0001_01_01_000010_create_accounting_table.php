<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'accounting', callback: static function (Blueprint $table): void
        {
            $table->uuid(column: 'id')
                ->primary();

            $table->text(column: 'user_name');

            $table->text(column: 'user_email');

            $table->uuid(column: 'order_id');

            $table->unsignedTinyInteger(column: 'package_id');

            $table->string(column: 'package_name');

            $table->string(column: 'package_description');

            $table->unsignedSmallInteger(column: 'price');

            $table->unsignedSmallInteger(column: 'tokens');

            $table->uuid(column: 'payment_id');

            $table->unsignedInteger(column: 'invoice_number')
                ->unique();

            $table->unsignedSmallInteger(column: 'amount');

            $table->text(column: 'gateway');

            $table->text(column: 'card_type')
                ->nullable();

            $table->text(column: 'card_last4')
                ->nullable();

            $table->string(column: 'event_id')
                ->nullable();

            $table->string(column: 'intent_id')
                ->nullable();

            $table->string(column: 'charge_id')
                ->nullable();

            $table->string(column: 'transaction_id')
                ->nullable();

            $table->text(column: 'receipt_url')
                ->nullable();

            $table->string(column: 'payment_token')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'accounting');
    }
};

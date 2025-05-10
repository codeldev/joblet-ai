<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'payments', callback: static function (Blueprint $table): void
        {
            $table->uuid(column: 'id')
                ->primary();

            $table->foreignUuid(column: 'order_id')
                ->index()
                ->constrained(table: 'orders')
                ->cascadeOnDelete();

            $table->foreignUuid(column: 'user_id')
                ->index()
                ->constrained(table: 'users')
                ->cascadeOnDelete();

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
        Schema::dropIfExists(table: 'payments');
    }
};

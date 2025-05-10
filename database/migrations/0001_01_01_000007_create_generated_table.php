<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'generated', callback: static function (Blueprint $table): void
        {
            $table->uuid(column: 'id')
                ->primary();

            $table->foreignUuid(column: 'user_id')
                ->index()
                ->constrained(table: 'users')
                ->cascadeOnDelete();

            $table->text(column: 'name');

            $table->text(column: 'job_title');

            $table->mediumText('job_description');

            $table->text(column: 'company')
                ->nullable();

            $table->text(column: 'manager')
                ->nullable();

            $table->boolean(column: 'include_placeholders')
                ->default(value: false);

            $table->mediumText(column: 'problem_solving_text')
                ->nullable();

            $table->mediumText(column: 'growth_interest_text')
                ->nullable();

            $table->mediumText(column: 'unique_value_text')
                ->nullable();

            $table->mediumText(column: 'achievements_text')
                ->nullable();

            $table->mediumText(column: 'motivation_text')
                ->nullable();

            $table->mediumText(column: 'career_goals')
                ->nullable();

            $table->mediumText(column: 'other_details')
                ->nullable();

            $table->unsignedTinyInteger(column: 'language_variant');

            $table->unsignedTinyInteger(column: 'date_format');

            $table->unsignedTinyInteger(column: 'option_creativity');

            $table->unsignedTinyInteger(column: 'option_tone');

            $table->unsignedTinyInteger(column: 'option_length');

            $table->longtext(column: 'generated_content_raw');

            $table->longtext(column: 'generated_content_html');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'generated');
    }
};

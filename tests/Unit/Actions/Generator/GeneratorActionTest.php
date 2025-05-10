<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Generator\GenerateAction;
use App\Models\Generated;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;

beforeEach(function (): void
{
    $this->text = fake()->paragraph(nbSentences: 4);
    $this->user = testUser();

    $this->settings = collect(value: Generated::factory()->make()->except([
        'id',
        'user_id',
        'generated_content_raw',
        'generated_content_html',
    ]))->map(callback: fn ($value) => $value instanceof BackedEnum ? $value->value : $value)->toArray();
});

it('stores generated content and returns asset through success callback', function (): void
{
    OpenAI::fake(
        responses: [fakeOpenAiResponse(content: $this->text)]
    );

    $this->actingAs(user: $this->user);

    $resultAsset = null;

    (new GenerateAction)->handle(
        settings: $this->settings,
        success : function ($asset) use (&$resultAsset): void
        {
            $resultAsset = $asset;
        },
        failed: fn ($message) => null,
    );

    $credit = $resultAsset->credit()->first();

    expect(value: $resultAsset)
        ->toBeInstanceOf(class: Generated::class)
        ->and(value: $resultAsset->generated_content_raw)
        ->toBe(expected: $this->text)
        ->and(value: $resultAsset->generated_content_html)
        ->toBe(expected: nl2br(string: $this->text))
        ->and(value: $resultAsset->user_id)
        ->toBe(expected: $this->user->id)
        ->and(value: $credit)
        ->not->toBeNull()
        ->and(value: $credit->user_id)
        ->toBe(expected: $this->user->id)
        ->and(value: $credit->word_count)
        ->toBe(expected: str_word_count(string: strip_tags(string: $this->text)))
        ->and(value: $credit->tokens_used)
        ->toBeGreaterThan(expected: 0);
});

it('gives a failed error response when AI generated content is empty', function (): void
{
    OpenAI::fake(
        responses: [fakeOpenAiResponse(content: '')]
    );

    $this->actingAs(user: $this->user);

    $resultMessage = null;

    (new GenerateAction)->handle(
        settings: $this->settings,
        success : fn ($asset) => null,
        failed  : function ($message) use (&$resultMessage): void
        {
            $resultMessage = $message;
        },
    );

    expect(value: $resultMessage)
        ->toBe(expected: trans(key: 'generator.generation.failed'));
});

it('does not generate an asset for a guest user', function (): void
{
    OpenAI::fake(
        responses: [fakeOpenAiResponse(content: $this->text)]
    );

    Auth::shouldReceive('user')
        ->once()
        ->andReturn(null);

    $resultAsset = null;

    (new GenerateAction)->handle(
        settings: $this->settings,
        success : function ($asset) use (&$resultAsset): void
        {
            $resultAsset = $asset;
        },
        failed: fn ($message) => null,
    );

    expect(value: $resultAsset)
        ->toBeNull();
});

it('does not generate an asset when auth user is not an instance of User', function (): void
{
    OpenAI::fake(
        responses: [fakeOpenAiResponse(content: $this->text)]
    );

    $nonUserObject = new stdClass();

    Auth::shouldReceive('user')
        ->once()
        ->andReturn($nonUserObject);

    $resultAsset = null;

    (new GenerateAction)->handle(
        settings: $this->settings,
        success : function ($asset) use (&$resultAsset): void
        {
            $resultAsset = $asset;
        },
        failed: fn ($message) => null,
    );

    expect(value: $resultAsset)
        ->toBeNull();
});

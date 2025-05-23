<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Exceptions\AiProviders\NoValidContentInResponseException;
use App\Facades\OpenAI;
use App\Models\BlogIdea;
use App\Services\AiProviders\OpenAI\ChatGpt;
use Illuminate\Support\Facades\Config;

beforeEach(closure: function (): void
{
    $this->blogIdea = BlogIdea::factory()->create(attributes: [
        'topic'        => 'Test Blog Topic',
        'keywords'     => 'keyword1, keyword2',
        'focus'        => 'SEO optimization',
        'requirements' => 'Include sections on introduction and conclusion',
        'additional'   => 'Add references where appropriate',
    ]);

    $this->chatGpt = new ChatGpt();

    Config::set('services.openai.api_key', 'test-api-key');

    $this->mockResponseContent = json_encode(value: [
        'title'            => 'Generated Blog Title',
        'content'          => 'Generated blog content',
        'meta_description' => 'Meta description for SEO',
    ], flags: JSON_THROW_ON_ERROR);
});

test(description: 'handle method initializes setup and returns result from get method', closure: function (): void
{
    $mockResponse = fakeOpenAiResponseObject(
        content: $this->mockResponseContent
    );

    OpenAI::fake(responses: [$mockResponse]);

    expect(value: $this->chatGpt->handle(blogIdea: $this->blogIdea))
        ->toBe(expected: [
            'title'            => 'Generated Blog Title',
            'content'          => 'Generated blog content',
            'meta_description' => 'Meta description for SEO',
        ])
        ->and(value: $this->chatGpt->getUserPrompt())
        ->not()->toBeEmpty()
        ->and(value: $this->chatGpt->getSystemPrompt())
        ->not()->toBeEmpty();
});

test(description: 'get method builds payload and returns decoded response', closure: function (): void
{
    $this->chatGpt->initSetup(
        apiConfigKey: 'services.openai.api_key',
        blogIdea: $this->blogIdea
    );

    $mockResponse = fakeOpenAiResponseObject(
        content: $this->mockResponseContent
    );

    OpenAI::fake(responses: [$mockResponse]);

    expect(value: $this->chatGpt->get())->toBe(expected: [
        'title'            => 'Generated Blog Title',
        'content'          => 'Generated blog content',
        'meta_description' => 'Meta description for SEO',
    ]);
});

test(description: 'get method throws exception when API returns invalid JSON', closure: function (): void
{
    $this->chatGpt->initSetup(
        apiConfigKey: 'services.openai.api_key',
        blogIdea    : $this->blogIdea
    );

    $invalidResponse = fakeOpenAiResponseObject(
        content: 'This is not valid JSON'
    );

    OpenAI::fake(responses: [$invalidResponse]);

    expect(value: fn () => $this->chatGpt->get())
        ->toThrow(exception: JsonException::class);
});

test(description: 'buildPayload method creates correctly structured payload through get method', closure: function (): void
{
    $this->chatGpt->initSetup(
        apiConfigKey: 'services.openai.api_key',
        blogIdea    : $this->blogIdea
    );

    $mockResponse = fakeOpenAiResponseObject(
        content: $this->mockResponseContent
    );

    OpenAI::fake(responses: [$mockResponse]);

    expect(value: $this->chatGpt->get())->toBe(expected: [
        'title'            => 'Generated Blog Title',
        'content'          => 'Generated blog content',
        'meta_description' => 'Meta description for SEO',
    ]);
});

test(description: 'buildPayload method handles exceptions', closure: function (): void
{
    $this->chatGpt->initSetup(
        apiConfigKey: 'services.openai.api_key',
        blogIdea    : $this->blogIdea
    );

    $testException = new Exception(
        message: 'Test exception'
    );

    OpenAI::fake(responses: [$testException]);

    expect(value: fn () => $this->chatGpt->get())
        ->toThrow(exception: Exception::class);
});

test(description: 'get method throws exception when response has empty choices', closure: function (): void
{
    $this->chatGpt->initSetup(
        apiConfigKey: 'services.openai.api_key',
        blogIdea    : $this->blogIdea
    );

    $emptyChoicesResponse = (object) ['choices' => []];

    OpenAI::fake(responses: [$emptyChoicesResponse]);

    expect(value: fn () => $this->chatGpt->get())
        ->toThrow(exception: NoValidContentInResponseException::class);
});

test(description: 'get method throws exception when response has empty content', closure: function (): void
{
    $this->chatGpt->initSetup(
        apiConfigKey: 'services.openai.api_key',
        blogIdea    : $this->blogIdea
    );

    $emptyContentResponse = (object) [
        'choices' => [(object) ['message' => (object) ['content' => '']]],
    ];

    OpenAI::fake(responses: [$emptyContentResponse]);

    expect(value: fn () => $this->chatGpt->get())
        ->toThrow(exception: NoValidContentInResponseException::class);
});

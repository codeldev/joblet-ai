<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Services\AiProviders\OpenAI\OpenAIClientInterface;
use App\Exceptions\AiProviders\OpenAiApiKeyNotConfiguredException;
use App\Facades\OpenAI as OpenAIFacade;
use App\Services\AiProviders\OpenAI\OpenAIClient;

beforeEach(closure: function (): void
{
    OpenAIFacade::clearResolvedInstances();

    OpenAIFacade::fake(
        responses: [fakeOpenAiResponse(content: 'Test response')]
    );

    $this->openAIClient = app(abstract: OpenAIClientInterface::class);
    $this->testApiKey   = 'test-api-key';
    $this->parameters   = [
        'model'    => 'gpt-4',
        'messages' => [['role' => 'user', 'content' => 'Hello']],
    ];
});

describe(description: 'OpenAIClient', tests: function (): void
{
    it(description: 'initializes client with api key', closure: function (): void
    {
        expect(value: $this->openAIClient->client($this->testApiKey))
            ->toBe(expected: $this->openAIClient)
            ->and(value: $this->openAIClient->chat())
            ->toBeObject()
            ->and(value: $this->openAIClient->create($this->parameters))
            ->toBeObject();
    });

    it(description: 'returns client instance for method chaining', closure: function (): void
    {
        $client = new OpenAIClient();
        $result = $client->client(apiKey: $this->testApiKey);
        $chat   = $client->chat();

        expect(value: $result)
            ->toBe(expected: $client)
            ->and(value: $chat)
            ->toBeObject()
            ->and(value: method_exists(object_or_class: $chat, method: 'create'))
            ->toBeTrue();
    });

    it(description: 'returns chat instance', closure: function (): void
    {
        $this->openAIClient->client($this->testApiKey);

        $result = $this->openAIClient->chat();

        expect(value: $result)
            ->toBeObject()
            ->and(value: method_exists(object_or_class: $result, method: 'create'))
            ->toBeTrue();
    });

    it(description: 'returns chat instance with create method', closure: function (): void
    {
        $this->openAIClient->client($this->testApiKey);

        $chat   = $this->openAIClient->chat();
        $result = $chat->create($this->parameters);

        expect(value: $chat)
            ->toBeObject()
            ->and(value: method_exists(object_or_class: $chat, method: 'create'))
            ->toBeTrue()
            ->and(value: $result)
            ->toBeObject()
            ->toHaveProperty(name: 'id')
            ->toHaveProperty(name: 'object')
            ->and(value: $result->id)
            ->toBeString()
            ->and(value: $result->object)
            ->toBe(expected: 'chat.completion');
    });

    it(description: 'throws exception when chat called before initialization', closure: function (): void
    {
        $client = new OpenAIClient();

        $this->expectException(
            exception: OpenAiApiKeyNotConfiguredException::class
        );

        $client->chat();
    });

    it(description: 'creates chat completion', closure: function (): void
    {
        $this->openAIClient->client($this->testApiKey);

        $result = $this->openAIClient->create($this->parameters);

        expect(value: $result)
            ->toBeObject()
            ->toHaveProperty(name: 'id')
            ->toHaveProperty(name: 'object')
            ->toHaveProperty(name: 'created')
            ->toHaveProperty(name: 'model')
            ->toHaveProperty(name: 'choices')
            ->and(value: $result->id)
            ->toBeString()
            ->and(value: $result->object)
            ->toBe(expected: 'chat.completion')
            ->and(value: $result->choices)
            ->toBeArray()
            ->and(value: $result->choices[0]->message->content ?? null)
            ->toBeString();
    });

    it(description: 'creates chat completion with chat method', closure: function (): void
    {
        $this->openAIClient->client($this->testApiKey);

        $result = $this->openAIClient
            ->chat()
            ->create($this->parameters);

        expect(value: $result)
            ->toBeObject()
            ->toHaveProperty(name: 'id')
            ->toHaveProperty(name: 'object')
            ->and(value: $result->id)
            ->toBeString()
            ->and(value: $result->object)
            ->toBe(expected: 'chat.completion');
    });

    it(description: 'handles chained method calls correctly', closure: function (): void
    {
        $this->openAIClient->client($this->testApiKey);

        $result = $this->openAIClient
            ->client($this->testApiKey)
            ->chat()
            ->create($this->parameters);

        expect(value: $result)
            ->toBeObject()
            ->toHaveProperty(name: 'id')
            ->toHaveProperty(name: 'object')
            ->and(value: $result->id)
            ->toBeString()
            ->and(value: $result->object)
            ->toBe(expected: 'chat.completion');
    });

    it(description: 'creates chat completion with direct create call', closure: function (): void
    {
        $result = $this->openAIClient
            ->client($this->testApiKey)
            ->create($this->parameters);

        expect(value: $result)
            ->toBeObject()
            ->toHaveProperty(name: 'id')
            ->toHaveProperty(name: 'object')
            ->and(value: $result->id)
            ->toBeString()
            ->and(value: $result->object)
            ->toBe(expected: 'chat.completion');
    });

    it(description: 'throws exception when create called before initialization', closure: function (): void
    {
        $client = new OpenAIClient();

        $this->expectException(
            exception: OpenAiApiKeyNotConfiguredException::class
        );

        $client->create(parameters: ['model' => 'gpt-4', 'messages' => []]);
    });

    it(description: 'returns response from create method', closure: function (): void
    {
        $this->openAIClient->client(
            apiKey: $this->testApiKey
        );

        $response = $this->openAIClient->create(
            parameters: $this->parameters
        );

        expect(value: $response)
            ->toBeObject()
            ->toHaveProperty(name: 'id')
            ->toHaveProperty(name: 'object')
            ->toHaveProperty(name: 'choices')
            ->and(value: $response->object)
            ->toBe(expected: 'chat.completion');
    });

    it(description: 'returns images instance', closure: function (): void
    {
        $this->openAIClient->client($this->testApiKey);

        $result = $this->openAIClient->images();

        expect(value: $result)
            ->toBeObject()
            ->and(value: method_exists(object_or_class: $result, method: 'create'))
            ->toBeTrue();
    });

    it(description: 'throws exception when images called before initialization', closure: function (): void
    {
        $client = new OpenAIClient();

        $this->expectException(
            exception: OpenAiApiKeyNotConfiguredException::class
        );

        $client->images();
    });

    it(description: 'can create images with chained method calls', closure: function (): void
    {
        $fakeResponse = fakeOpenAiResponse(content: 'Test response');

        OpenAIFacade::fake(responses: [$fakeResponse]);

        $this->openAIClient->client($this->testApiKey);

        $result = $this->openAIClient
            ->images()
            ->create(parameters: [
                'model'           => 'dall-e-3',
                'prompt'          => 'A test image',
                'n'               => 1,
                'size'            => '1024x1024',
                'response_format' => 'b64_json',
            ]);

        expect(value: $result)
            ->toBeObject()
            ->and(value: $result->choices[0]->message->content ?? null)
            ->toBe('Test response');
    });

    it(description: 'directly returns the response from chat create method', closure: function (): void
    {
        $this->openAIClient->client(
            apiKey: $this->testApiKey
        );

        $result = $this->openAIClient->create(
            parameters: $this->parameters
        );

        expect(value: $result)
            ->toBeObject()
            ->toHaveProperty(name: 'id')
            ->toHaveProperty(name: 'object')
            ->toHaveProperty(name: 'choices')
            ->and(value: $result->choices)
            ->toBeArray()
            ->and(value: $result->choices[0]->message->content ?? null)
            ->not()->toBeNull();
    });

    it(description: 'ensures create method handles client type checking correctly', closure: function (): void
    {
        OpenAIFacade::fake(
            responses: [fakeOpenAiResponse(content: 'Type checking test')]
        );

        $client = app(abstract: OpenAIClientInterface::class);
        $client->client(apiKey: $this->testApiKey);

        expect(value: $client->create(parameters: $this->parameters))
            ->toBeObject()
            ->toHaveProperty(name: 'id')
            ->toHaveProperty(name: 'object');
    });

    it(description: 'throws exception when create is called with null client', closure: function (): void
    {
        expect(value: fn () => (new OpenAIClient)->create(parameters: $this->parameters))
            ->toThrow(exception: OpenAiApiKeyNotConfiguredException::class);
    });

    it(description: 'ensures images method returns an object with create method', closure: function (): void
    {
        $imagesResponse = (object) [
            'created' => time(),
            'data'    => [(object) ['b64_json' => 'test_image_data']],
        ];

        OpenAIFacade::fake(responses: [$imagesResponse]);

        $client = app(abstract: OpenAIClientInterface::class);
        $client->client(apiKey: 'test-api-key');

        $images = $client->images();

        expect(value: $images)
            ->toBeObject()
            ->and(value: method_exists(object_or_class: $images, method: 'create'))
            ->toBeTrue();
    });

    it(description: 'ensures images method can be chained with create', closure: function (): void
    {
        $imagesResponse = (object) [
            'created' => time(),
            'data'    => [(object) ['b64_json' => 'test_image_data']],
        ];

        OpenAIFacade::fake(responses: [$imagesResponse]);

        $client = app(abstract: OpenAIClientInterface::class);
        $client->client(apiKey: 'test-api-key');

        $result = $client->images()->create(parameters: [
            'model'  => 'dall-e-3',
            'prompt' => 'A test image',
            'n'      => 1,
            'size'   => '1024x1024',
        ]);

        expect(value: $result)
            ->toBeObject()
            ->toBe(expected: $imagesResponse);
    });
});

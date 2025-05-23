<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Services\AiProviders\Anthropic\AnthropicClientInterface;
use App\Exceptions\AiProviders\AnthropicApiKeyNotConfiguredException;
use App\Exceptions\Blog\BlogPostContentModelNotSetException;
use App\Facades\Anthropic as AnthropicFacade;
use App\Services\AiProviders\Anthropic\AnthropicClient;
use Claude\Claude3Api\Client;
use Claude\Claude3Api\Models\Tool;
use Claude\Claude3Api\Responses\MessageResponse;
use Illuminate\Support\Facades\Config;

beforeEach(closure: function (): void
{
    AnthropicFacade::clearResolvedInstances();

    AnthropicFacade::fake(
        responses: [fakeAnthropicResponse(content: 'Test response')]
    );

    Config::set('blog.post.model', 'claude-3-opus-20240229');

    $this->anthropicClient = app(abstract: AnthropicClientInterface::class);
    $this->testApiKey      = 'test-api-key';
    $this->parameters      = [
        'model'    => 'claude-3-opus-20240229',
        'messages' => [['role' => 'user', 'content' => 'Hello']],
    ];

    $this->tool = new Tool(
        name       : 'test_tool',
        description: 'A test tool',
        inputSchema: [
            'type'       => 'object',
            'properties' => [
                'test' => [
                    'type'        => 'string',
                    'description' => 'Test property',
                ],
            ],
        ]
    );
});

afterEach(closure: function (): void
{
    AnthropicFacade::clearResolvedInstances();
});

describe(description: 'AnthropicClient', tests: function (): void
{
    it(description: 'initializes client with api key', closure: function (): void
    {
        $client = new AnthropicClient();

        expect(value: $client->client(apiKey: $this->testApiKey))
            ->toBeInstanceOf(class: AnthropicClient::class);
    });

    it(description: 'throws exception when sendMessage called before initialization', closure: function (): void
    {
        $client = new AnthropicClient();

        expect(value: fn () => $client->sendMessage(parameters: $this->parameters))->toThrow(
            exception : AnthropicApiKeyNotConfiguredException::class
        );
    });

    it(description: 'returns response from sendMessage', closure: function (): void
    {
        $client = $this->anthropicClient;
        $client->client(apiKey: $this->testApiKey);

        $result = $client->sendMessage(parameters: $this->parameters);

        expect(value: $result)
            ->toBeInstanceOf(class: MessageResponse::class)
            ->and(value: $result->getContent())
            ->toBeArray()
            ->not()->toBeEmpty();
    });

    it(description: 'returns the exact response from the Claude client', closure: function (): void
    {
        $mockClient   = $this->createMock(Client::class);
        $mockResponse = $this->createMock(MessageResponse::class);

        $mockClient->expects($this->once())
            ->method(constraint: 'sendMessage')
            ->willReturn(value: $mockResponse);

        $client = new AnthropicClient();
        $client->client(apiKey: $this->testApiKey);

        $reflectionClass = new ReflectionClass(objectOrClass: $client);
        $clientProperty  = $reflectionClass->getProperty(name: 'client');

        $clientProperty->setAccessible(accessible: true);
        $clientProperty->setValue(objectOrValue: $client, value: $mockClient);

        expect(value: $client->sendMessage(parameters: $this->parameters))
            ->toBe(expected: $mockResponse);
    });

    it(description: 'handles chained method calls correctly', closure: function (): void
    {
        AnthropicFacade::fake(
            responses: [fakeAnthropicResponse(content: 'Chained test response')]
        );

        $client = app(abstract: AnthropicClientInterface::class);

        $result = $client
            ->client(apiKey: $this->testApiKey)
            ->sendMessage(parameters: $this->parameters);

        expect(value: $result)
            ->toBeInstanceOf(class: MessageResponse::class);
    });

    it(description: 'handles parameters with model correctly', closure: function (): void
    {
        AnthropicFacade::fake(
            responses: [fakeAnthropicResponse(content: 'Model test response')]
        );

        $client = app(abstract: AnthropicClientInterface::class);
        $client->client(apiKey: $this->testApiKey);

        $parameters = [
            'model'    => 'claude-3-haiku-20240307',
            'messages' => [['role' => 'user', 'content' => 'Hello']],
        ];

        expect(value: $client->sendMessage(parameters: $parameters))
            ->toBeInstanceOf(class: MessageResponse::class);
    });

    it(description: 'handles parameters with max_tokens correctly', closure: function (): void
    {
        AnthropicFacade::fake(
            responses: [fakeAnthropicResponse(content: 'Max tokens test response')]
        );

        $client = app(abstract: AnthropicClientInterface::class);
        $client->client(apiKey: $this->testApiKey);

        $parameters = [
            'model'      => 'claude-3-opus-20240229',
            'messages'   => [['role' => 'user', 'content' => 'Hello']],
            'max_tokens' => 1000,
        ];

        expect(value: $client->sendMessage(parameters: $parameters))
            ->toBeInstanceOf(class: MessageResponse::class);
    });

    it(description: 'handles parameters with temperature correctly', closure: function (): void
    {
        AnthropicFacade::fake(
            responses: [fakeAnthropicResponse(content: 'Temperature test response')]
        );

        $client = app(abstract: AnthropicClientInterface::class);
        $client->client(apiKey: $this->testApiKey);

        $parameters = [
            'model'       => 'claude-3-opus-20240229',
            'messages'    => [['role' => 'user', 'content' => 'Hello']],
            'temperature' => 0.5,
        ];

        expect(value: $client->sendMessage(parameters: $parameters))
            ->toBeInstanceOf(class: MessageResponse::class);
    });

    it(description: 'handles parameters with system_message correctly', closure: function (): void
    {
        AnthropicFacade::fake(
            responses: [fakeAnthropicResponse(content: 'System message test response')]
        );

        $client = app(abstract: AnthropicClientInterface::class);
        $client->client(apiKey: $this->testApiKey);

        $parameters = [
            'model'          => 'claude-3-opus-20240229',
            'messages'       => [['role' => 'user', 'content' => 'Hello']],
            'system_message' => 'You are a helpful assistant.',
        ];

        expect($client->sendMessage(parameters: $parameters))
            ->toBeInstanceOf(class: MessageResponse::class);
    });

    it(description: 'handles parameters with tools correctly', closure: function (): void
    {
        AnthropicFacade::fake(
            responses: [fakeAnthropicResponse(content: 'Tools test response')]
        );

        $client = app(abstract: AnthropicClientInterface::class);
        $client->client(apiKey: $this->testApiKey);

        $parameters = [
            'model'    => 'claude-3-opus-20240229',
            'messages' => [['role' => 'user', 'content' => 'Hello']],
            'tools'    => [$this->tool],
        ];

        expect(value: $client->sendMessage(parameters: $parameters))
            ->toBeInstanceOf(class: MessageResponse::class);
    });

    it(description: 'handles multiple messages correctly', closure: function (): void
    {
        AnthropicFacade::fake(
            responses: [fakeAnthropicResponse(content: 'Multiple messages test response')]
        );

        $client = app(abstract: AnthropicClientInterface::class);
        $client->client(apiKey: $this->testApiKey);

        $parameters = [
            'model'    => 'claude-3-opus-20240229',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello'],
                ['role' => 'assistant', 'content' => 'Hi there! How can I help you?'],
                ['role' => 'user', 'content' => 'Tell me about Laravel'],
            ],
        ];

        expect(value: $client->sendMessage(parameters: $parameters))
            ->toBeInstanceOf(class: MessageResponse::class);
    });

    it(description: 'handles invalid message formats gracefully', closure: function (): void
    {
        AnthropicFacade::fake(
            responses: [fakeAnthropicResponse(content: 'Invalid message test response')]
        );

        $client = app(abstract: AnthropicClientInterface::class);
        $client->client(apiKey: $this->testApiKey);

        $parameters = [
            'model'    => 'claude-3-opus-20240229',
            'messages' => [
                ['role' => 'user', 'content' => 'Valid message'],
                ['role'    => 'assistant'],
                ['content' => 'Missing role'],
                ['role'    => 123, 'content' => 456],
            ],
        ];

        expect(value: $client->sendMessage(parameters: $parameters))
            ->toBeInstanceOf(class: MessageResponse::class);
    });

    it(description: 'handles all parameters together', closure: function (): void
    {
        AnthropicFacade::fake(
            responses: [fakeAnthropicResponse(content: 'All parameters test response')]
        );

        $client = app(abstract: AnthropicClientInterface::class);
        $client->client(apiKey: $this->testApiKey);

        $parameters = [
            'model'          => 'claude-3-opus-20240229',
            'max_tokens'     => 2000,
            'temperature'    => 0.7,
            'system_message' => 'You are a helpful assistant.',
            'messages'       => [
                ['role' => 'user', 'content' => 'Hello'],
                ['role' => 'assistant', 'content' => 'Hi there! How can I help you?'],
                ['role' => 'user', 'content' => 'Tell me about Laravel'],
            ],
            'tools' => [$this->tool],
        ];

        expect(value: $client->sendMessage(parameters: $parameters))
            ->toBeInstanceOf(class: MessageResponse::class);
    });

    it(description: 'throws exception when model is not set', closure: function (): void
    {
        Config::set('blog.post.model');

        expect(value: fn () => (new AnthropicClient)->client(apiKey: 'test-api-key'))
            ->toThrow(exception: BlogPostContentModelNotSetException::class);

        Config::set('blog.post.model', 'claude-3-opus-20240229');
    });

    it(description: 'initializes client when maxTokens config is not set', closure: function (): void
    {
        Config::set('services.anthropic.max_tokens');

        expect(value: (new AnthropicClient)->client(apiKey: 'test-api-key'))
            ->toBeInstanceOf(class: AnthropicClient::class);
    });

    it(description: 'initializes client when maxTokens config is set', closure: function (): void
    {
        Config::set('services.anthropic.max_tokens', '100000');

        expect(value: (new AnthropicClient)->client(apiKey: 'test-api-key'))
            ->toBeInstanceOf(class: AnthropicClient::class);

        Config::set('services.anthropic.max_tokens');
    });

    it(description: 'builds request with model correctly', closure: function (): void
    {
        $parameters = [
            'model'    => 'claude-3-haiku-20240307',
            'messages' => [['role' => 'user', 'content' => 'Hello']],
        ];

        $request         = (new AnthropicClient)->buildRequest(parameters: $parameters);
        $reflectionClass = new ReflectionClass(objectOrClass: $request);
        $modelProperty   = $reflectionClass->getProperty(name: 'model');

        $modelProperty->setAccessible(accessible: true);

        expect(value: $modelProperty->getValue(object: $request))
            ->toBe(expected: 'claude-3-haiku-20240307');
    });

    it(description: 'builds request with max_tokens correctly', closure: function (): void
    {
        $parameters = [
            'model'      => 'claude-3-opus-20240229',
            'messages'   => [['role' => 'user', 'content' => 'Hello']],
            'max_tokens' => 1000,
        ];

        $request           = (new AnthropicClient)->buildRequest(parameters: $parameters);
        $reflectionClass   = new ReflectionClass(objectOrClass: $request);
        $maxTokensProperty = $reflectionClass->getProperty(name: 'maxTokens');
        $maxTokensProperty->setAccessible(accessible: true);

        expect(value: $maxTokensProperty->getValue(object: $request))
            ->toBe(expected: 1000);
    });

    it(description: 'builds request with temperature correctly', closure: function (): void
    {
        $parameters = [
            'model'       => 'claude-3-opus-20240229',
            'messages'    => [['role' => 'user', 'content' => 'Hello']],
            'temperature' => 0.5,
        ];

        $request             = (new AnthropicClient)->buildRequest(parameters: $parameters);
        $reflectionClass     = new ReflectionClass(objectOrClass: $request);
        $temperatureProperty = $reflectionClass->getProperty(name: 'temperature');
        $temperatureProperty->setAccessible(accessible: true);

        expect(value: $temperatureProperty->getValue(object: $request))
            ->toBe(expected: 0.5);
    });

    it(description: 'builds request with system message correctly', closure: function (): void
    {
        $parameters = [
            'model'          => 'claude-3-opus-20240229',
            'messages'       => [['role' => 'user', 'content' => 'Hello']],
            'system_message' => 'You are a helpful assistant.',
        ];

        $request         = (new AnthropicClient)->buildRequest(parameters: $parameters);
        $reflectionClass = new ReflectionClass(objectOrClass: $request);
        $systemProperty  = $reflectionClass->getProperty(name: 'system');
        $systemProperty->setAccessible(accessible: true);

        expect(value: $systemProperty->getValue(object: $request))
            ->not->toBeNull();
    });

    it(description: 'builds request with messages correctly', closure: function (): void
    {
        $parameters = [
            'model'    => 'claude-3-opus-20240229',
            'messages' => [
                ['role' => 'user', 'content' => 'Hello'],
                ['role' => 'assistant', 'content' => 'Hi there! How can I help you?'],
                ['role' => 'user', 'content' => 'Tell me about Laravel'],
            ],
        ];

        $request          = (new AnthropicClient)->buildRequest(parameters: $parameters);
        $reflectionClass  = new ReflectionClass(objectOrClass: $request);
        $messagesProperty = $reflectionClass->getProperty(name: 'messages');
        $messagesProperty->setAccessible(accessible: true);

        expect(value: $messagesProperty->getValue(object: $request))
            ->toBeArray()
            ->toHaveCount(count: 3);
    });

    it(description: 'builds request with tools correctly', closure: function (): void
    {
        $parameters = [
            'model'    => 'claude-3-opus-20240229',
            'messages' => [['role' => 'user', 'content' => 'Hello']],
            'tools'    => [$this->tool],
        ];

        $request         = (new AnthropicClient)->buildRequest(parameters: $parameters);
        $reflectionClass = new ReflectionClass(objectOrClass: $request);
        $toolsProperty   = $reflectionClass->getProperty(name: 'tools');
        $toolsProperty->setAccessible(accessible: true);

        expect(value: $toolsProperty->getValue(object: $request))
            ->toBeArray()
            ->toHaveCount(count: 1);
    });

    it(description: 'validates message formats during request building', closure: function (): void
    {
        $parameters = [
            'model'    => 'claude-3-opus-20240229',
            'messages' => [
                ['role' => 'user', 'content' => 'Valid message'],
                ['role'    => 'assistant'],
                ['content' => 'Missing role'],
                ['role'    => 123, 'content' => 456],
            ],
        ];

        $request          = (new AnthropicClient)->buildRequest(parameters: $parameters);
        $reflectionClass  = new ReflectionClass(objectOrClass: $request);
        $messagesProperty = $reflectionClass->getProperty(name: 'messages');
        $messagesProperty->setAccessible(accessible: true);

        expect(value: $messagesProperty->getValue(object: $request))
            ->toBeArray()
            ->toHaveCount(count: 1);
    });

    it(description: 'safely handles non-array messages in messages array', closure: function (): void
    {
        $parameters = [
            'model'    => 'claude-3-opus-20240229',
            'messages' => [
                ['role' => 'user', 'content' => 'Valid message'],  // Valid message
                'invalid string message',                          // Invalid non-array message
                null,                                              // Another invalid message
                123,                                               // Numeric invalid message
                (object) ['key' => 'value'],                       // Object invalid message
                ['role' => 'user', 'content' => 'Another valid'],  // Another valid message
            ],
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->once())
            ->method(constraint: 'sendMessage')
            ->with($this->callback(callback: function ($request)
            {
                $reflectionClass  = new ReflectionClass(objectOrClass: $request);
                $messagesProperty = $reflectionClass->getProperty(name: 'messages');
                $messagesProperty->setAccessible(accessible: true);

                return count(value: $messagesProperty->getValue(object: $request)) === 2;
            }))->willReturn(value: new MessageResponse(data: []));

        $client = new AnthropicClient();
        $client->client(apiKey: $this->testApiKey);

        $reflectionClass = new ReflectionClass(objectOrClass: $client);
        $clientProperty  = $reflectionClass->getProperty(name: 'client');
        $clientProperty->setAccessible(accessible: true);
        $clientProperty->setValue(objectOrValue: $client, value: $mockClient);

        $client->sendMessage(parameters: $parameters);
    });

    it(description: 'ensures getApiConfig handles model type correctly', closure: function (): void
    {
        Config::set('blog.post.model', 'claude-3-opus-20240229');

        $client = new AnthropicClient();

        $reflectionClass    = new ReflectionClass(objectOrClass: $client);
        $getApiConfigMethod = $reflectionClass->getMethod(name: 'getApiConfig');
        $getApiConfigMethod->setAccessible(accessible: true);

        $config = $getApiConfigMethod->invoke($client, 'test-api-key', '100000');

        $configReflection = new ReflectionClass(objectOrClass: $config);
        $modelProperty    = $configReflection->getProperty(name: 'model');
        $modelProperty->setAccessible(accessible: true);

        expect(value: $modelProperty->getValue(object: $config))
            ->toBe(expected: 'claude-3-opus-20240229')
            ->and(value: $config)
            ->toBeInstanceOf(class: Claude\Claude3Api\Config::class);
    });

    it(description: 'throws exception when model is not set in config', closure: function (): void
    {
        Config::set('blog.post.model', null);

        $client = new AnthropicClient();

        $reflectionClass    = new ReflectionClass(objectOrClass: $client);
        $getApiConfigMethod = $reflectionClass->getMethod(name: 'getApiConfig');
        $getApiConfigMethod->setAccessible(accessible: true);

        expect(value: fn () => $getApiConfigMethod->invoke($client, 'test-api-key', '100000'))
            ->toThrow(exception: BlogPostContentModelNotSetException::class);

        Config::set('blog.post.model', 'claude-3-opus-20240229');
    });
});

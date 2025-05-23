<?php

/** @noinspection JsonEncodingApiUsageInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Exceptions\AiProviders\NoValidContentInResponseException;
use App\Exceptions\AiProviders\ProviderApiKeyNotConfiguredException;
use App\Facades\Anthropic as AnthropicFacade;
use App\Models\BlogIdea;
use App\Models\BlogPrompt;
use App\Services\AiProviders\Anthropic\Anthropic;
use Claude\Claude3Api\Exceptions\ApiException;
use Claude\Claude3Api\Responses\MessageResponse;

beforeEach(closure: function (): void
{
    AnthropicFacade::clearResolvedInstances();

    $this->blogPrompt = BlogPrompt::factory()->create(attributes: [
        'system_prompt' => 'You are a blog post generator.',
        'user_prompt'   => 'Generate a blog post about testing.',
    ]);

    $this->blogIdea  = BlogIdea::factory()->create();
    $this->anthropic = app(abstract: Anthropic::class);

    config()->set('services.anthropic.api_key', 'test-api-key');
    config()->set('services.anthropic.model', 'claude-3-opus-20240229');
});

describe(description: 'Anthropic', tests: function (): void
{
    it(description: 'throws exception when API key is not configured', closure: function (): void
    {
        config()->set('services.anthropic.api_key');

        expect(value: fn () => $this->anthropic->handle(blogIdea: $this->blogIdea))->toThrow(
            exception: ProviderApiKeyNotConfiguredException::class
        );
    });

    it(description: 'handle method returns result from get method', closure: function (): void
    {
        AnthropicFacade::fake(
            responses: [fakeAnthropicResponse(content: json_encode(value: [
                'type'  => 'tool_use',
                'name'  => 'generate_blog_json',
                'input' => [
                    'meta_title'       => 'Mock Title',
                    'meta_description' => 'Mock Description',
                    'post_summary'     => 'Mock Summary',
                    'post_content'     => 'Mock Content',
                    'image_prompt'     => 'Mock Image Prompt',
                ],
            ]))]
        );

        $reflectionClass = new ReflectionClass(objectOrClass: $this->anthropic);

        $systemPromptProperty = $reflectionClass->getProperty(name: 'systemPrompt');
        $systemPromptProperty->setAccessible(accessible: true);
        $systemPromptProperty->setValue(
            objectOrValue: $this->anthropic,
            value: $this->blogPrompt->system_prompt
        );

        $userPromptProperty = $reflectionClass->getProperty(name: 'userPrompt');
        $userPromptProperty->setAccessible(accessible: true);
        $userPromptProperty->setValue(
            objectOrValue: $this->anthropic,
            value: $this->blogPrompt->user_prompt
        );

        $apiKeyProperty = $reflectionClass->getProperty(name: 'apiKey');
        $apiKeyProperty->setAccessible(accessible: true);
        $apiKeyProperty->setValue(
            objectOrValue: $this->anthropic,
            value: config(key: 'services.anthropic.api_key')
        );

        $blogIdeaProperty = $reflectionClass->getProperty(name: 'blogIdea');
        $blogIdeaProperty->setAccessible(accessible: true);
        $blogIdeaProperty->setValue(
            objectOrValue: $this->anthropic,
            value: $this->blogIdea
        );

        expect(value: $this->anthropic->handle(blogIdea: $this->blogIdea))
            ->toBeArray()
            ->toHaveCount(count: 6)
            ->toHaveKeys(keys: [
                'meta_title',
                'meta_description',
                'post_summary',
                'post_content',
                'image_prompt',
                'content_images',
            ]);
    });

    it(description: 'returns valid response from Anthropic API', closure: function (): void
    {
        AnthropicFacade::fake(responses: [fakeAnthropicResponse(content: json_encode(value: [
            'meta_title'       => 'Test Title',
            'meta_description' => 'Test Description',
            'post_summary'     => 'Test Summary',
            'post_content'     => 'Test Content',
            'image_prompt'     => 'Test Image Prompt',
        ]))]);

        $reflectionClass = new ReflectionClass(objectOrClass: $this->anthropic);

        $systemPromptProperty = $reflectionClass->getProperty(name: 'systemPrompt');
        $systemPromptProperty->setAccessible(accessible: true);
        $systemPromptProperty->setValue(
            objectOrValue: $this->anthropic,
            value: $this->blogPrompt->system_prompt
        );

        $userPromptProperty = $reflectionClass->getProperty(name: 'userPrompt');
        $userPromptProperty->setAccessible(accessible: true);
        $userPromptProperty->setValue(
            objectOrValue: $this->anthropic,
            value: $this->blogPrompt->user_prompt
        );

        $apiKeyProperty = $reflectionClass->getProperty(name: 'apiKey');
        $apiKeyProperty->setAccessible(accessible: true);
        $apiKeyProperty->setValue(
            objectOrValue: $this->anthropic,
            value: config(key: 'services.anthropic.api_key')
        );

        $blogIdeaProperty = $reflectionClass->getProperty(name: 'blogIdea');
        $blogIdeaProperty->setAccessible(accessible: true);
        $blogIdeaProperty->setValue(
            objectOrValue: $this->anthropic,
            value: $this->blogIdea
        );

        expect(value: $result = $this->anthropic->get())
            ->toBeArray()
            ->toHaveCount(count: 6)
            ->and(value: $result['meta_title'])
            ->toBe(expected: 'Test Title')
            ->and(value: $result['meta_description'])
            ->toBe(expected: 'Test Description')
            ->and(value: $result['post_summary'])
            ->toBe(expected: 'Test Summary')
            ->and(value: $result['post_content'])
            ->toBe(expected: 'Test Content')
            ->and(value: $result['image_prompt'])
            ->toBe(expected: 'Test Image Prompt')
            ->and(value: $result['content_images'])
            ->toBeArray();
    });

    it(description: 'throws exception when response has no valid content', closure: function (): void
    {
        AnthropicFacade::fake(responses: [new MessageResponse(data: [
            'id'            => 'msg_' . uniqid(prefix: 'test', more_entropy: true),
            'model'         => 'claude-3-opus-20240229',
            'type'          => 'message',
            'role'          => 'assistant',
            'content'       => [['type' => 'text', 'text' => 'Invalid response without tool_use']],
            'stop_reason'   => 'end_turn',
            'stop_sequence' => null,
            'usage'         => [
                'input_tokens'  => 100,
                'output_tokens' => 100,
            ],
        ])]);

        $reflectionClass = new ReflectionClass(objectOrClass: $this->anthropic);

        $systemPromptProperty = $reflectionClass->getProperty(name: 'systemPrompt');
        $systemPromptProperty->setAccessible(accessible: true);
        $systemPromptProperty->setValue(
            objectOrValue: $this->anthropic,
            value: $this->blogPrompt->system_prompt
        );

        $userPromptProperty = $reflectionClass->getProperty(name: 'userPrompt');
        $userPromptProperty->setAccessible(accessible: true);
        $userPromptProperty->setValue(
            objectOrValue: $this->anthropic,
            value: $this->blogPrompt->user_prompt
        );

        $apiKeyProperty = $reflectionClass->getProperty(name: 'apiKey');
        $apiKeyProperty->setAccessible(accessible: true);
        $apiKeyProperty->setValue(
            objectOrValue: $this->anthropic,
            value: config(key: 'services.anthropic.api_key')
        );

        $blogIdeaProperty = $reflectionClass->getProperty(name: 'blogIdea');
        $blogIdeaProperty->setAccessible(accessible: true);
        $blogIdeaProperty->setValue(
            objectOrValue: $this->anthropic,
            value: $this->blogIdea
        );

        expect(value: fn () => $this->anthropic->get())->toThrow(
            exception: NoValidContentInResponseException::class
        );
    });

    it(description: 'throws exception when API returns error', closure: function (): void
    {
        AnthropicFacade::fake(
            responses: [new ApiException(message: 'API Error', code: 500)]
        );

        $reflectionClass = new ReflectionClass(objectOrClass: $this->anthropic);

        $systemPromptProperty = $reflectionClass->getProperty(name: 'systemPrompt');
        $systemPromptProperty->setAccessible(accessible: true);
        $systemPromptProperty->setValue(
            objectOrValue: $this->anthropic,
            value: $this->blogPrompt->system_prompt
        );

        $userPromptProperty = $reflectionClass->getProperty(name: 'userPrompt');
        $userPromptProperty->setAccessible(accessible: true);
        $userPromptProperty->setValue(
            objectOrValue: $this->anthropic,
            value: $this->blogPrompt->user_prompt
        );

        $apiKeyProperty = $reflectionClass->getProperty(name: 'apiKey');
        $apiKeyProperty->setAccessible(accessible: true);
        $apiKeyProperty->setValue(
            objectOrValue: $this->anthropic,
            value: config(key: 'services.anthropic.api_key')
        );

        $blogIdeaProperty = $reflectionClass->getProperty(name: 'blogIdea');
        $blogIdeaProperty->setAccessible(accessible: true);
        $blogIdeaProperty->setValue(
            objectOrValue: $this->anthropic,
            value: $this->blogIdea
        );

        expect(value: fn () => $this->anthropic->get())->toThrow(
            exception       : ApiException::class,
            exceptionMessage: 'API Error'
        );
    });
});

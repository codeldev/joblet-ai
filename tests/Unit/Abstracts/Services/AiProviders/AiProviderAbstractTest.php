<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpIllegalPsrClassPathInspection */
/** @noinspection AutoloadingIssuesInspection */

declare(strict_types=1);

use App\Abstracts\Services\AiProviders\AiProviderAbstract;
use App\Exceptions\AiProviders\ProviderApiKeyNotConfiguredException;
use App\Exceptions\Blog\BlogPostContentModelNotSetException;
use App\Exceptions\Blog\BlogPromptSystemPromptEmptyException;
use App\Exceptions\Blog\BlogPromptSystemPromptMissingException;
use App\Exceptions\Blog\BlogPromptUserPromptEmptyException;
use App\Exceptions\Blog\BlogPromptUserPromptMissingException;
use App\Models\BlogIdea;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\Classes\Unit\Abstract\Services\AiProviders\TestAiProvider;

describe(description: 'AiProviderAbstract', tests: function (): void
{
    beforeEach(closure: function (): void
    {
        $this->blogIdea = BlogIdea::factory()->create(attributes: [
            'topic'        => 'Test Topic',
            'keywords'     => 'test, keywords',
            'focus'        => 'Test Focus',
            'requirements' => 'Test Requirements',
            'additional'   => 'Test Additional',
        ]);

        $this->aiProvider     = new TestAiProvider();
        $this->promptDiskMock = Mockery::mock(args: Filesystem::class);
        $this->errorDiskMock  = Mockery::mock(args: Filesystem::class);

        Storage::partialMock()
            ->shouldReceive(methodNames: 'disk')
            ->with('blog:prompts')
            ->andReturn($this->promptDiskMock);

        Storage::partialMock()
            ->shouldReceive(methodNames: 'disk')
            ->with('blog:errors')
            ->andReturn($this->errorDiskMock);

        $this->errorDiskMock->shouldReceive(methodNames: 'files')
            ->andReturn([])
            ->byDefault();

        $this->errorDiskMock->shouldReceive(methodNames: 'delete')
            ->andReturn(true)
            ->byDefault();
    });

    it('throws exception when model is not configured', function (): void
    {
        Config::set('blog.post.model');
        Config::set('services.openai.api_key', 'test-api-key');

        expect(value: fn () => $this->aiProvider->initSetup(
            apiConfigKey: 'services.openai.api_key',
            blogIdea    : $this->blogIdea
        ))->toThrow(exception: BlogPostContentModelNotSetException::class);
    });

    it('throws exception when API key is not configured', function (): void
    {
        Config::set('blog.post.model', 'gpt-4');
        Config::set('services.openai.api_key');

        expect(value: fn () => $this->aiProvider->initSetup(
            apiConfigKey: 'services.openai.api_key',
            blogIdea    : $this->blogIdea
        ))->toThrow(exception: ProviderApiKeyNotConfiguredException::class);
    });

    it('successfully runs checks with valid configuration', function (): void
    {
        $reflectionClass = new ReflectionClass(objectOrClass: TestAiProvider::class);
        $testProvider    = new TestAiProvider();

        $apiConfigKeyProperty = $reflectionClass->getProperty(name: 'apiConfigKey');
        $apiConfigKeyProperty->setAccessible(accessible: true);
        $apiConfigKeyProperty->setValue(
            objectOrValue: $testProvider,
            value        : 'services.openai.api_key'
        );

        $blogIdeaProperty = $reflectionClass->getProperty(name: 'blogIdea');
        $blogIdeaProperty->setAccessible(accessible: true);
        $blogIdeaProperty->setValue(
            objectOrValue: $testProvider,
            value        : $this->blogIdea
        );

        Config::set('blog.post.model', 'gpt-4');
        Config::set('services.openai.api_key', 'test-api-key');

        $method = $reflectionClass->getMethod(name: 'runChecks');
        $method->setAccessible(accessible: true);
        $method->invoke(object: $testProvider);

        $modelProperty = $reflectionClass->getProperty(name: 'model');
        $modelProperty->setAccessible(accessible: true);

        $apiKeyProperty = $reflectionClass->getProperty(name: 'apiKey');
        $apiKeyProperty->setAccessible(accessible: true);

        expect(value: $modelProperty->getValue(object: $testProvider))
            ->toBe(expected: 'gpt-4')
            ->and(value: $apiKeyProperty->getValue(object: $testProvider))
            ->toBe(expected: 'test-api-key');
    });

    it('handles integer API key by converting to string', function (): void
    {
        $reflectionClass = new ReflectionClass(objectOrClass: TestAiProvider::class);
        $testProvider    = new TestAiProvider();

        $apiConfigKeyProperty = $reflectionClass->getProperty(name: 'apiConfigKey');
        $apiConfigKeyProperty->setAccessible(accessible: true);
        $apiConfigKeyProperty->setValue(
            objectOrValue: $testProvider,
            value        : 'services.openai.api_key'
        );

        $blogIdeaProperty = $reflectionClass->getProperty(name: 'blogIdea');
        $blogIdeaProperty->setAccessible(accessible: true);
        $blogIdeaProperty->setValue(
            objectOrValue: $testProvider,
            value        : $this->blogIdea
        );

        Config::set('blog.post.model', 'gpt-4');
        Config::set('services.openai.api_key', 12345);

        $method = $reflectionClass->getMethod(name: 'runChecks');
        $method->setAccessible(accessible: true);
        $method->invoke(object: $testProvider);

        $apiKeyProperty = $reflectionClass->getProperty(name: 'apiKey');
        $apiKeyProperty->setAccessible(accessible: true);

        expect(value: $apiKeyProperty->getValue(object: $testProvider))
            ->toBe(expected: '12345');
    });

    it('handles integer model by converting to string', function (): void
    {
        $reflectionClass = new ReflectionClass(objectOrClass: TestAiProvider::class);
        $testProvider    = new TestAiProvider();

        $apiConfigKeyProperty = $reflectionClass->getProperty(name: 'apiConfigKey');
        $apiConfigKeyProperty->setAccessible(accessible: true);
        $apiConfigKeyProperty->setValue(
            objectOrValue: $testProvider,
            value        : 'services.openai.api_key'
        );

        $blogIdeaProperty = $reflectionClass->getProperty(name: 'blogIdea');
        $blogIdeaProperty->setAccessible(accessible: true);
        $blogIdeaProperty->setValue(
            objectOrValue: $testProvider,
            value        : $this->blogIdea
        );

        Config::set('blog.post.model', 12345);
        Config::set('services.openai.api_key', 'test-api-key');

        $method = $reflectionClass->getMethod(name: 'runChecks');
        $method->setAccessible(accessible: true);
        $method->invoke(object: $testProvider);

        $modelProperty = $reflectionClass->getProperty(name: 'model');
        $modelProperty->setAccessible(accessible: true);

        expect(value: $modelProperty->getValue(object: $testProvider))
            ->toBe(expected: '12345');
    });

    it('handles boolean API key by converting to string', function (): void
    {
        $reflectionClass = new ReflectionClass(objectOrClass: TestAiProvider::class);
        $testProvider    = new TestAiProvider();

        $apiConfigKeyProperty = $reflectionClass->getProperty(name: 'apiConfigKey');
        $apiConfigKeyProperty->setAccessible(accessible: true);
        $apiConfigKeyProperty->setValue(
            objectOrValue: $testProvider,
            value        : 'services.openai.api_key'
        );

        $blogIdeaProperty = $reflectionClass->getProperty(name: 'blogIdea');
        $blogIdeaProperty->setAccessible(accessible: true);
        $blogIdeaProperty->setValue(
            objectOrValue: $testProvider,
            value        : $this->blogIdea
        );

        Config::set('blog.post.model', 'gpt-4');
        Config::set('services.openai.api_key', true);

        $method = $reflectionClass->getMethod(name: 'runChecks');
        $method->setAccessible(accessible: true);
        $method->invoke(object: $testProvider);

        $apiKeyProperty = $reflectionClass->getProperty(name: 'apiKey');
        $apiKeyProperty->setAccessible(accessible: true);

        expect(value: $apiKeyProperty->getValue(object: $testProvider))
            ->toBe(expected: '1');
    });

    it('handles non-scalar API key by setting empty string', function (): void
    {
        $reflectionClass = new ReflectionClass(objectOrClass: TestAiProvider::class);
        $testProvider    = new TestAiProvider();

        $apiConfigKeyProperty = $reflectionClass->getProperty(name: 'apiConfigKey');
        $apiConfigKeyProperty->setAccessible(accessible: true);
        $apiConfigKeyProperty->setValue(
            objectOrValue: $testProvider,
            value        : 'services.openai.api_key'
        );

        $blogIdeaProperty = $reflectionClass->getProperty(name: 'blogIdea');
        $blogIdeaProperty->setAccessible(accessible: true);
        $blogIdeaProperty->setValue(
            objectOrValue: $testProvider,
            value        : $this->blogIdea
        );

        Config::set('blog.post.model', 'gpt-4');
        Config::set('services.openai.api_key', ['invalid', 'api', 'key']);

        $method = $reflectionClass->getMethod(name: 'runChecks');
        $method->setAccessible(accessible: true);
        $method->invoke(object: $testProvider);

        $apiKeyProperty = $reflectionClass->getProperty(name: 'apiKey');
        $apiKeyProperty->setAccessible(accessible: true);

        expect(value: $apiKeyProperty->getValue(object: $testProvider))
            ->toBe(expected: '');
    });

    it('handles non-scalar model by setting empty string', function (): void
    {
        $reflectionClass = new ReflectionClass(objectOrClass: TestAiProvider::class);
        $testProvider    = new TestAiProvider();

        $apiConfigKeyProperty = $reflectionClass->getProperty(name: 'apiConfigKey');
        $apiConfigKeyProperty->setAccessible(accessible: true);
        $apiConfigKeyProperty->setValue(
            objectOrValue: $testProvider,
            value        : 'services.openai.api_key'
        );

        $blogIdeaProperty = $reflectionClass->getProperty(name: 'blogIdea');
        $blogIdeaProperty->setAccessible(accessible: true);
        $blogIdeaProperty->setValue(
            objectOrValue: $testProvider,
            value        : $this->blogIdea
        );

        Config::set('blog.post.model', ['invalid', 'model', 'value']);
        Config::set('services.openai.api_key', 'test-api-key');

        $method = $reflectionClass->getMethod(name: 'runChecks');
        $method->setAccessible(accessible: true);
        $method->invoke(object: $testProvider);

        $modelProperty = $reflectionClass->getProperty(name: 'model');
        $modelProperty->setAccessible(accessible: true);

        expect(value: $modelProperty->getValue(object: $testProvider))
            ->toBe(expected: '');
    });

    it('throws exception when system prompt file is missing', function (): void
    {
        Config::set('blog.post.model', 'gpt-4');
        Config::set('services.openai.api_key', 'test-api-key');
        Config::set('blog.prompts.system', 'system.md');

        $this->promptDiskMock->shouldReceive('exists')
            ->with('system.md')
            ->andReturn(false);

        expect(value: fn () => $this->aiProvider->initSetup(
            apiConfigKey: 'services.openai.api_key',
            blogIdea    : $this->blogIdea
        ))->toThrow(exception: BlogPromptSystemPromptMissingException::class);
    });

    it('throws exception when system prompt file is empty', function (): void
    {
        Config::set('blog.post.model', 'gpt-4');
        Config::set('services.openai.api_key', 'test-api-key');
        Config::set('blog.prompts.system', 'system.md');

        $this->promptDiskMock->shouldReceive('exists')
            ->with('system.md')
            ->andReturn(true);

        $this->promptDiskMock->shouldReceive('get')
            ->with('system.md')
            ->andReturn('');

        expect(value: fn () => $this->aiProvider->initSetup(
            apiConfigKey: 'services.openai.api_key',
            blogIdea    : $this->blogIdea
        ))->toThrow(exception: BlogPromptSystemPromptEmptyException::class);
    });

    it('throws exception when user prompt file is missing', function (): void
    {
        Config::set('blog.post.model', 'gpt-4');
        Config::set('services.openai.api_key', 'test-api-key');
        Config::set('blog.prompts.system', 'system.md');
        Config::set('blog.prompts.user', 'user.md');

        $this->promptDiskMock->shouldReceive('exists')
            ->with('system.md')
            ->andReturn(true);

        $this->promptDiskMock->shouldReceive('get')
            ->with('system.md')
            ->andReturn('System prompt content');

        $this->promptDiskMock->shouldReceive('exists')
            ->with('user.md')
            ->andReturn(false);

        expect(value: fn () => $this->aiProvider->initSetup(
            apiConfigKey: 'services.openai.api_key',
            blogIdea    : $this->blogIdea
        ))->toThrow(exception: BlogPromptUserPromptMissingException::class);
    });

    it('successfully initializes with valid configuration', function (): void
    {
        Config::set('blog.post.model', 'gpt-4');
        Config::set('services.openai.api_key', 'test-api-key');
        Config::set('blog.prompts.system', 'system.md');
        Config::set('blog.prompts.user', 'user.md');

        $this->errorDiskMock->shouldReceive('files')
            ->andReturn(['old-error.txt']);

        $this->errorDiskMock->shouldReceive('delete')
            ->with('old-error.txt')
            ->andReturn(true);

        $this->promptDiskMock->shouldReceive('exists')
            ->with('system.md')
            ->andReturn(true);

        $this->promptDiskMock->shouldReceive('get')
            ->with('system.md')
            ->andReturn('System prompt content');

        $this->promptDiskMock->shouldReceive('exists')
            ->with('user.md')
            ->andReturn(true);

        $this->promptDiskMock->shouldReceive('get')
            ->with('user.md')
            ->andReturn('User prompt with :topic, :keywords, :focus, :requirements, and :additional');

        $this->aiProvider->initSetup(
            apiConfigKey: 'services.openai.api_key',
            blogIdea    : $this->blogIdea
        );

        expect(value: $this->aiProvider->getSystemPrompt())
            ->toBe(expected: 'System prompt content')
            ->and(value: $this->aiProvider->getUserPrompt())
            ->toContain(needles: 'Test Topic')
            ->toContain(needles: 'test, keywords')
            ->toContain(needles: 'Test Focus')
            ->toContain(needles: 'Test Requirements')
            ->toContain(needles: 'Test Additional');
    });

    it('throws exception when user prompt file is empty', function (): void
    {
        $reflectionClass    = new ReflectionClass(objectOrClass: TestAiProvider::class);
        $promptDiskProperty = $reflectionClass->getProperty(name: 'promptDisk');
        $promptDiskProperty->setAccessible(accessible: true);
        $promptDiskProperty->setValue(
            objectOrValue: $this->aiProvider,
            value        : $this->promptDiskMock
        );

        $this->promptDiskMock->shouldReceive('get')
            ->with('user.md')
            ->andReturn('');

        $reflectionMethod = new ReflectionMethod(
            objectOrMethod: TestAiProvider::class,
            method        : 'getFormattedUserPrompt'
        );

        $reflectionMethod->setAccessible(accessible: true);

        expect(value: fn () => $reflectionMethod->invoke($this->aiProvider, 'user.md'))
            ->toThrow(exception: BlogPromptUserPromptEmptyException::class);
    });

    it('successfully formats user prompt with replacements', function (): void
    {
        $reflectionClass    = new ReflectionClass(objectOrClass: TestAiProvider::class);
        $promptDiskProperty = $reflectionClass->getProperty(name: 'promptDisk');
        $promptDiskProperty->setAccessible(accessible: true);
        $promptDiskProperty->setValue(
            objectOrValue: $this->aiProvider,
            value        : $this->promptDiskMock
        );

        $blogIdeaProperty = $reflectionClass->getProperty(name: 'blogIdea');
        $blogIdeaProperty->setAccessible(accessible: true);
        $blogIdeaProperty->setValue(
            objectOrValue: $this->aiProvider,
            value        : $this->blogIdea
        );

        $this->promptDiskMock->shouldReceive('get')
            ->with('user.md')
            ->andReturn('This is a test with :topic and :keywords');

        $reflectionMethod = new ReflectionMethod(
            objectOrMethod: TestAiProvider::class,
            method        : 'getFormattedUserPrompt'
        );

        $reflectionMethod->setAccessible(accessible: true);

        expect(value: $reflectionMethod->invoke($this->aiProvider, 'user.md'))
            ->toBe(expected: 'This is a test with Test Topic and test, keywords');
    });

    it('stores response content to error disk', function (): void
    {
        $this->errorDiskMock->shouldReceive('put')
            ->withArgs(fn (string $path, string $contents) => mb_strlen($path) === 36 && $contents === 'Test error content')
            ->once();

        $reflectionMethod = new ReflectionMethod(
            objectOrMethod: AiProviderAbstract::class,
            method        : 'storeResponse'
        );

        $reflectionMethod->setAccessible(accessible: true);
        $reflectionMethod->invoke($this->aiProvider, 'Test error content');

        expect(value: true)->toBeTrue();
    });

    it('handles error when storing response fails', function (): void
    {
        $this->errorDiskMock->shouldReceive('put')
            ->andThrow(new Exception(message: 'Failed to write file'));

        $reflectionMethod = new ReflectionMethod(
            objectOrMethod: AiProviderAbstract::class,
            method        : 'storeResponse'
        );

        $reflectionMethod->setAccessible(accessible: true);
        $reflectionMethod->invoke($this->aiProvider, 'Test error content');

        expect(value: true)->toBeTrue();
    });

    it('clears all error files from the error disk', function (): void
    {
        $files = [
            'error1.txt',
            'error2.txt',
        ];

        $this->errorDiskMock->shouldReceive('files')
            ->once()
            ->andReturn($files);

        foreach ($files as $file)
        {
            $this->errorDiskMock->shouldReceive('delete')
                ->with($file)
                ->once()
                ->andReturn(true);
        }

        $reflectionClass = new ReflectionClass(objectOrClass: TestAiProvider::class);
        $method          = $reflectionClass->getMethod(name: 'clearErrorFiles');
        $method->setAccessible(accessible: true);
        $method->invoke(object: $this->aiProvider);

        expect(value: true)->toBeTrue();
    });

    it('calls clearErrorFiles during initialization', function (): void
    {
        Config::set('blog.post.model', 'gpt-4');
        Config::set('services.openai.api_key', 'test-api-key');
        Config::set('blog.prompts.system', 'system.md');
        Config::set('blog.prompts.user', 'user.md');

        $this->errorDiskMock->shouldReceive('files')
            ->once()
            ->andReturn([]);

        $this->promptDiskMock->shouldReceive('exists')
            ->with('system.md')
            ->andReturn(true);

        $this->promptDiskMock->shouldReceive('get')
            ->with('system.md')
            ->andReturn('System prompt content');

        $this->promptDiskMock->shouldReceive('exists')
            ->with('user.md')
            ->andReturn(true);

        $this->promptDiskMock->shouldReceive('get')
            ->with('user.md')
            ->andReturn('User prompt');

        $this->aiProvider->initSetup(
            apiConfigKey: 'services.openai.api_key',
            blogIdea: $this->blogIdea
        );

        expect(value: true)->toBeTrue();
    });

    it('returns the system prompt', function (): void
    {
        $reflectionClass      = new ReflectionClass(objectOrClass: TestAiProvider::class);
        $systemPromptProperty = $reflectionClass->getProperty(name: 'systemPrompt');
        $systemPromptProperty->setAccessible(accessible: true);
        $systemPromptProperty->setValue(
            objectOrValue: $this->aiProvider,
            value        : 'Test system prompt'
        );

        expect(value: $this->aiProvider->getSystemPrompt())
            ->toBe(expected: 'Test system prompt');
    });

    it('returns the user prompt', function (): void
    {
        $reflectionClass    = new ReflectionClass(objectOrClass: TestAiProvider::class);
        $userPromptProperty = $reflectionClass->getProperty(name: 'userPrompt');
        $userPromptProperty->setAccessible(accessible: true);
        $userPromptProperty->setValue(
            objectOrValue: $this->aiProvider,
            value        : 'Test user prompt'
        );

        expect(value: $this->aiProvider->getUserPrompt())
            ->toBe(expected: 'Test user prompt');
    });

    it('sets the prompt disk', function (): void
    {
        $reflectionClass    = new ReflectionClass(objectOrClass: TestAiProvider::class);
        $promptDiskProperty = $reflectionClass->getProperty(name: 'promptDisk');
        $promptDiskProperty->setAccessible(accessible: true);

        $method = $reflectionClass->getMethod(name: 'setPromptDisk');
        $method->setAccessible(accessible: true);
        $method->invoke(object: $this->aiProvider);

        expect(value: $promptDiskProperty->getValue(object: $this->aiProvider))
            ->toBe(expected: $this->promptDiskMock);
    });
});

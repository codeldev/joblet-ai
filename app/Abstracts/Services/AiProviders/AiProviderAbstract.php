<?php

/** @noinspection PhpGetterAndSetterCanBeReplacedWithPropertyHooksInspection */
/** @noinspection NestedTernaryOperatorInspection */

declare(strict_types=1);

namespace App\Abstracts\Services\AiProviders;

use App\Enums\StorageDiskEnum;
use App\Exceptions\AiProviders\ProviderApiKeyNotConfiguredException;
use App\Exceptions\Blog\BlogPostContentModelNotSetException;
use App\Exceptions\Blog\BlogPromptSystemPromptEmptyException;
use App\Exceptions\Blog\BlogPromptSystemPromptMissingException;
use App\Exceptions\Blog\BlogPromptUserPromptEmptyException;
use App\Exceptions\Blog\BlogPromptUserPromptMissingException;
use App\Models\BlogIdea;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Throwable;

abstract class AiProviderAbstract
{
    protected ?string $model = null;

    protected ?string $apiKey = null;

    protected Filesystem $promptDisk;

    protected string $systemPrompt = '';

    protected string $userPrompt = '';

    protected string $apiConfigKey = '';

    protected BlogIdea $blogIdea;

    /** @throws Exception */
    final public function initSetup(string $apiConfigKey, BlogIdea $blogIdea): void
    {
        $this->apiConfigKey = $apiConfigKey;
        $this->blogIdea     = $blogIdea;

        $this->runChecks();
        $this->setPromptDisk();
        $this->clearErrorFiles();
        $this->setSystemPrompt();
        $this->setUserPrompt();
    }

    final public function getUserPrompt(): string
    {
        return $this->userPrompt;
    }

    final public function getSystemPrompt(): string
    {
        return $this->systemPrompt;
    }

    protected function storeResponse(string $content): void
    {
        $file = Str::random(length: 32) . '.txt';
        $disk = StorageDiskEnum::BLOG_ERRORS->disk();

        try
        {
            $disk->put(path: $file, contents: $content);
        }
        catch (Throwable)
        {
        }
    }

    /** @throws Exception */
    private function runChecks(): void
    {
        /** @var mixed $model */
        $model = config(key: 'blog.post.model');

        /** @var mixed $apiKey */
        $apiKey = config(key: $this->apiConfigKey);

        if (! notEmpty(value: $model))
        {
            throwException(BlogPostContentModelNotSetException::class);
        }

        if (! notEmpty(value: $apiKey))
        {
            throwException(ProviderApiKeyNotConfiguredException::class);
        }

        $this->model = is_string(value: $model)
            ? $model
            : (is_scalar(value: $model) ? (string) $model : '');

        $this->apiKey = is_string(value: $apiKey)
            ? $apiKey
            : (is_scalar(value: $apiKey) ? (string) $apiKey : '');
    }

    private function clearErrorFiles(): void
    {
        $disk = StorageDiskEnum::BLOG_ERRORS->disk();

        collect(value: $disk->files())
            ->each(callback: fn ($file) => $disk->delete(paths: $file));
    }

    private function setPromptDisk(): void
    {
        $this->promptDisk = StorageDiskEnum::BLOG_PROMPTS->disk();
    }

    /** @throws Exception */
    private function setSystemPrompt(): void
    {
        /** @var string $promptFile */
        $promptFile = config(
            key    : 'blog.prompts.system',
            default: 'system.md'
        );

        if (! $this->promptDisk->exists(path: $promptFile))
        {
            throwException(BlogPromptSystemPromptMissingException::class);
        }

        $systemPrompt = $this->promptDisk->get($promptFile);

        if (! notEmpty(value: $systemPrompt))
        {
            throwException(BlogPromptSystemPromptEmptyException::class);
        }

        /** @var string $systemPrompt */
        $this->systemPrompt = $systemPrompt;
    }

    /** @throws Exception */
    private function setUserPrompt(): void
    {
        /** @var string $promptFile */
        $promptFile = config(
            key    : 'blog.prompts.user',
            default: 'user.md'
        );

        if (! $this->promptDisk->exists(path: $promptFile))
        {
            throwException(BlogPromptUserPromptMissingException::class);
        }

        $this->userPrompt = $this->getFormattedUserPrompt($promptFile);
    }

    /** @throws Exception */
    private function getFormattedUserPrompt(string $promptFile): string
    {
        /** @var string $promptContent */
        $promptContent = $this->promptDisk->get($promptFile);

        if (! notEmpty(value: $promptContent))
        {
            throwException(BlogPromptUserPromptEmptyException::class);
        }

        return trans(
            key: $promptContent,
            replace: [
                'topic'        => $this->blogIdea->topic,
                'keywords'     => $this->blogIdea->keywords,
                'focus'        => $this->blogIdea->focus,
                'requirements' => $this->blogIdea->requirements,
                'additional'   => $this->blogIdea->additional,
            ]
        );
    }
}

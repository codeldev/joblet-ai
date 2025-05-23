<?php

declare(strict_types=1);

namespace App\Actions\Images;

use App\Contracts\Actions\Images\GenerateActionInterface;
use App\Enums\StorageDiskEnum;
use App\Exceptions\AiProviders\OpenAiApiKeyNotConfiguredException;
use App\Exceptions\Blog\BlogImageNotBase64EncodedException;
use App\Exceptions\Blog\BlogPromptNotFoundDuringImageGenerationException;
use App\Facades\OpenAI;
use Exception;
use OpenAI\Client;

final class GenerateAction implements GenerateActionInterface
{
    private string $imagePrompt = '';

    private string $promptString = '';

    private string $tempPath = '';

    /** @throws Exception */
    public function handle(string $tempPath, string $promptString): void
    {
        $this->loadImagePromptTemplate();

        $this->promptString = $promptString;
        $this->tempPath     = $tempPath;

        try
        {
            $apiKey = $this->getApiKey();

            /** @var Client $client */
            $client = OpenAI::client(apiKey: $apiKey);

            /** @var array<string, mixed> $payload */
            $payload  = $this->buildPayload();
            $response = $client->images()->create(parameters: $payload);

            $this->storeImageFile(base64Image: $response->data[0]->b64_json);
        }
        catch (Exception $exception)
        {
            report(exception: $exception);

            throw $exception;
        }
    }

    /** @throws Exception */
    private function storeImageFile(string $base64Image): void
    {
        try
        {
            /** @var string|false $imageData */
            $imageData = base64_decode(string: $base64Image, strict: true);

            if ($imageData === false)
            {
                throwException(
                    exceptionClass: BlogImageNotBase64EncodedException::class
                );
            }

            /** @var string $imageData */
            StorageDiskEnum::BLOG_IMAGES->disk()->put(
                path    : $this->tempPath,
                contents: $imageData
            );
        }
        catch (Exception $exception)
        {
            report(exception: $exception);

            throw $exception;
        }
    }

    /** @throws Exception */
    private function getApiKey(): string
    {
        /** @var mixed $apiKey */
        $apiKey = config(key: 'services.openai.api_key');

        if (! is_string(value: $apiKey) || ! notEmpty(value: $apiKey))
        {
            throwException(
                exceptionClass: OpenAiApiKeyNotConfiguredException::class
            );
        }

        /** @var string $apiKey */
        return $apiKey;
    }

    /** @return array<string, mixed> */
    private function buildPayload(): array
    {
        return [
            'model'   => $this->getAiImageModel(),
            'prompt'  => $this->buildImagePrompt(),
            'size'    => $this->getRequiredImageDimensions(),
            'quality' => $this->getImageQualitySetting(),
            'n'       => 1,
        ];
    }

    private function buildImagePrompt(): string
    {
        return trans(key: $this->imagePrompt, replace: [
            'prompt' => $this->promptString,
        ]);
    }

    private function getAiImageModel(): string
    {
        $defaultModel = 'gpt-image-1';

        /** @var string|mixed $model */
        $model = config(
            key    : 'blog.image.model',
            default: $defaultModel
        );

        return is_string(value: $model)
            ? $model
            : $defaultModel;
    }

    private function getRequiredImageDimensions(): string
    {
        $defaultSize = '1536x1024';

        /** @var string|mixed $size */
        $size = config(
            key    : 'blog.image.size',
            default: $defaultSize
        );

        return is_string(value: $size)
            ? $size
            : $defaultSize;
    }

    private function getImageQualitySetting(): string
    {
        $defaultQuality = 'medium';

        /** @var string|mixed $quality */
        $quality = config(
            key    : 'blog.image.quality',
            default: $defaultQuality
        );

        return is_string(value: $quality)
            ? $quality
            : $defaultQuality;
    }

    /** @throws Exception */
    private function loadImagePromptTemplate(): void
    {
        $file = $this->getPromptFileName();
        $disk = StorageDiskEnum::BLOG_PROMPTS->disk();

        if (! $disk->exists(path: $file))
        {
            throwException(
                exceptionClass: BlogPromptNotFoundDuringImageGenerationException::class
            );
        }

        $this->imagePrompt = (string) $disk->get($file);

        if (! notEmpty(value: $this->imagePrompt))
        {
            throwException(
                exceptionClass: BlogPromptNotFoundDuringImageGenerationException::class
            );
        }
    }

    private function getPromptFileName(): string
    {
        $defaultFile = 'image.md';

        /** @var string|mixed $file */
        $file = config(
            key    : 'blog.prompts.image',
            default: $defaultFile
        );

        return is_string(value: $file)
            ? $file
            : $defaultFile;
    }
}

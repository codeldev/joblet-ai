<?php

/** @noinspection PhpUnreachableStatementInspection */

/** @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection */

declare(strict_types=1);

namespace App\Services\AiProviders\Anthropic;

use App\Abstracts\Services\AiProviders\AiProviderAbstract;
use App\Contracts\Services\AiProviders\AiProviderInterface;
use App\Contracts\Services\AiProviders\Anthropic\AnthropicInterface;
use App\Exceptions\AiProviders\NoValidContentInResponseException;
use App\Facades\Anthropic as AnthropicFacade;
use App\Models\BlogIdea;
use Claude\Claude3Api\Models\Tool;
use Exception;
use JsonException;
use Throwable;

final class Anthropic extends AiProviderAbstract implements AiProviderInterface, AnthropicInterface
{
    /**
     * @return array<string, array<string, string>|string>
     *
     * @throws Exception|Throwable
     */
    public function handle(BlogIdea $blogIdea): array
    {
        $this->initSetup(
            apiConfigKey: 'services.anthropic.api_key',
            blogIdea    : $blogIdea
        );

        return $this->get();
    }

    /**
     * @return array<string, array<string, string>|string>
     *
     * @throws JsonException|Throwable|Exception
     */
    public function get(): array
    {
        $apiKey = (string) $this->apiKey;
        $client = AnthropicFacade::client(apiKey: $apiKey);

        $response = $client->sendMessage(
            parameters: $this->buildPayload()
        );

        /** @var array<int, mixed> $responseContent */
        $responseContent = $response->getContent();

        /** @var array<string, array<string, string>|string> */
        return $this->extractJsonContent(contents: $responseContent);
    }

    /** @return array<string, mixed> */
    private function buildPayload(): array
    {
        return [
            'model'          => $this->model,
            'max_tokens'     => 100000,
            'temperature'    => 0.7,
            'system_message' => $this->systemPrompt,
            'messages'       => [
                [
                    'role'    => 'user',
                    'content' => $this->userPrompt,
                ],
            ],
            'tools' => [$this->buildJsonResponseTool()],
        ];
    }

    /**
     * @param  array<int, mixed>  $contents
     * @return array<string, array<string, string>|string>
     *
     * @throws JsonException|Exception
     */
    private function extractJsonContent(array $contents): array
    {
        foreach ($contents as $content)
        {
            /** @var mixed $content */
            if (is_array(value: $content) && $this->hasRequiredKeys(content: $content))
            {
                /** @var array<string, mixed> $typedContent */
                $typedContent = $content;

                if ($this->isRequiredResponse(content: $typedContent))
                {
                    return $this->getFormattedResponse(content: $typedContent);
                }
            }
        }

        $this->storeResponse(
            content: json_encode(value: $contents, flags: JSON_THROW_ON_ERROR)
        );

        throwException(
            exceptionClass: NoValidContentInResponseException::class
        );

        // @codeCoverageIgnoreStart
        return []; // Here to appease Stan
        // @codeCoverageIgnoreEnd
    }

    /** @param array<mixed, mixed> $content */
    private function hasRequiredKeys(array $content): bool
    {
        return isset($content['type'], $content['name'], $content['input']);
    }

    /** @param array<string, mixed> $content */
    private function isRequiredResponse(array $content): bool
    {
        /** @var string $type */
        $type = $content['type'];

        /** @var string $name */
        $name = $content['name'];

        /** @var mixed $input */
        $input = $content['input'];

        return $type === 'tool_use'
            && $name === 'generate_blog_json'
            && is_array(value: $input);
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, array<string, string>|string>
     */
    private function getFormattedResponse(array $content): array
    {
        /** @var array<string, mixed> $input */
        $input = $content['input'];

        /** @var string $metaTitle */
        $metaTitle = $input['meta_title'] ?? '';

        /** @var string $metaDescription */
        $metaDescription = $input['meta_description'] ?? '';

        /** @var string $postSummary */
        $postSummary = $input['post_summary'] ?? '';

        /** @var string $postContent */
        $postContent = $input['post_content'] ?? '';

        /** @var string $imagePrompt */
        $imagePrompt = $input['image_prompt'] ?? '';

        /** @var array<string,string> $contentImages */
        $contentImages = $input['content_images'] ?? [];

        return [
            'meta_title'       => $metaTitle,
            'meta_description' => $metaDescription,
            'post_summary'     => $postSummary,
            'post_content'     => $postContent,
            'image_prompt'     => $imagePrompt,
            'content_images'   => $contentImages,
        ];
    }

    private function buildJsonResponseTool(): Tool
    {
        return new Tool(
            name       : 'generate_blog_json',
            description: 'Generate a properly formatted JSON object containing blog content',
            inputSchema: [
                'type'       => 'object',
                'properties' => [
                    'meta_title' => [
                        'type'        => 'string',
                        'description' => 'SEO-optimized title (65-80 characters)',
                    ],
                    'meta_description' => [
                        'type'        => 'string',
                        'description' => 'Compelling description (under 160 characters)',
                    ],
                    'post_summary' => [
                        'type'        => 'string',
                        'description' => '3-4 sentence summary of key points and value',
                    ],
                    'post_content' => [
                        'type'        => 'string',
                        'description' => 'Full markdown blog post content meeting all requirements',
                    ],
                    'image_prompt' => [
                        'type'        => 'string',
                        'description' => 'Detailed, photo-realistic image description',
                    ],
                    'content_images' => [
                        'type'        => 'array',
                        'description' => 'An array of blog content image prompts',
                    ],
                ],
                'required' => [
                    'meta_title',
                    'meta_description',
                    'post_summary',
                    'post_content',
                    'image_prompt',
                    'content_images',
                ],
            ]
        );
    }
}

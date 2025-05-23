<?php

/** @noinspection JsonEncodingApiUsageInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

namespace Tests\Unit\Facades;

use App\Facades\Anthropic;
use Claude\Claude3Api\Responses\MessageResponse;

it('returns default response when no fake responses provided', function (): void
{
    Anthropic::fake();

    $response = Anthropic::sendMessage(parameters: []);
    $content  = $response->getContent()[0]['input'];

    expect(value: $response)
        ->toBeInstanceOf(class: MessageResponse::class)
        ->and(value: $content)
        ->toBeArray()
        ->toHaveKey(key: 'meta_title', value: 'Test Title')
        ->toHaveKey(key: 'meta_description', value: 'Test Description')
        ->toHaveKey(key: 'post_summary', value: 'Test Summary')
        ->toHaveKey(key: 'post_content', value: 'Test Content')
        ->toHaveKey(key: 'image_prompt', value: 'Test Image Prompt');
});

it('returns custom fake response when provided', function (): void
{
    $customContent = json_encode(value: [
        'meta_title'       => 'Custom Title',
        'meta_description' => 'Custom Description',
        'post_summary'     => 'Custom Summary',
        'post_content'     => 'Custom Content',
        'image_prompt'     => 'Custom Image Prompt',
    ]);

    $customResponse = fakeAnthropicResponse(content: $customContent);
    Anthropic::fake(responses: [$customResponse]);

    $response = Anthropic::sendMessage(parameters: []);
    $content  = $response->getContent()[0]['input'];

    expect(value: $response)
        ->toBeInstanceOf(class: MessageResponse::class)
        ->and(value: $content)
        ->toBeArray()
        ->toHaveKey(key: 'meta_title', value: 'Custom Title')
        ->toHaveKey(key: 'meta_description', value: 'Custom Description')
        ->toHaveKey(key: 'post_summary', value: 'Custom Summary')
        ->toHaveKey(key: 'post_content', value: 'Custom Content')
        ->toHaveKey(key: 'image_prompt', value: 'Custom Image Prompt');
});

it('creates fake response with expected structure', function (): void
{
    $content = json_encode(value: [
        'meta_title'       => 'Test Title',
        'meta_description' => 'Test Description',
        'post_summary'     => 'Test Summary',
        'post_content'     => 'Test Content',
        'image_prompt'     => 'Test Image Prompt',
    ]);

    $response = fakeAnthropicResponse(content: $content);
    $content  = $response->getContent()[0];

    expect(value: $response)
        ->toBeInstanceOf(class: MessageResponse::class)
        ->and(value: $response->getId())
        ->toBeString()
        ->toStartWith('msg_test')
        ->and(value: $response->getModel())
        ->toBe(expected: 'claude-3-opus-20240229')
        ->and(value: $response->getStopReason())
        ->toBe(expected: 'end_turn')
        ->and(value: $content)
        ->toBeArray()
        ->toHaveKeys(['type', 'name', 'input'])
        ->and(value: $content['type'])
        ->toBe(expected: 'tool_use')
        ->and(value: $content['name'])
        ->toBe(expected: 'generate_blog_json')
        ->and(value: $content['input'])
        ->toBeArray()
        ->toHaveKeys(keys: [
            'meta_title',
            'meta_description',
            'post_summary',
            'post_content',
            'image_prompt',
        ]);
});

it('handles multiple fake responses in sequence', function (): void
{
    $response1 = fakeAnthropicResponse(content: json_encode(value: [
        'meta_title'       => 'First Title',
        'meta_description' => 'First Description',
        'post_summary'     => 'First Summary',
        'post_content'     => 'First Content',
        'image_prompt'     => 'First Image Prompt',
    ]));

    $response2 = fakeAnthropicResponse(content: json_encode(value: [
        'meta_title'       => 'Second Title',
        'meta_description' => 'Second Description',
        'post_summary'     => 'Second Summary',
        'post_content'     => 'Second Content',
        'image_prompt'     => 'Second Image Prompt',
    ]));

    Anthropic::fake(responses: [$response1, $response2]);

    $firstResponse = Anthropic::sendMessage(parameters: []);
    $firstContent  = $firstResponse->getContent()[0]['input'];

    $secondResponse = Anthropic::sendMessage(parameters: []);
    $secondContent  = $secondResponse->getContent()[0]['input'];

    $thirdResponse = Anthropic::sendMessage(parameters: []);
    $thirdContent  = $thirdResponse->getContent()[0]['input'];

    expect(value: $firstContent)
        ->toBeArray()
        ->toHaveKey(key: 'meta_title', value: 'First Title')
        ->toHaveKey(key: 'meta_description', value: 'First Description')
        ->and(value: $secondContent)
        ->toBeArray()
        ->toHaveKey(key: 'meta_title', value: 'Second Title')
        ->toHaveKey(key: 'meta_description', value: 'Second Description')
        ->and(value: $thirdContent)
        ->toBeArray()
        ->toHaveKey(key: 'meta_title', value: 'First Title')
        ->toHaveKey(key: 'meta_description', value: 'First Description');
});

it('handles null content in fakeAnthropicResponse', function (): void
{
    $response = fakeAnthropicResponse();
    $content  = $response->getContent()[0]['input'];

    expect(value: $response)
        ->toBeInstanceOf(class: MessageResponse::class)
        ->and(value: $content)
        ->toBeArray()
        ->toHaveKey(key: 'meta_title', value: 'Test Title')
        ->toHaveKey(key: 'meta_description', value: 'Test Description')
        ->toHaveKey(key: 'post_summary', value: 'Test Summary')
        ->toHaveKey(key: 'post_content', value: 'Test Content')
        ->toHaveKey(key: 'image_prompt', value: 'Test Image Prompt');
});

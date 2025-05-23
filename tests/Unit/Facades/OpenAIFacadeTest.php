<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Facades\OpenAI;

describe(description: 'OpenAI Facade', tests: function (): void
{
    it('can create a fake instance for testing', function (): void
    {
        $jsonResponse = '{"title":"Test Title","content":"Test Content"}';

        OpenAI::fake(responses: [
            fakeOpenAiResponse(content: $jsonResponse),
        ]);

        $client   = OpenAI::client(apiKey: 'test-api-key');
        $response = $client->chat()->create(parameters: [
            'model'    => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => 'Generate a blog post.'],
            ],
        ]);

        expect(value: $response->choices[0]->message->content)
            ->toBe(expected: $jsonResponse);
    });

    it('can handle multiple responses in sequence', function (): void
    {
        $response1 = '{"title":"First Title","content":"First Content"}';
        $response2 = '{"title":"Second Title","content":"Second Content"}';

        OpenAI::fake(responses: [
            fakeOpenAiResponse(content: $response1),
            fakeOpenAiResponse(content: $response2),
        ]);

        $client = OpenAI::client(apiKey: 'test-api-key');

        $firstResponse  = $client->chat()->create(parameters: []);
        $secondResponse = $client->chat()->create(parameters: []);

        expect(value: $firstResponse->choices[0]->message->content)
            ->toBe(expected: $response1)
            ->and(value: $secondResponse->choices[0]->message->content)
            ->toBe(expected: $response2);
    });

    it('can simulate exceptions', function (): void
    {
        OpenAI::fake(responses: [
            new RuntimeException(message: 'OpenAI API Error'),
        ]);

        $client = OpenAI::client(apiKey: 'test-api-key');

        expect(value: fn () => $client->chat()->create(parameters: []))
            ->toThrow(exception: RuntimeException::class, message: 'OpenAI API Error');
    });

    it('returns default response when no responses are provided', function (): void
    {
        OpenAI::fake();

        $client   = OpenAI::client(apiKey: 'test-api-key');
        $response = $client->chat()->create(parameters: []);

        expect(value: $response->choices[0]->message->content)
            ->toBe(expected: '{"title":"Test Title","content":"Test Content"}');
    });
});

<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Http\Requests\Webhooks\StripeWebhookRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use OpenAI\Responses\Chat\CreateResponse;

pest()
    ->extends(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Arch', 'Unit', 'Console', 'Livewire');

function testUser(array $params = []): User
{
    return ! empty($params)
        ? User::factory()->create(attributes: $params)
        : User::factory()->create();
}

function testUserWithoutEvents(array $params = []): User
{
    return Model::withoutEvents(callback: static fn () => testUser(params: $params));
}

function testEmail(): string
{
    return str(string: fake()->name())
        ->slug()
        ->toString() . '@gmail.com';
}

function fakeOpenAiResponse(?string $content): CreateResponse
{
    return CreateResponse::fake(override: [
        'id'      => 'chatcmpl-' . uniqid(prefix: 'text', more_entropy: true),
        'object'  => 'chat.completion',
        'created' => time(),
        'model'   => 'gpt-3.5-turbo',
        'choices' => [
            [
                'index'   => 0,
                'message' => [
                    'role'    => 'assistant',
                    'content' => $content,
                ],
                'finish_reason' => 'stop',
            ],
        ],
        'usage' => [
            'prompt_tokens'     => $prompt     = fake()->numberBetween(int1: 100, int2: 700),
            'completion_tokens' => $completion = fake()->numberBetween(int1: 100, int2: 700),
            'total_tokens'      => ($prompt + $completion),
        ],
    ]);
}

function makeStripeRequest(array $headers = [], string $content = '{}'): StripeWebhookRequest
{
    $request = StripeWebhookRequest::create(
        uri    : '/',
        method : 'POST',
        content: $content
    );

    foreach ($headers as $key => $value)
    {
        $request->headers->set(key: $key, values: $value);
    }

    return $request;
}

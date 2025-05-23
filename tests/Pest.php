<?php

/** @noinspection JsonEncodingApiUsageInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Http\Requests\Webhooks\StripeWebhookRequest;
use App\Models\User;
use Claude\Claude3Api\Responses\MessageResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use OpenAI\Responses\Chat\CreateResponse;

if (file_exists(filename: __DIR__ . '/../bootstrap/cache/config.php'))
{
    echo "\033[31mERROR: Configuration is cached. This can lead to database wipes during testing.\033[0m\n";
    echo "\033[31mPlease clear the configuration cache before running tests:\033[0m\n";
    echo "\033[33mRun: pa config:clear && pa cache:clear && pa view:clear\033[0m\n";
    exit(1);
}

pest()
    ->extends(classAndTraits: TestCase::class)
    ->use(classAndTraits: RefreshDatabase::class)
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

function fakeOpenAiResponseObject(string $content): object
{
    return (object) [
        'choices' => [(object) ['message' => (object) ['content' => $content]]],
    ];
}

function fakeAnthropicResponse(?string $content = null): MessageResponse
{
    $defaultContent = '{"meta_title":"Test Title","meta_description":"Test Description","post_summary":"Test Summary","post_content":"Test Content","image_prompt":"Test Image Prompt"}';

    $toolUseContent = [
        'type'  => 'tool_use',
        'name'  => 'generate_blog_json',
        'input' => json_decode(json: $content ?? $defaultContent, associative: true),
    ];

    $data = [
        'id'            => 'msg_' . uniqid(prefix: 'test', more_entropy: true),
        'model'         => 'claude-3-opus-20240229',
        'type'          => 'message',
        'role'          => 'assistant',
        'content'       => [$toolUseContent],
        'stop_reason'   => 'end_turn',
        'stop_sequence' => null,
        'usage'         => [
            'input_tokens'  => fake()->numberBetween(int1: 100, int2: 700),
            'output_tokens' => fake()->numberBetween(int1: 100, int2: 700),
        ],
    ];

    return new MessageResponse(data: $data);
}

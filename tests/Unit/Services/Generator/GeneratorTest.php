<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\LetterCreativityEnum;
use App\Enums\MaxTokensEnum;
use App\Services\Generator\Generator;
use Carbon\CarbonImmutable;

beforeEach(closure: function (): void
{
    $this->generatorName       = fake()->name();
    $this->generatorJob        = fake()->jobTitle();
    $this->generatorCompany    = fake()->company();
    $this->generatorManager    = fake()->name();
    $this->generatorLeaveDate  = CarbonImmutable::parse(time: '2025-04-25');
    $this->generatorReason     = 'Career growth';
    $this->generatorExperience = 'Great team';
});

test(description: 'constructor sets default model', closure: function (): void
{
    $result = new Generator(settings: [])->builder();

    expect(value: $result)
        ->toBeArray()
        ->toHaveKey(key: 'model')
        ->and(value: $result['model'])
        ->toBe(expected: 'gpt-3.5-turbo');
});

test(description: 'builder returns complete configuration array', closure: function (): void
{
    $result = new Generator(settings: [
        'name' => $this->generatorName,
    ])->builder();

    expect(value: $result)
        ->toBeArray()
        ->toHaveKeys(keys: [
            'model',
            'messages',
            'max_tokens',
            'temperature',
            'presence_penalty',
            'frequency_penalty',
        ]);
});

test(description: 'builder includes system and user messages', closure: function (): void
{
    $result = new Generator(settings: [
        'name' => $this->generatorName,
    ])->builder();

    expect(value: $result['messages'])
        ->toBeArray()
        ->toHaveCount(count: 2)
        ->and(value: $result['messages'][0])
        ->toHaveKey(key: 'role')
        ->and(value: $result['messages'][0]['role'])
        ->toBe(expected: 'system')
        ->and(value: $result['messages'][1])
        ->toHaveKey(key: 'role')
        ->and(value: $result['messages'][1]['role'])
        ->toBe(expected: 'user');
});

test(description: 'builder includes message content', closure: function (): void
{
    $result = new Generator(settings: [
        'name' => $this->generatorName,
    ])->builder();

    expect(value: $result['messages'])
        ->toBeArray()
        ->and(value: $result['messages'][0])
        ->toHaveKey(key: 'content')
        ->and(value: $result['messages'][0]['content'])
        ->toBeString()
        ->and(value: $result['messages'][1])
        ->toHaveKey(key: 'content')
        ->and(value: $result['messages'][1]['content'])
        ->toBeString()
        ->and(value: $result['messages'][1]['content'])
        ->toContain(needles: $this->generatorName);
});

test(description: 'builder uses precise temperature when specified', closure: function (): void
{
    $result = new Generator(settings: [
        'name'              => $this->generatorName,
        'option_creativity' => LetterCreativityEnum::PRECISE->value,
    ])->builder();

    expect(value: $result)
        ->toHaveKey(key: 'temperature')
        ->and(value: $result['temperature'])
        ->toBe(expected: 0.25);
});

test(description: 'builder uses balanced temperature when specified', closure: function (): void
{
    $result = new Generator(settings: [
        'name'              => $this->generatorName,
        'option_creativity' => LetterCreativityEnum::BALANCED->value,
    ])->builder();

    expect(value: $result)
        ->toHaveKey(key: 'temperature')
        ->and(value: $result['temperature'])
        ->toBe(expected: 0.5);
});

test(description: 'builder uses dynamic temperature when specified', closure: function (): void
{
    $result = new Generator(settings: [
        'name'              => $this->generatorName,
        'option_creativity' => LetterCreativityEnum::DYNAMIC->value,
    ])->builder();

    expect(value: $result)
        ->toHaveKey(key: 'temperature')
        ->and(value: $result['temperature'])
        ->toBe(expected: 0.75);
});

test(description: 'builder uses creative temperature when specified', closure: function (): void
{
    $result = new Generator(settings: [
        'name'              => $this->generatorName,
        'option_creativity' => LetterCreativityEnum::CREATIVE->value,
    ])->builder();

    expect(value: $result)
        ->toHaveKey(key: 'temperature')
        ->and(value: $result['temperature'])
        ->toBe(expected: 0.9);
});

test(description: 'builder uses balanced temperature by default', closure: function (): void
{
    $result = new Generator(settings: [
        'name' => $this->generatorName,
    ])->builder();

    expect(value: $result)
        ->toHaveKey(key: 'temperature')
        ->and(value: $result['temperature'])
        ->toBe(expected: 0.5);
});

test(description: 'builder uses short max tokens when specified', closure: function (): void
{
    $result = new Generator(settings: [
        'name'          => $this->generatorName,
        'option_length' => MaxTokensEnum::SHORT->value,
    ])->builder();

    expect(value: $result)
        ->toHaveKey(key: 'max_tokens')
        ->and(value: $result['max_tokens'])
        ->toBe(expected: 1250);
});

test(description: 'builder uses medium max tokens when specified', closure: function (): void
{
    $result = new Generator(settings: [
        'name'          => $this->generatorName,
        'option_length' => MaxTokensEnum::MEDIUM->value,
    ])->builder();

    expect(value: $result)
        ->toHaveKey(key: 'max_tokens')
        ->and(value: $result['max_tokens'])
        ->toBe(expected: 1850);
});

test(description: 'builder uses long max tokens when specified', closure: function (): void
{
    $result = new Generator(settings: [
        'name'          => $this->generatorName,
        'option_length' => MaxTokensEnum::LONG->value,
    ])->builder();

    expect(value: $result)
        ->toHaveKey(key: 'max_tokens')
        ->and(value: $result['max_tokens'])
        ->toBe(expected: 2500);
});

test(description: 'builder uses medium max tokens by default', closure: function (): void
{
    $result = new Generator(settings: [
        'name' => $this->generatorName,
    ])->builder();

    expect(value: $result)
        ->toHaveKey(key: 'max_tokens')
        ->and(value: $result['max_tokens'])
        ->toBe(expected: 1850);
});

test(description: 'builder includes standard penalty values', closure: function (): void
{
    $result = new Generator(settings: [
        'name' => $this->generatorName,
    ])->builder();

    expect(value: $result)
        ->toHaveKey(key: 'presence_penalty')
        ->and(value: $result['presence_penalty'])
        ->toBe(expected: 0.1)
        ->and(value: $result)
        ->toHaveKey(key: 'frequency_penalty')
        ->and(value: $result['frequency_penalty'])
        ->toBe(expected: 0.1);
});

test(description: 'builder passes all settings to prompts', closure: function (): void
{
    $result = new Generator(settings: [
        'name'                     => $this->generatorName,
        'job'                      => $this->generatorJob,
        'company'                  => $this->generatorCompany,
        'manager'                  => $this->generatorManager,
        'leave_date'               => $this->generatorLeaveDate,
        'leaving_reason'           => true,
        'leaving_reason_text'      => $this->generatorReason,
        'positive_experience'      => true,
        'positive_experience_text' => $this->generatorExperience,
    ])->builder();

    expect(value: $result['messages'])
        ->toBeArray()
        ->and(value: $result['messages'][1]['content'])
        ->toContain(needles: $this->generatorName)
        ->toContain(needles: $this->generatorJob)
        ->toContain(needles: $this->generatorCompany)
        ->toContain(needles: $this->generatorManager)
        ->toContain(needles: $this->generatorReason)
        ->toContain(needles: $this->generatorExperience);
});

<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Enums\LetterCreativityEnum;
use App\Enums\LetterLengthEnum;
use App\Enums\LetterToneEnum;
use App\Livewire\Generator\Index;
use App\Models\Usage;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

beforeEach(closure: function (): void
{
    $this->testUser = testUser();
});

it('can access the generator page as a guest', function (): void
{
    $this->get(uri: route(name: 'generator'))
        ->assertOk();
});

it('can access the generator page as a user', function (): void
{
    $this->actingAs(user: $this->testUser)
        ->get(uri: route(name: 'generator'))
        ->assertOk();
});

it('exposes correct computed properties', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Index::class)
        ->assertSet(name: 'languages', value: LanguageEnum::getLanguages())
        ->assertSet(name: 'dateFormats', value: DateFormatEnum::getFormats())
        ->assertSet(name: 'creativityOptions', value: LetterCreativityEnum::getOptions())
        ->assertSet(name: 'toneOptions', value: LetterToneEnum::getOptions())
        ->assertSet(name: 'lengthOptions', value: LetterLengthEnum::getOptions());
});

it('shows creditsRequired as false for user with credits', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Index::class)
        ->assertSet(name: 'creditsRequired', value: false);
});

it('submits the form as authenticated user with credits', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Index::class)
        ->set('form.name', 'Test Name')
        ->call(method: 'submit')
        ->assertOk();
});

it('shows login required when submitting form as a guest', function (): void
{
    Livewire::test(name: Index::class)
        ->call(method: 'submit')
        ->assertDispatched(event: 'modal-show', name: 'auth-required');
});

it('shows credits required when submitting form as a user with no credits', function (): void
{
    Usage::factory(count: 2)
        ->for(factory: $this->testUser)
        ->create();

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Index::class)
        ->call(method: 'submit')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'generator.insufficient.credits'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('processes the form successfully when submitting with available credits', function (): void
{
    OpenAI::fake(
        responses: [fakeOpenAiResponse(content: fake()->sentence())]
    );

    $formData = [
        'form.name'            => fake()->name(),
        'form.job_title'       => fake()->jobTitle(),
        'form.job_description' => fake()->paragraphs(nb: 5, asText: true),
        'form.company'         => fake()->company(),
        'form.manager'         => fake()->name(),
    ];

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Index::class)
        ->set($formData)
        ->call(method: 'submit')
        ->assertHasNoErrors()
        ->assertDispatched(event: 'reload-credits-panel')
        ->assertDispatched(event: 'generator-credits-check')
        ->assertDispatched(event: 'view-generated-letter');
});

it('shows an error when a problem occurs with letter generation', function (): void
{
    DB::shouldReceive('transaction')->once()->andThrow(
        exception: new RuntimeException(message: 'An error occurred')
    );

    OpenAI::fake(
        responses: [fakeOpenAiResponse(content: fake()->sentence())]
    );

    $formData = [
        'form.name'            => fake()->name(),
        'form.job_title'       => fake()->jobTitle(),
        'form.job_description' => fake()->paragraphs(nb: 5, asText: true),
        'form.company'         => fake()->company(),
        'form.manager'         => fake()->name(),
    ];

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Index::class)
        ->set($formData)
        ->call(method: 'submit')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => 'An error occurred',
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('clears the form', function (): void
{
    $formData = [
        'form.name'            => fake()->name(),
        'form.job_title'       => fake()->jobTitle(),
        'form.job_description' => fake()->paragraphs(nb: 5, asText: true),
        'form.company'         => fake()->company(),
        'form.manager'         => fake()->name(),
    ];

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Index::class)
        ->set($formData)
        ->call(method: 'clearForm')
        ->assertSet(name: 'form.name', value: $this->testUser->name)
        ->assertSet(name: 'form.job_title', value: null)
        ->assertSet(name: 'form.company', value: null)
        ->assertSet(name: 'form.manager', value: null)
        ->assertSet(name: 'form.job_description', value: null);
});

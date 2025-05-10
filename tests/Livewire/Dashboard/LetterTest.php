<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Dashboard\Letter;
use App\Models\Generated;

beforeEach(closure: function (): void
{
    $this->testUser = testUser();
});

it('can render the letter component', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Letter::class)
        ->assertOk()
        ->assertSee(values: 'show-viewable');
});

it('saves asset and dispatches toast when save is called with asset', function (): void
{
    $oldContent = fake()->sentence();
    $newContent = fake()->sentence();
    $generated  = Generated::factory()
        ->for(factory: $this->testUser)
        ->create(attributes: [
            'generated_content_raw' => $oldContent,
        ]);

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Letter::class)
        ->set('generated', $generated)
        ->set('generatedContentHtml', $newContent)
        ->call(method: 'save')
        ->assertSet(name: 'generatedContentText', value: $newContent)
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'letter.result.actions.saved'),
            ],
            dataset : [
                'variant'  => 'success',
            ]
        );
});

it('does not save if asset is null', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Letter::class)
        ->call(method: 'save')
        ->assertSet(name: 'generatedContentText', value: null);
});

it('gives an error if user does not have permission to save changed', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Letter::class)
        ->set(name: 'generated', value: Generated::factory()->create())
        ->call(method: 'save')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'misc.action.disallowed'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('downloads letter with asset', function (): void
{
    $asset = Generated::factory()
        ->for(factory: $this->testUser)
        ->create();

    $fileName = str(string: "{$asset->user->name}-{$asset->id}")
        ->slug()
        ->toString() . '.txt';

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Letter::class)
        ->set('generated', $asset)
        ->call(method: 'download')
        ->assertFileDownloaded(
            filename   : $fileName,
            content    : $asset->generated_content_raw,
            contentType: 'text/plain'
        );

    expect(value: Storage::disk(name: 'local')->exists(path: $fileName))
        ->toBeFalse();
});

it('notifies error when download is called and user does not own the asset', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Letter::class)
        ->set('generated', Generated::factory()->create())
        ->call(method: 'download')
        ->assertNoFileDownloaded()
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'misc.action.disallowed'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('notifies error when download is called without asset', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Letter::class)
        ->call(method: 'download')
        ->assertNoFileDownloaded()
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'generator.download.failed'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('opens a modal when viewing a letter from generation', function (): void
{
    $generated = Generated::factory()
        ->for(factory: $this->testUser)
        ->create();

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Letter::class)
        ->call('viewGeneratedLetter', $generated->id)
        ->assertSet(name: 'generatedContentHtml', value: $generated->generated_content_html)
        ->assertSet(name: 'generatedContentText', value: $generated->generated_content_raw)
        ->assertNoFileDownloaded()
        ->assertDispatched(event: 'modal-show', name: 'show-viewable');
});

it('opens a modal when viewing a letter from dashboard', function (): void
{
    $generated = Generated::factory()
        ->for(factory: $this->testUser)
        ->create();

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Letter::class)
        ->call(method: 'view', generated: $generated)
        ->assertSet(name: 'generatedContentHtml', value: $generated->generated_content_html)
        ->assertSet(name: 'generatedContentText', value: $generated->generated_content_raw)
        ->assertDispatched(event: 'modal-show', name: 'show-viewable');
});

it('gives an error when trying to view someone elses letter from dashboard', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Letter::class)
        ->call(method: 'view', generated: Generated::factory()->create())
        ->assertSet(name: 'generatedContentHtml', value: null)
        ->assertSet(name: 'generatedContentText', value: null)
        ->assertNotDispatched(event: 'modal-show', name: 'show-viewable')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'misc.action.disallowed'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('gives an error when trying to view someone elses letter from generator', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Letter::class)
        ->call(method: 'viewGeneratedLetter', generatedId: Generated::factory()->create()->id)
        ->assertSet(name: 'generatedContentHtml', value: null)
        ->assertSet(name: 'generatedContentText', value: null)
        ->assertNotDispatched(event: 'modal-show', name: 'show-viewable')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'misc.action.disallowed'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('gives an error when trying to view an invalid letter from the generator', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Letter::class)
        ->call(method: 'viewGeneratedLetter', generatedId: Str::uuid())
        ->assertSet(name: 'generatedContentHtml', value: null)
        ->assertSet(name: 'generatedContentText', value: null)
        ->assertNotDispatched(event: 'modal-show', name: 'show-viewable')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'letter.not.found'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('resets a modal when closed', function (): void
{
    $generated = Generated::factory()
        ->for(factory: $this->testUser)
        ->create();

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Letter::class)
        ->call('view', $generated->id)
        ->assertSet(name: 'generatedContentHtml', value: $generated->generated_content_html)
        ->assertSet(name: 'generatedContentText', value: $generated->generated_content_raw)
        ->call(method: 'close')
        ->assertSet(name: 'generated', value: null)
        ->assertSet(name: 'generatedContentHtml', value: null)
        ->assertSet(name: 'generatedContentText', value: null);
});

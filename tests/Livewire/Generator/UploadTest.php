<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Actions\Generator\UploadActionInterface;
use App\Livewire\Generator\Upload;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\UploadedFile;

beforeEach(function (): void
{
    $this->testUser = testUser();
});

it('can mount the component as a guest', function (): void
{
    Livewire::test(Upload::class)
        ->assertSet('fileName', null)
        ->assertSet('processing', false)
        ->assertNotSet('uploadKey', null);
});

it('can mount the component as a user with CV filename', function (): void
{
    $this->testUser->updateQuietly([
        'cv_filename' => 'test-resume.pdf',
    ]);

    Livewire::actingAs($this->testUser)
        ->test(Upload::class)
        ->assertSet('fileName', 'test-resume.pdf')
        ->assertSet('processing', false)
        ->assertNotSet('uploadKey', null);
});

it('validates file upload requirements', function (): void
{
    // Test with non-PDF file
    Livewire::test(Upload::class)
        ->set('file', UploadedFile::fake()->create('document.docx', 100))
        ->assertHasErrors(['file' => 'mimes']);

    // Test with file too large
    Livewire::test(Upload::class)
        ->set('file', UploadedFile::fake()->create('large.pdf', 6000))
        ->assertHasErrors(['file' => 'max']);

    // Test with valid PDF file
    $file = UploadedFile::fake()->create('resume.pdf', 100);

    Livewire::test(Upload::class)
        ->set('file', $file)
        ->assertHasNoErrors()
        ->assertSet('processing', true)
        ->assertDispatched('process-resume-upload');
});

it('shows error notification when no file is provided for processing', function (): void
{
    Livewire::test(Upload::class)
        ->call('processUpload')
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'generator.form.resume.upload.no.file'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        )
        ->assertSet('processing', false);
});

it('generates a new upload key', function (): void
{
    $component        = Livewire::test(name: Upload::class);
    $initialUploadKey = $component->get('uploadKey');
    $reflection       = new ReflectionClass(objectOrClass: $component->instance());
    $method           = $reflection->getMethod(name: 'generateUploadKey');

    $method->setAccessible(accessible: true);
    $method->invoke(object: $component->instance());

    $newUploadKey = $component->get('uploadKey');

    expect(value: $initialUploadKey)
        ->not->toBe(expected: $newUploadKey)
        ->and(value: mb_strlen($newUploadKey))
        ->toBe(expected: 40);
});

it('processes file upload successfully', function (): void
{
    $uploadAction = Mockery::mock(UploadActionInterface::class);

    $uploadAction
        ->shouldReceive(methodNames: 'handle')
        ->once()
        ->withAnyArgs()
        ->andReturnUsing(fn ($file, $success, $failed) => $success('resume.pdf'));

    app()->instance(
        abstract: UploadActionInterface::class,
        instance: $uploadAction
    );

    $name = 'resume.pdf';
    $file = UploadedFile::fake()->create(
        name     : $name,
        kilobytes: 100
    );

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Upload::class)
        ->set('file', $file)
        ->assertHasNoErrors()
        ->call(method: 'processUpload')
        ->assertSet(name: 'processing', value: false)
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'generator.form.resume.upload.success'),
            ],
            dataset : [
                'variant'  => 'success',
            ]
        )
        ->assertSet(name: 'fileName', value: $name)
        ->assertSet(name: 'file', value: null);
});

it('handles upload action failure', function (): void
{
    $uploadAction = Mockery::mock(UploadActionInterface::class);

    $uploadAction
        ->shouldReceive(methodNames: 'handle')
        ->once()
        ->withAnyArgs()
        ->andReturnUsing(fn ($file, $success, $failed) => $failed('Error processing file'));

    app()->instance(
        abstract: UploadActionInterface::class,
        instance: $uploadAction
    );

    $name = 'resume.pdf';
    $file = UploadedFile::fake()->create(
        name     : $name,
        kilobytes: 100
    );

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Upload::class)
        ->set('file', $file)
        ->assertHasNoErrors()
        ->call(method: 'processUpload')
        ->assertSet(name: 'processing', value: false)
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => 'Error processing file',
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('handles binding resolution exception', function (): void
{
    app()->bind(
        abstract: UploadActionInterface::class,
        concrete: fn () => throw new BindingResolutionException(
            message: 'Unable to resolve implementation'
        )
    );

    $name = 'resume.pdf';
    $file = UploadedFile::fake()->create(
        name     : $name,
        kilobytes: 100
    );

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Upload::class)
        ->set('file', $file)
        ->assertHasNoErrors()
        ->call(method: 'processUpload')
        ->assertSet(name: 'processing', value: false)
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => 'Unable to resolve implementation',
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

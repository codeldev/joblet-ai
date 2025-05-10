<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Generator\UploadAction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\PdfToText\Pdf;

beforeEach(function (): void
{
    $this->user       = testUser();
    $this->pdfContent = fake()->paragraphs(nb: 3, asText: true);
    $this->fileName   = 'test-resume.pdf';

    // Create a mock file
    $this->file = Mockery::mock(TemporaryUploadedFile::class);
    $this->file->shouldReceive('getClientOriginalName')->andReturn($this->fileName);
    $this->file->shouldReceive('extension')->andReturn('pdf');

    // Create a mock disk
    $this->disk = Mockery::mock('Illuminate\\Contracts\\Filesystem\\Filesystem');

    // Setup Storage facade mock
    Storage::shouldReceive('disk')->with('local')->andReturn($this->disk);
});

it('successfully processes and stores resume content', function (): void
{
    // Mock Auth to return a valid user
    $this->actingAs(user: $this->user);

    // Setup disk expectations
    $this->disk->shouldReceive('putFileAs')->once()->with('', $this->file, Mockery::any())->andReturn(true);
    $this->disk->shouldReceive('path')->once()->with(Mockery::any())->andReturn('/tmp/test.pdf');
    $this->disk->shouldReceive('exists')->once()->with(Mockery::any())->andReturn(true);
    $this->disk->shouldReceive('delete')->once()->with(Mockery::any())->andReturn(true);

    // Mock the Pdf class
    Mockery::mock('overload:Spatie\\PdfToText\\Pdf')
        ->shouldReceive('getText')
        ->once()
        ->andReturn($this->pdfContent);

    // Prepare for callbacks
    $resultFileName = null;
    $failedCalled   = false;

    // Call the action
    $action = new UploadAction();
    $action->handle(
        file: $this->file,
        success: function ($fileName) use (&$resultFileName): void
        {
            $resultFileName = $fileName;
        },
        failed: function () use (&$failedCalled): void
        {
            $failedCalled = true;
        },
    );

    // Verify the user was updated with the content
    expect(value: $resultFileName)
        ->toBe(expected: $this->fileName)
        ->and(value: $this->user->cv_filename)
        ->toBe(expected: $this->fileName)
        ->and(value: $this->user->cv_content)
        ->toBe(expected: $this->pdfContent)
        ->and(value: $failedCalled)
        ->toBeFalse();
});

it('handles empty PDF content by throwing exception', function (): void
{
    // Mock Auth to return a valid user
    $this->actingAs(user: $this->user);

    // Setup disk expectations
    $this->disk->shouldReceive('putFileAs')->once()->with('', $this->file, Mockery::any())->andReturn(true);
    $this->disk->shouldReceive('path')->once()->with(Mockery::any())->andReturn('/tmp/test.pdf');
    $this->disk->shouldReceive('exists')->once()->with(Mockery::any())->andReturn(true);
    $this->disk->shouldReceive('delete')->once()->with(Mockery::any())->andReturn(true);

    // Mock the Pdf class to return empty content
    Mockery::mock('overload:Spatie\\PdfToText\\Pdf')
        ->shouldReceive('getText')
        ->once()
        ->andReturn('');

    // Prepare for callbacks
    $successCalled = false;
    $resultMessage = null;

    // Call the action
    $action = new UploadAction();
    $action->handle(
        file: $this->file,
        success: function () use (&$successCalled): void
        {
            $successCalled = true;
        },
        failed: function ($message) use (&$resultMessage): void
        {
            $resultMessage = $message;
        },
    );

    // Verify the error message and that success was not called
    expect(value: $successCalled)
        ->toBeFalse()
        ->and(value: $resultMessage)
        ->toBe(expected: trans(key: 'exception.upload.resume.empty'));
});

it('handles exceptions during processing', function (): void
{
    // Mock Auth to return a valid user
    $this->actingAs(user: $this->user);

    $errorMessage = 'Error processing file';

    // Setup disk expectations to throw an exception
    $this->disk->shouldReceive('putFileAs')->once()->with('', $this->file, Mockery::any())
        ->andThrow(new Exception($errorMessage));
    $this->disk->shouldReceive('exists')->once()->with(Mockery::any())->andReturn(true);
    $this->disk->shouldReceive('delete')->once()->with(Mockery::any())->andReturn(true);

    // Prepare for callbacks
    $successCalled = false;
    $resultMessage = null;

    // Call the action
    $action = new UploadAction();
    $action->handle(
        file: $this->file,
        success: function () use (&$successCalled): void
        {
            $successCalled = true;
        },
        failed: function ($message) use (&$resultMessage): void
        {
            $resultMessage = $message;
        },
    );

    // Verify the error message and that success was not called
    expect(value: $successCalled)
        ->toBeFalse()
        ->and(value: $resultMessage)
        ->toBe(expected: $errorMessage);
});

// Note: We don't test guest user or non-User scenarios as this action would never be used in those contexts

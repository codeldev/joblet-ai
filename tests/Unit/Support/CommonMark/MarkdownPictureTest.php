<?php

/** @noinspection HtmlUnknownTarget */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

namespace Tests\Unit\Support\CommonMark;

use App\Support\CommonMark\MarkdownPicture;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

it(description: 'initializes with correct properties', closure: function (): void
{
    $imageData = [
        'alt'   => 'Test Alt Text',
        'files' => [
            ['width' => 480, 'image' => 'test-480.jpg'],
            ['width' => 700, 'image' => 'test-700.jpg'],
            ['width' => 1200, 'image' => 'test-1200.jpg'],
        ],
    ];

    $markdownPicture = new MarkdownPicture(
        imageData: $imageData
    );

    expect(value: $markdownPicture->html)
        ->toBe(expected: '')
        ->and(value: $markdownPicture->disk)
        ->toBeInstanceOf(class: Filesystem::class);
});

it(description: 'converts image data to HTML picture element', closure: function (): void
{
    $imageData = [
        'alt'   => 'Test Alt Text',
        'files' => [
            ['width' => 480, 'image' => 'test-480.jpg'],
            ['width' => 700, 'image' => 'test-700.jpg'],
            ['width' => 1200, 'image' => 'test-1200.jpg'],
        ],
    ];

    Storage::shouldReceive('disk')
        ->andReturn(mock(Filesystem::class));

    $mockDisk = mock(args: Filesystem::class);

    $mockDisk->shouldReceive(methodNames: 'url')
        ->withArgs(argsOrClosure: fn (string $path) => $path === 'test-480.jpg')
        ->andReturn('/storage/blog/test-480.jpg');

    $mockDisk->shouldReceive(methodNames: 'url')
        ->withArgs(argsOrClosure: fn (string $path) => $path === 'test-700.jpg')
        ->andReturn('/storage/blog/test-700.jpg');

    $mockDisk->shouldReceive(methodNames: 'url')
        ->withArgs(argsOrClosure: fn (string $path) => $path === 'test-1200.jpg')
        ->andReturn('/storage/blog/test-1200.jpg');

    $markdownPicture = new MarkdownPicture(
        imageData: $imageData
    );

    $markdownPicture->disk = $mockDisk;

    expect(value: $markdownPicture->convert())
        ->toBeString()
        ->toContain(needles: '<picture>')
        ->toContain(needles: '</picture>')
        ->toContain(needles: '<source media="(max-width: 480px)" srcset="/storage/blog/test-480.jpg">')
        ->toContain(needles: '<source media="(min-width: 481px) and (max-width: 700px)" srcset="/storage/blog/test-700.jpg">')
        ->toContain(needles: '<source media="(min-width: 701px)" srcset="/storage/blog/test-1200.jpg">')
        ->toContain(needles: 'alt="Test Alt Text"')
        ->toContain(needles: 'title="Test Alt Text"');
});

it(description: 'handles single file in image data', closure: function (): void
{
    $imageData = [
        'alt'   => 'Single Image',
        'files' => [
            ['width' => 700, 'image' => 'single.jpg'],
        ],
    ];

    $mockDisk = mock(args: Filesystem::class);

    $mockDisk->shouldReceive(methodNames: 'url')
        ->withArgs(argsOrClosure: fn (string $path) => $path === 'single.jpg')
        ->andReturn('/storage/blog/single.jpg');

    $markdownPicture = new MarkdownPicture(
        imageData: $imageData
    );

    $markdownPicture->disk = $mockDisk;

    expect(value: $markdownPicture->convert())
        ->toBeString()
        ->toContain(needles: '<picture>')
        ->toContain(needles: '</picture>')
        ->toContain(needles: '<source media="(max-width: 700px)" srcset="/storage/blog/single.jpg">')
        ->toContain(needles: '<img')
        ->toContain(needles: 'alt="Single Image"')
        ->toContain(needles: 'title="Single Image"');
});

it(description: 'selects default image with width 700 when available', closure: function (): void
{
    $imageData = [
        'alt'   => 'Multiple Images',
        'files' => [
            ['width' => 480, 'image' => 'small.jpg'],
            ['width' => 700, 'image' => 'medium.jpg'],
            ['width' => 1200, 'image' => 'large.jpg'],
        ],
    ];

    $mockDisk = mock(args: Filesystem::class);

    $mockDisk->shouldReceive(methodNames: 'url')
        ->withArgs(argsOrClosure: fn (string $path) => $path === 'small.jpg')
        ->andReturn('/storage/blog/small.jpg');

    $mockDisk->shouldReceive(methodNames: 'url')
        ->withArgs(argsOrClosure: fn (string $path) => $path === 'medium.jpg')
        ->andReturn('/storage/blog/medium.jpg');

    $mockDisk->shouldReceive(methodNames: 'url')
        ->withArgs(argsOrClosure: fn (string $path) => $path === 'large.jpg')
        ->andReturn('/storage/blog/large.jpg');

    $markdownPicture = new MarkdownPicture(
        imageData: $imageData
    );
    $markdownPicture->disk = $mockDisk;

    expect(value: $markdownPicture->convert())
        ->toBeString()
        ->toContain(needles: 'src="/storage/blog/medium.jpg"');
});

it(description: 'falls back to first image when no 700px width is available', closure: function (): void
{
    $imageData = [
        'alt'   => 'No Medium Image',
        'files' => [
            ['width' => 480, 'image' => 'small.jpg'],
            ['width' => 1200, 'image' => 'large.jpg'],
        ],
    ];

    $mockDisk = mock(args: Filesystem::class);

    $mockDisk->shouldReceive(methodNames: 'url')
        ->withArgs(argsOrClosure: fn (string $path) => $path === 'small.jpg')
        ->andReturn('/storage/blog/small.jpg');

    $mockDisk->shouldReceive(methodNames: 'url')
        ->withArgs(argsOrClosure: fn (string $path) => $path === 'large.jpg')
        ->andReturn('/storage/blog/large.jpg');

    $markdownPicture = new MarkdownPicture(
        imageData: $imageData
    );

    $markdownPicture->disk = $mockDisk;

    expect(value: $markdownPicture->convert())
        ->toBeString()
        ->toContain(needles: 'src="/storage/blog/small.jpg"');
});

it(description: 'handles empty files array gracefully', closure: function (): void
{
    $imageData = [
        'alt'   => 'No Files',
        'files' => [],
    ];

    $markdownPicture = new MarkdownPicture(
        imageData: $imageData
    );

    expect(value: $markdownPicture->convert())
        ->toBeString()
        ->toBe(expected: '<picture></picture>');
});

it(description: 'uses correct disk from StorageDiskEnum', closure: function (): void
{
    $imageData = [
        'alt'   => 'Test Alt Text',
        'files' => [
            ['width' => 700, 'image' => 'test.jpg'],
        ],
    ];

    $mockDisk = mock(args: Filesystem::class);

    Storage::shouldReceive('disk')
        ->withArgs(argsOrClosure: fn (string $name) => $name === 'blog:images')
        ->andReturn(args: $mockDisk);

    $markdownPicture = new MarkdownPicture(
        imageData: $imageData
    );

    expect(value: $markdownPicture->disk)
        ->toBe(expected: $mockDisk);
});

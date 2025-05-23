<?php

/** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\StorageDiskEnum;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

describe(description: 'StorageDiskEnum', tests: function (): void
{
    it('has the correct cases defined', function (): void
    {
        expect(value: StorageDiskEnum::cases())
            ->toHaveCount(count: 5)
            ->and(value: StorageDiskEnum::BLOG_IDEAS->value)
            ->toBe(expected: 'blog:ideas')
            ->and(value: StorageDiskEnum::BLOG_PROMPTS->value)
            ->toBe(expected: 'blog:prompts')
            ->and(value: StorageDiskEnum::BLOG_IMAGES->value)
            ->toBe(expected: 'blog:images')
            ->and(value: StorageDiskEnum::BLOG_ERRORS->value)
            ->toBe(expected: 'blog:errors')
            ->and(value: StorageDiskEnum::BLOG_UNPROCESSABLE->value)
            ->toBe(expected: 'blog:unprocessable');
    });

    it('can be instantiated from value', function (): void
    {
        expect(value: StorageDiskEnum::from(value: 'blog:ideas'))
            ->toBe(expected: StorageDiskEnum::BLOG_IDEAS)
            ->and(value: StorageDiskEnum::from(value: 'blog:prompts'))
            ->toBe(expected: StorageDiskEnum::BLOG_PROMPTS)
            ->and(value: StorageDiskEnum::from(value: 'blog:images'))
            ->toBe(expected: StorageDiskEnum::BLOG_IMAGES)
            ->and(value: StorageDiskEnum::from(value: 'blog:errors'))
            ->toBe(expected: StorageDiskEnum::BLOG_ERRORS)
            ->and(value: StorageDiskEnum::from(value: 'blog:unprocessable'))
            ->toBe(expected: StorageDiskEnum::BLOG_UNPROCESSABLE);
    });

    it('throws exception for invalid value', function (): void
    {
        expect(value: fn () => StorageDiskEnum::from(value: 'invalid-disk'))
            ->toThrow(exception: ValueError::class);
    });

    it('returns a filesystem instance from disk method', function (): void
    {
        Storage::shouldReceive('disk')
            ->once()
            ->with('blog:ideas')
            ->andReturn(mock(Filesystem::class));

        expect(value: StorageDiskEnum::BLOG_IDEAS->disk())
            ->toBeInstanceOf(class: Filesystem::class);
    });
});

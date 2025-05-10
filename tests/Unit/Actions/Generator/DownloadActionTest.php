<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Generator\DownloadAction;
use App\Models\Generated;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

it('downloads an asset content', function (): void
{
    $content   = fake()->sentence();
    $generated = Generated::factory()->create(attributes: [
        'generated_content_raw' => $content,
    ]);

    $fileName  = str(string: "{$generated->user->name}-{$generated->id}")
        ->slug()
        ->toString() . '.txt';

    $response = (new DownloadAction)->handle(
        generated: $generated
    );

    expect(value: $response)
        ->toBeInstanceOf(class: BinaryFileResponse::class)
        ->and(value: $response->headers->get(key: 'Content-Disposition'))
        ->toContain(needles: 'filename=' . $fileName)
        ->and(value: pathinfo(path: $fileName, flags: PATHINFO_EXTENSION))
        ->toBe(expected: 'txt')
        ->and(value: $response->headers->get(key: 'Content-Type'))
        ->toBe(expected: 'text/plain')
        ->and(value: file_get_contents(filename: $response->getFile()->getPathname()))
        ->toBe(expected: $content);

    $reflection = new ReflectionClass(objectOrClass: $response);
    $property   = $reflection->getProperty(name: 'deleteFileAfterSend');
    $property->setAccessible(accessible: true);

    expect(value: $property->getValue(object: $response))
        ->toBeTrue();

    $disk = Storage::disk(name: 'local');
    $disk->delete(paths: $fileName);

    expect(value: $disk->exists(path: $fileName))
        ->toBeFalse();
});

<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Images\ResizeAction;
use App\Contracts\Actions\Images\ResizeActionInterface;
use App\Enums\StorageDiskEnum;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

beforeEach(closure: function (): void
{
    $this->encodedImageString  = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUl';
    $this->encodedImageString .= 'EQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

    Storage::fake(disk: StorageDiskEnum::BLOG_IMAGES->value);

    Config::set('blog.image.conversion.sizes', [400, 700, 1000]);
    Config::set('blog.image.format', 'png');
    Config::set('blog.image.conversion.format', 'webp');
});

afterEach(closure: function (): void
{
    Mockery::close();
});

describe(description: 'ResizeAction', tests: function (): void
{
    it('implements expected Interface', function (): void
    {
        expect(value: new ResizeAction)
            ->toBeInstanceOf(class: ResizeActionInterface::class);
    });

    it('throws exception when source file does not exist', function (): void
    {
        $service    = new ResizeAction;
        $reflection = new ReflectionClass(objectOrClass: $service);
        $property   = $reflection->getProperty(name: 'sourceFile');

        $property->setAccessible(accessible: true);

        $property->setValue(
            objectOrValue: $service,
            value        : 'non-existent-file.png'
        );

        $storageDisk = Storage::fake(disk: StorageDiskEnum::BLOG_IMAGES->value);
        $property    = $reflection->getProperty(name: 'storageDisk');

        $property->setAccessible(accessible: true);
        $property->setValue(
            objectOrValue: $service,
            value        : $storageDisk
        );

        $this->expectException(
            exception: RuntimeException::class
        );

        $method = $reflection->getMethod(name: 'createImageSizes');
        $method->setAccessible(accessible: true);
        $method->invoke(object: $service);
    });

    it('properly sets up properties during initialization (lines 34-63)', function (): void
    {
        $sourceFile   = 'test-image.png';
        $destination  = 'resized';
        $imageContent = base64_decode(string: $this->encodedImageString, strict: true);
        $disk         = Storage::disk(name: StorageDiskEnum::BLOG_IMAGES->value);
        $disk->put(path: $sourceFile, contents: $imageContent);

        $service    = new ResizeAction;
        $reflection = new ReflectionClass(objectOrClass: $service);

        $service->handle(
            sourceFile: $sourceFile,
            destination: $destination,
            storageDisk: StorageDiskEnum::BLOG_IMAGES
        );

        $sourceFileProperty = $reflection->getProperty(name: 'sourceFile');
        $sourceFileProperty->setAccessible(accessible: true);

        $destinationProperty = $reflection->getProperty(name: 'destination');
        $destinationProperty->setAccessible(accessible: true);

        $imageWidthsProperty = $reflection->getProperty(name: 'imageWidths');
        $imageWidthsProperty->setAccessible(accessible: true);

        expect(value: $sourceFileProperty->getValue(object: $service))
            ->toBe(expected: $sourceFile)
            ->and(value: $destinationProperty->getValue(object: $service))
            ->toBe(expected: $destination)
            ->and(value: $imageWidthsProperty->getValue(object: $service))
            ->toBe(expected: [400, 700, 1000]);
    });

    it('uses default sizes when config is invalid (lines 38-57)', function (): void
    {
        $sourceFile   = 'test-image-defaults.png';
        $destination  = 'resized-defaults';
        $imageContent = base64_decode(string: $this->encodedImageString, strict: true);
        $disk         = Storage::disk(name: StorageDiskEnum::BLOG_IMAGES->value);
        $disk->put(path: $sourceFile, contents: $imageContent);

        Config::set('blog.image.conversion.sizes', 'invalid-value');

        $service    = new ResizeAction();
        $reflection = new ReflectionClass(objectOrClass: $service);

        $service->handle(
            sourceFile: $sourceFile,
            destination: $destination,
            storageDisk: StorageDiskEnum::BLOG_IMAGES
        );

        $imageWidthsProperty = $reflection->getProperty(name: 'imageWidths');
        $imageWidthsProperty->setAccessible(accessible: true);
        $defaultSizes = [400, 700, 1000, 1300, 1600, 1920];

        expect(value: $imageWidthsProperty->getValue(object: $service))
            ->toBe(expected: $defaultSizes);
    });

    it('returns correct file information in results (lines 101-103)', function (): void
    {
        $sourceFile   = 'test-image-results.png';
        $destination  = 'resized-results';
        $imageContent = base64_decode(string: $this->encodedImageString, strict: true);
        $disk         = Storage::disk(name: StorageDiskEnum::BLOG_IMAGES->value);

        $disk->put(path: $sourceFile, contents: $imageContent);

        $results = (new ResizeAction)->handle(
            sourceFile: $sourceFile,
            destination: $destination,
            storageDisk: StorageDiskEnum::BLOG_IMAGES
        );

        expect(value: $results)
            ->toBeArray()
            ->toHaveCount(count: 3);

        foreach ($results as $result)
        {
            expect(value: $result)
                ->toBeObject()
                ->toHaveProperties(names: ['width', 'image'])
                ->and(value: $result->width)
                ->toBeInt()
                ->and(value: $result->image)
                ->toBeString()
                ->and(value: $disk->exists(path: $result->image))
                ->toBeTrue();
        }
    });

    it('applies correct image quality based on width (lines 113-121)', function (): void
    {
        $service    = new ResizeAction();
        $reflection = new ReflectionClass(objectOrClass: $service);
        $method     = $reflection->getMethod(name: 'getImageQuality');

        $method->setAccessible(accessible: true);

        expect(value: $method->invoke(object: $service, width: 1600))
            ->toBe(expected: 75)
            ->and(value: $method->invoke(object: $service, width: 1700))
            ->toBe(expected: 75)
            ->and(value: $method->invoke(object: $service, width: 1000))
            ->toBe(expected: 80)
            ->and(value: $method->invoke(object: $service, width: 1500))
            ->toBe(expected: 80)
            ->and(value: $method->invoke(object: $service, width: 700))
            ->toBe(expected: 85)
            ->and(value: $method->invoke(object: $service, width: 999))
            ->toBe(expected: 85)
            ->and(value: $method->invoke(object: $service, width: 400))
            ->toBe(expected: 90)
            ->and(value: $method->invoke(object: $service, width: 699))
            ->toBe(expected: 90);
    });

    it('generates correct converted filename with format (lines 124-131)', function (): void
    {
        $service    = new ResizeAction();
        $reflection = new ReflectionClass(objectOrClass: $service);
        $method     = $reflection->getMethod(name: 'generateConvertedFilename');
        $method->setAccessible(accessible: true);

        $property = $reflection->getProperty(name: 'destination');
        $property->setAccessible(accessible: true);
        $property->setValue(objectOrValue: $service, value: 'test-folder');

        Config::set('blog.image.conversion.format', 'webp');

        $filename = $method->invoke(object: $service, width: 400);
        expect(value: $filename)
            ->toBe(expected: '400w.webp');

        Config::set('blog.image.conversion.format', 'jpg');

        $filename = $method->invoke(object: $service, width: 700);

        expect(value: $filename)
            ->toBe(expected: '700w.jpg');

        Config::set('blog.image.conversion.format');

        $filename = $method->invoke(object: $service, width: 1000);

        expect(value: $filename)
            ->toBe(expected: '1000w.webp');
    });

    it('reports and rethrows exceptions during image resizing (lines 106-110)', function (): void
    {
        $sourceFile          = 'test-image-exception.png';
        $destination         = 'resized-exception';
        $invalidImageContent = 'This is not a valid image file';

        $disk = Storage::disk(name: StorageDiskEnum::BLOG_IMAGES->value);
        $disk->put(path: $sourceFile, contents: $invalidImageContent);

        $this->expectException(RuntimeException::class);

        (new ResizeAction)->handle(
            sourceFile: $sourceFile,
            destination: $destination,
            storageDisk: StorageDiskEnum::BLOG_IMAGES
        );
    });
});

<?php

declare(strict_types=1);

namespace App\Actions\Images;

use App\Contracts\Actions\Images\ResizeActionInterface;
use App\Enums\StorageDiskEnum;
use Illuminate\Contracts\Filesystem\Filesystem;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use RuntimeException;
use Throwable;

final class ResizeAction implements ResizeActionInterface
{
    private Filesystem $storageDisk;

    private string $sourceFile;

    private string $destination;

    /** @var array<int, int> */
    private array $imageWidths = [];

    private ImageManager $imgManager;

    /** @return array<int, object{width: int, image: string}> */
    public function handle(
        string $sourceFile,
        string $destination,
        StorageDiskEnum $storageDisk
    ): array {
        $this->sourceFile  = $sourceFile;
        $this->destination = $destination;
        $this->storageDisk = $storageDisk->disk();

        return $this
            ->setupForResizing()
            ->createImageSizes();
    }

    private function setupForResizing(): self
    {
        /** @var array<int, int> $defaultSizes */
        $defaultSizes = [400, 700, 1000, 1300, 1600, 1920];

        /** @var array<int, int>|mixed $configSizes */
        $configSizes = config(
            key    : 'blog.image.conversion.sizes',
            default: $defaultSizes
        );

        /** @var array<int, int> $mappedSizes */
        $mappedSizes = is_array(value: $configSizes) ? array_map(
            callback: static fn ($size): int => is_numeric(value: $size) ? (int) $size : 0,
            array: $configSizes
        ) : $defaultSizes;

        $this->imageWidths = $mappedSizes;
        $this->imgManager  = new ImageManager(driver: new Driver);

        return $this;
    }

    /** @return array<int, object{width: int, image: string}> */
    private function createImageSizes(): array
    {
        if (! $this->storageDisk->exists(path: $this->sourceFile))
        {
            throw new RuntimeException(
                message: "Image {$this->sourceFile} does not exist."
            );
        }

        /** @var array<int, object{width: int, image: string}> $imageFiles */
        $imageFiles = collect(value: $this->imageWidths)->map(
            callback: fn ($width): object => $this->resizeImage(width: $width)
        )->toArray();

        return $imageFiles;
    }

    /**
     * @return object{width: int, image: string}
     *
     * @throws Throwable
     */
    private function resizeImage(int $width): object
    {
        try
        {
            $fileName = $this->generateConvertedFilename(width: $width);
            $filePath = $this->destination . $fileName;

            $image    = $this->imgManager->read(input: $this->storageDisk->path(path: $this->sourceFile));
            $image->scale(width: $width);
            $image->toWebp(quality: $this->getImageQuality(width: $width));
            $image->save(path: $this->storageDisk->path(path: $filePath));

            return (object) [
                'width' => $width,
                'image' => $filePath,
            ];
        }
        catch (Throwable $exception)
        {
            report(exception: $exception);

            throw $exception;
        }
    }

    private function getImageQuality(int $width): int
    {
        return match (true)
        {
            $width >= 1600 => 75,
            $width >= 1000 => 80,
            $width >= 700  => 85,
            default        => 90,
        };
    }

    private function generateConvertedFilename(int $width): string
    {
        /** @var string|mixed $format */
        $format = config(
            key    : 'blog.image.conversion.format',
            default: 'webp'
        );

        $safeFormat = is_string(value: $format)
            ? $format
            : 'webp';

        return "{$width}w.{$safeFormat}";
    }
}

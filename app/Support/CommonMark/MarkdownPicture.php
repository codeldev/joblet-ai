<?php

declare(strict_types=1);

namespace App\Support\CommonMark;

use App\Enums\StorageDiskEnum;
use Illuminate\Contracts\Filesystem\Filesystem;

final class MarkdownPicture
{
    public string $html = '';

    public Filesystem $disk;

    private readonly string $alt;

    /** @var array<int, array{width: int, image: string}> */
    private array $files;

    private readonly int $total;

    /**
     * @param  array{alt: string, files: array<int, array{width: int, image: string}>}  $imageData
     */
    public function __construct(private readonly array $imageData)
    {
        $this->alt   = (string) $this->imageData['alt'];
        $this->disk  = StorageDiskEnum::BLOG_IMAGES->disk();
        $this->files = (array) $this->imageData['files'];
        $this->total = count(value: $this->files);
    }

    public function convert(): string
    {
        return $this
            ->start()
            ->sources()
            ->addImage()
            ->finish();
    }

    private function sources(): self
    {
        foreach ($this->files as $index => $file)
        {
            $addSrc = $index < $this->total - 1;

            match (true)
            {
                ($index === 0) => $this->addFirstSource(file: $file),
                ($addSrc)      => $this->addSource(file: $file, index: $index),
                default        => $this->addLastSource(file: $file, index: $index),
            };
        }

        return $this;
    }

    /**
     * @param  array{width: int, image: string}  $file
     */
    private function addFirstSource(array $file): void
    {
        $img   = $this->disk->url(path: (string) $file['image']);
        $width = (int) $file['width'];

        $this->html .= '<source media="(max-width: ' . $width . 'px)" srcset="' . $img . '">';
    }

    /**
     * @param  array{width: int, image: string}  $file
     */
    private function addLastSource(array $file, int $index): void
    {
        $prev = $this->getPixelWidth(index: $index);
        $img  = $this->disk->url(path: (string) $file['image']);

        $this->html .= '<source media="(min-width: ' . $prev . 'px)" srcset="' . $img . '">';
    }

    /**
     * @param  array{width: int, image: string}  $file
     */
    private function addSource(array $file, int $index): void
    {
        $min   = $this->getPixelWidth(index: $index);
        $img   = $this->disk->url(path: (string) $file['image']);
        $width = (int) $file['width'];

        $this->html .= '<source media="(min-width: ' . $min . 'px) and (max-width: ' . $width . 'px)" srcset="' . $img . '">';
    }

    private function getPixelWidth(int $index): int
    {
        return (int) $this->files[$index - 1]['width'] + 1;
    }

    private function addImage(): self
    {
        $fileList = collect(value: $this->files);
        $default  = $fileList->firstWhere(key: 'width', operator: 700) ?? $fileList->first();

        if ($default === null)
        {
            return $this;
        }

        $img = $this->disk->url(path: (string) $default['image']);

        $this->html .= '<img class="w-full object-cover rounded-xl overflow-hidden dark:border border-zinc-700/60 dark:p-1.5" loading="lazy" alt="' . $this->alt . '" title="' . $this->alt . '" src="' . $img . '">';

        return $this;
    }

    private function start(): self
    {
        $this->html = '<picture>';

        return $this;
    }

    private function finish(): string
    {
        $this->html .= '</picture>';

        return $this->html;
    }
}

<?php

declare(strict_types=1);

namespace App\Contracts\Services\Invoice;

use App\Enums\PageSizeEnum;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface PdfServiceInterface
{
    public function generate(): void;

    public function hasErrors(): bool;

    /** @return array<int, string> */
    public function getErrors(): array;

    public function outputToBrowser(): BinaryFileResponse;

    public function download(): BinaryFileResponse;

    public function setPageSize(PageSizeEnum $size): self;

    public function setPageMargins(
        float $top,
        float $right,
        float $bottom,
        float $left,
        string $unit = 'mm',
    ): self;

    public function setPageView(string $view): self;

    /** @param array<string, mixed> $data */
    public function setPdfData(array $data): self;

    public function createTempFileName(): self;

    public function setFilename(string $fileName): self;
}

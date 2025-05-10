<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace App\Services\Invoice;

use App\Contracts\Services\Invoice\PdfServiceInterface;
use App\Enums\PageSizeEnum;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

final class PdfService implements PdfServiceInterface
{
    public Filesystem $disk;

    public string $pageSize;

    public float $marginTop    = 15.0;

    public float $marginBottom = 15.0;

    public float $marginLeft   = 15.0;

    public float $marginRight  = 15.0;

    public string $marginUnit  = 'mm';

    public string $pageView    = 'pdf.invoice';

    public string $fileName    = 'invoice.pdf';

    /** @var array<string, mixed> */
    public array $pdfData      = [];

    /** @var array<int, string> */
    public array $errors       = [];

    public function __construct()
    {
        $this->disk = Storage::disk(name: 'local');
    }

    public function generate(): void
    {
        $html = $this->getHtml();

        if (notEmpty(value: $this->errors))
        {
            return;
        }

        try
        {
            Browsershot::html(html: $html)
                ->showBackground()
                ->noSandbox()
                ->margins(
                    top   : $this->marginTop,
                    right : $this->marginRight,
                    bottom: $this->marginBottom,
                    left  : $this->marginLeft,
                    unit  : $this->marginUnit
                )
                ->format(format: $this->pageSize)
                ->save(targetPath: $this->getStoragePath());
        }
        catch (CouldNotTakeBrowsershot $e)
        {
            $this->errors[] = $e->getMessage();
        }
    }

    public function hasErrors(): bool
    {
        return count(value: $this->errors) > 0;
    }

    /** @return array<int, string> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function outputToBrowser(): BinaryFileResponse
    {
        return response()->file(
            file   : $this->disk->path(path: $this->fileName),
            headers: $this->outputToBrowserHeaders()
        );
    }

    public function download(): BinaryFileResponse
    {
        return response()
            ->download(file: $this->disk->path(path: $this->fileName))
            ->deleteFileAfterSend();
    }

    public function setPageSize(PageSizeEnum $size): self
    {
        $this->pageSize = $size->value;

        return $this;
    }

    public function setPageMargins(
        float $top,
        float $right,
        float $bottom,
        float $left,
        string $unit = 'mm',
    ): self {
        $this->marginTop    = $top;
        $this->marginBottom = $bottom;
        $this->marginLeft   = $left;
        $this->marginRight  = $right;
        $this->marginUnit   = Str::of(string: $unit)
            ->lower()
            ->toString();

        return $this;
    }

    public function setPageView(string $view): self
    {
        $this->pageView = Str::of(string: $view)
            ->lower()
            ->toString();

        return $this;
    }

    /** @param array<string, mixed> $data */
    public function setPdfData(array $data): self
    {
        $this->pdfData = $data;

        return $this;
    }

    public function createTempFileName(): self
    {
        $randomString = Str::random(length: 40);
        $timestamp    = (string) time();

        return $this->setFilename(
            fileName: $randomString . '-' . $timestamp
        );
    }

    public function setFilename(string $fileName): self
    {
        $fileName = Str::of($fileName)
            ->lower()
            ->replace(search: '.pdf', replace: '')
            ->slug()
            ->toString();

        $this->fileName = $fileName . '.pdf';

        return $this;
    }

    /** @return array<string, string> */
    private function outputToBrowserHeaders(): array
    {
        return [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $this->fileName . '"',
        ];
    }

    private function getHtml(): string
    {
        try
        {
            return view(
                view: $this->pageView,
                data: $this->pdfData
            )->render();
        }
        catch (Throwable $e)
        {
            report(exception: $e);

            $this->errors[] = $e->getMessage();

            return '';
        }
    }

    private function getStoragePath(): string
    {
        return $this->disk->path(path: $this->fileName);
    }
}

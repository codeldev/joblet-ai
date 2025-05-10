<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\Invoice;

use AllowDynamicProperties;
use App\Contracts\Services\Invoice\PdfServiceInterface;
use App\Enums\PageSizeEnum;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[AllowDynamicProperties]
final class TestPdfService implements PdfServiceInterface
{
    public string $pageSize = 'letter';

    public float $marginTop = 15.0;

    public float $marginRight = 15.0;

    public float $marginBottom = 15.0;

    public float $marginLeft = 15.0;

    public string $marginUnit = 'mm';

    public string $pageView = 'pdf.invoice';

    public string $fileName = 'invoice.pdf';

    public array $pdfData = [];

    public array $errors = [];

    public bool $pdfGenerated = false;

    public ?BinaryFileResponse $mockResponse = null;

    public bool $shouldThrowException = false;

    public bool $simulateHasErrorsButEmptyArray = false;

    public function __construct(?BinaryFileResponse $mockResponse = null)
    {
        $this->mockResponse = $mockResponse;
    }

    public function setPageSize(PageSizeEnum $size): self
    {
        $this->pageSize = $size->value;

        return $this;
    }

    /** @throws Exception */
    public function generate(): void
    {
        if ($this->shouldThrowException)
        {
            throw new RuntimeException(message: 'Test exception');
        }

        $this->pdfGenerated = true;
    }

    public function hasErrors(): bool
    {
        if ($this->simulateHasErrorsButEmptyArray)
        {
            return true;
        }

        return count(value: $this->errors) > 0;
    }

    /** @return array<int, string> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function outputToBrowser(): BinaryFileResponse
    {
        if ($this->mockResponse)
        {
            return $this->mockResponse;
        }

        // Create a temporary test file
        $tempFile = storage_path('app/' . $this->fileName);
        file_put_contents($tempFile, 'Test PDF content');

        return response()->file($tempFile, ['Content-Type' => 'application/pdf']);
    }

    public function download(): BinaryFileResponse
    {
        if ($this->mockResponse)
        {
            return $this->mockResponse;
        }

        // Create a temporary test file
        $tempFile = storage_path('app/' . $this->fileName);
        file_put_contents($tempFile, 'Test PDF content');

        return response()->download($tempFile, $this->fileName, [], 'inline')->deleteFileAfterSend();
    }

    public function setPageMargins(float $top, float $right, float $bottom, float $left, string $unit = 'mm'): self
    {
        $this->marginTop    = $top;
        $this->marginRight  = $right;
        $this->marginBottom = $bottom;
        $this->marginLeft   = $left;
        $this->marginUnit   = mb_strtolower(string: $unit);

        return $this;
    }

    public function setPageView(string $view): self
    {
        $this->pageView = mb_strtolower(string: $view);

        return $this;
    }

    public function setPdfData(array $data): self
    {
        $this->pdfData = $data;

        return $this;
    }

    public function createTempFileName(): self
    {
        $alphabetStr  = str_shuffle(string: 'abcdefghijklmnopqrstuvwxyz0123456789');
        $randomString = mb_substr(string: $alphabetStr, start: 0, length: 40);
        $timestamp    = time();

        $this->fileName = $randomString . '-' . $timestamp . '.pdf';

        return $this;
    }

    public function setFilename(string $fileName): self
    {
        $fileName = mb_strtolower(string: $fileName);
        $fileName = str_replace(search: '.pdf', replace: '', subject: $fileName);
        $fileName = preg_replace(pattern: '/[^a-z0-9\-]/', replacement: '-', subject: $fileName);

        $this->fileName = $fileName . '.pdf';

        return $this;
    }

    // Methods for testing specific scenarios

    public function simulateHtmlRenderingError(): void
    {
        $this->errors[] = 'View not found';
    }

    public function simulateBrowserShotError(): void
    {
        $this->errors[] = 'Failed to generate PDF';
    }
}

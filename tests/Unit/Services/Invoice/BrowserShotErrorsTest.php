<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Services\Invoice\PdfServiceInterface;
use App\Enums\PageSizeEnum;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Invoice\InvoiceDataService;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;

beforeEach(function (): void
{
    $this->user = testUser();

    $this->order = Order::factory()
        ->for($this->user)
        ->create();

    $this->payment = Payment::factory()
        ->for($this->order)
        ->for($this->user)
        ->create();

    $this->pdfService  = app()->make(abstract: PdfServiceInterface::class);
    $this->dataService = app()->make(abstract: InvoiceDataService::class, parameters: [
        'order' => $this->order,
    ]);

    $fileName = collect(value: [
        config(key: 'app.name', default: ''),
        $this->order->user->name                        ?? 'unknown',
        $this->order->payment->formatted_invoice_number ?? Str::random(length: 8),
        time(),
    ])->implode(value: ' ');

    $this->fileName = str(string: $fileName)
        ->slug()
        ->toString();
});

test(description: 'browser shot throws and returns an error', closure: function (): void
{
    $errorMessage    = 'Failed to generate PDF';
    $mockBrowserShot = Mockery::mock(args: 'alias:' . Browsershot::class);

    $mockBrowserShot->shouldReceive('html')
        ->once()
        ->andReturnSelf();
    $mockBrowserShot->shouldReceive('showBackground')
        ->once()
        ->andReturnSelf();
    $mockBrowserShot->shouldReceive('noSandbox')
        ->once()
        ->andReturnSelf();
    $mockBrowserShot->shouldReceive('margins')
        ->once()
        ->andReturnSelf();
    $mockBrowserShot->shouldReceive('format')
        ->once()
        ->andReturnSelf();
    $mockBrowserShot->shouldReceive('save')
        ->once()
        ->andThrow(new CouldNotTakeBrowsershot(message: $errorMessage));

    $this->pdfService
        ->setPageMargins(top: 0, right: 0, bottom: 0, left: 0)
        ->setPageSize(size: PageSizeEnum::A4)
        ->setPageView(view: 'pdf.invoice')
        ->setPdfData(data: $this->dataService->build())
        ->setFilename(fileName: $this->fileName);

    $this->pdfService->generate();

    expect(value: $this->pdfService->hasErrors())
        ->toBeTrue()
        ->and(value: $this->pdfService->errors)
        ->toHaveCount(count: 1)
        ->and(value: $this->pdfService->errors[0])
        ->toBe(expected: $errorMessage);
})->group('browser-shot');

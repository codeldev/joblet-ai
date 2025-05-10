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
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

beforeEach(function (): void
{
    Artisan::call(command: 'view:clear');
    Artisan::call(command: 'config:clear');
    Artisan::call(command: 'cache:clear');

    Carbon::setTestNow(now());

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
        now()->timestamp,
    ])->implode(value: ' ');

    $this->fileName = str(string: $fileName)
        ->slug()
        ->toString();
});

afterEach(closure: function (): void
{
    Carbon::setTestNow();
    Mockery::close();
});

it(description: 'outputs a pdf to the browser', closure: function (): void
{
    $pdfName  = $this->fileName . '.pdf';
    $filePath = storage_path(path: 'app/private/' . $pdfName);

    Storage::shouldReceive('path')
        ->with(args: $pdfName)
        ->andReturn(args: $filePath);

    $mockResponse = Mockery::mock(BinaryFileResponse::class);
    $mockHeaders  = [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $pdfName . '"',
    ];

    Response::shouldReceive('file')
        ->once()
        ->with($filePath, $mockHeaders)
        ->andReturn($mockResponse);

    $this->pdfService
        ->setPageMargins(top: 0, right: 0, bottom: 0, left: 0)
        ->setPageSize(size: PageSizeEnum::A4)
        ->setPageView(view: 'pdf.invoice')
        ->setPdfData(data: $this->dataService->build())
        ->setFilename(fileName: $this->fileName)
        ->generate();

    expect(value: $this->pdfService->outputToBrowser())
        ->toBe(expected: $mockResponse);

    @unlink(filename: $filePath);
});

it(description: 'downloads a pdf file', closure: function (): void
{
    $pdfName  = $this->fileName . '.pdf';
    $filePath = storage_path(path: 'app/private/' . $pdfName);

    Storage::shouldReceive('path')
        ->with(args: $pdfName)
        ->andReturn(args: $filePath);

    $mockResponse = Mockery::mock(args: BinaryFileResponse::class);
    $mockBuilder  = Mockery::mock('Illuminate\Http\ResponseBuilder');

    Response::shouldReceive('download')
        ->once()
        ->with($filePath)
        ->andReturn($mockBuilder);

    $mockBuilder->shouldReceive(methodNames: 'deleteFileAfterSend')
        ->once()
        ->withNoArgs()
        ->andReturn($mockResponse);

    $this->pdfService
        ->setPageMargins(top: 0, right: 0, bottom: 0, left: 0)
        ->setPageSize(size: PageSizeEnum::A4)
        ->setPageView(view: 'pdf.invoice')
        ->setPdfData(data: $this->dataService->build())
        ->setFilename(fileName: $this->fileName)
        ->generate();

    expect(value: $this->pdfService->download())
        ->toBe(expected: $mockResponse);

    @unlink(filename: $filePath);
});

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
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    Carbon::setTestNow(now());

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

    try
    {
        $files = array_filter(
            array   : Storage::disk(name: 'local')->files(),
            callback: fn (string $file): bool => str_ends_with(haystack: $file, needle: '.pdf')
        );

        if (count(value: $files) > 0)
        {
            Storage::disk(name: 'local')->delete(paths: $files);
        }
    }
    catch (Exception)
    {
    }

    Mockery::close();
});

it(description: 'initializes with default values', closure: function (): void
{
    expect(value: $this->pdfService->marginTop)
        ->toBe(expected: 15.0)
        ->and(value: $this->pdfService->marginBottom)
        ->toBe(expected: 15.0)
        ->and(value: $this->pdfService->marginLeft)
        ->toBe(expected: 15.0)
        ->and(value: $this->pdfService->marginRight)
        ->toBe(expected: 15.0)
        ->and(value: $this->pdfService->marginUnit)
        ->toBe(expected: 'mm')
        ->and(value: $this->pdfService->pageView)
        ->toBe(expected: 'pdf.invoice')
        ->and(value: $this->pdfService->fileName)
        ->toBe(expected: 'invoice.pdf')
        ->and(value: $this->pdfService->pdfData)
        ->toBeArray()
        ->toBeEmpty()
        ->and(value: $this->pdfService->errors)
        ->toBeArray()
        ->toBeEmpty();
});

it(description: 'sets a page size', closure: function (): void
{
    $this->pdfService->setPageSize(size: PageSizeEnum::LETTER);

    expect(value: $this->pdfService->pageSize)
        ->toBe(expected: 'letter');
});

it(description: 'sets a page view', closure: function (): void
{
    $this->pdfService->setPageView(view: 'test.pdf');

    expect(value: $this->pdfService->pageView)
        ->toBe(expected: 'test.pdf');
});

it(description: 'sets a pages margins', closure: function (): void
{
    $this->pdfService->setPageMargins(
        top   : 10.5,
        right : 12.1,
        bottom: 7.6,
        left  : 25.4,
        unit  : 'cm'
    );

    expect(value: $this->pdfService->marginTop)
        ->toBe(expected: 10.5)
        ->and(value: $this->pdfService->marginBottom)
        ->toBe(expected: 7.6)
        ->and(value: $this->pdfService->marginLeft)
        ->toBe(expected: 25.4)
        ->and(value: $this->pdfService->marginRight)
        ->toBe(expected: 12.1)
        ->and(value: $this->pdfService->marginUnit)
        ->toBe(expected: 'cm');
});

it(description: 'sets a file name', closure: function (): void
{
    $this->pdfService->setFilename(fileName: $this->fileName);

    expect(value: $this->pdfService->fileName)
        ->toBe(expected: $this->fileName . '.pdf');
});

it(description: 'creates a temp file name', closure: function (): void
{
    $this->pdfService->createTempFileName();

    expect(value: $this->pdfService->fileName)
        ->not->toBe(expected: 'invoice.pdf');
});

it(description: 'sets pdf data', closure: function (): void
{
    $data = $this->dataService->build();

    $this->pdfService->setPdfData(
        data: $data
    );

    expect(value: $this->pdfService->pdfData)
        ->toBe(expected: $data);
});

it(description: 'sends back an error when attempting to render pdf view', closure: function (): void
{
    $error = 'View has errors';
    $mock  = Mockery::mock(args: ViewFactory::class);

    $mock->shouldReceive(methodNames: 'make')
        ->andThrow(exception: new RuntimeException(message: $error));

    View::swap(instance: $mock);

    $this->pdfService->generate();

    expect(value: $this->pdfService->errors)
        ->not->toBe(expected: [])
        ->toHaveCount(count: 1)
        ->and(value: $this->pdfService->errors[0])
        ->toBe(expected: $error);
});

it(description: 'has expected headers', closure: function (): void
{
    $this->pdfService->setFilename(fileName: $this->fileName);

    $reflectionClass = new ReflectionClass(objectOrClass: $this->pdfService);
    $method          = $reflectionClass->getMethod(name: 'outputToBrowserHeaders');
    $method->setAccessible(accessible: true);

    $headers = $method->invoke(object: $this->pdfService);

    expect(value: $headers)
        ->toBeArray()
        ->toHaveCount(count: 2)
        ->toHaveKey(key: 'Content-Type')
        ->toHaveKey(key: 'Content-Disposition')
        ->and(value: $headers['Content-Type'])
        ->toBe(expected: 'application/pdf')
        ->and(value: $headers['Content-Disposition'])
        ->toBe(expected: 'inline; filename="' . $this->fileName . '.pdf"');
});

it(description: 'uses the right storage path', closure: function (): void
{
    $this->pdfService->setFilename(fileName: $this->fileName);

    $reflectionClass = new ReflectionClass(objectOrClass: $this->pdfService);
    $method          = $reflectionClass->getMethod(name: 'getStoragePath');
    $method->setAccessible(accessible: true);

    expect(value: $method->invoke(object: $this->pdfService))
        ->toBe(expected: Storage::disk(name: 'local')->path(path: $this->fileName . '.pdf'));
});

it(description: 'send back valid html from rendered view', closure: function (): void
{
    $pdfData  = $this->dataService->build();
    $viewHtml = view(
        view: 'pdf.invoice',
        data: $pdfData
    )->render();

    $this->pdfService->setPdfData(data: $pdfData);

    $reflectionClass = new ReflectionClass(objectOrClass: $this->pdfService);
    $method          = $reflectionClass->getMethod(name: 'getHtml');
    $method->setAccessible(accessible: true);

    expect(value: $method->invoke(object: $this->pdfService))
        ->toBe(expected: $viewHtml);
});

it(description: 'returns errors array with getErrors method', closure: function (): void
{
    // Test with empty errors array
    expect(value: $this->pdfService->getErrors())
        ->toBeArray()
        ->toBeEmpty();

    // Test with errors set
    $this->pdfService->errors = ['Error 1', 'Error 2'];

    expect(value: $this->pdfService->getErrors())
        ->toBeArray()
        ->toHaveCount(count: 2)
        ->toContain(value: 'Error 1')
        ->toContain(value: 'Error 2');
});

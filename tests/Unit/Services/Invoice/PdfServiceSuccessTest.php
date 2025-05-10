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

it(description: 'successfully generated an invoice pdf', closure: function (): void
{
    Artisan::call(command: 'view:clear');
    Artisan::call(command: 'config:clear');
    Artisan::call(command: 'cache:clear');

    Carbon::setTestNow(now());

    $user = testUser();

    $order = Order::factory()
        ->for($user)
        ->create();

    Payment::factory()
        ->for($order)
        ->for($user)
        ->create();

    $fileName = collect(value: [
        config(key: 'app.name', default: ''),
        $order->user->name                        ?? 'unknown',
        $order->payment->formatted_invoice_number ?? Str::random(length: 8),
        now()->timestamp,
    ])->implode(value: ' ');

    $fileName = str(string: $fileName)
        ->slug()
        ->toString();

    $pdfService  = app()->make(abstract: PdfServiceInterface::class);
    $dataService = app()->make(abstract: InvoiceDataService::class, parameters: [
        'order' => $order,
    ]);

    $disk    = Storage::disk(name: 'local');
    $pdfName = $fileName . '.pdf';

    $mockBrowsershot = Mockery::mock('alias:Spatie\Browsershot\Browsershot');
    $mockBrowsershot->shouldReceive('html')->andReturnSelf();
    $mockBrowsershot->shouldReceive('showBackground')->andReturnSelf();
    $mockBrowsershot->shouldReceive('noSandbox')->andReturnSelf();
    $mockBrowsershot->shouldReceive('margins')->andReturnSelf();
    $mockBrowsershot->shouldReceive('format')->andReturnSelf();
    $mockBrowsershot->shouldReceive('pages')->andReturnSelf();
    $mockBrowsershot->shouldReceive('paperSize')->andReturnSelf();
    $mockBrowsershot->shouldReceive('timeout')->andReturnSelf();
    $mockBrowsershot->shouldReceive('save')->with(Mockery::any())->andReturnUsing(function ($targetPath)
    {
        file_put_contents(filename: $targetPath, data: 'Test PDF content');

        return true;
    });

    $pdfService
        ->setPageMargins(top: 0, right: 0, bottom: 0, left: 0)
        ->setPageSize(size: PageSizeEnum::A4)
        ->setPageView(view: 'pdf.invoice')
        ->setPdfData(data: $dataService->build())
        ->setFilename(fileName: $fileName)
        ->generate();

    expect(value: $disk->exists(path: $pdfName))
        ->toBeTrue();

    $disk->delete(paths: $pdfName);

    expect(value: $disk->exists(path: $pdfName))
        ->toBeFalse();

    Carbon::setTestNow();
    Mockery::close();
});

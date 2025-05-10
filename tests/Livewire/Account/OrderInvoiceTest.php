<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Services\Invoice\PdfServiceInterface;
use App\Livewire\Account\Orders;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\Classes\Unit\Services\Invoice\TestPdfService;

beforeEach(function (): void
{
    // Bind the TestPdfService to the PdfServiceInterface
    app()->bind(
        abstract: PdfServiceInterface::class,
        concrete: fn () => new TestPdfService()
    );
});

afterEach(function (): void
{
    Carbon::setTestNow();
});

it('can download an invoice pdf successfully', function (): void
{
    $testUser = testUser();

    $order = Order::factory()
        ->for(factory: $testUser)
        ->create();

    Payment::factory()
        ->for(factory: $order)
        ->for(factory: $testUser)
        ->create();

    // Use a fixed timestamp for deterministic testing
    $fixedTime = Carbon::parse('2025-04-29 08:00:00');
    Carbon::setTestNow($fixedTime);

    $testName = collect(value: [
        config(key: 'app.name', default: ''),
        $order->user->name                        ?? 'unknown',
        $order->payment->formatted_invoice_number ?? Str::random(length: 8),
        now()->timestamp,
    ])->implode(value: ' ');

    $fileName = str(string: $testName)
        ->slug()
        ->toString() . '.pdf';

    Livewire::actingAs(user: $testUser)
        ->test(name: Orders::class)
        ->call('downloadPdf', $order->id)
        ->assertFileDownloaded(
            filename : $fileName,
        );

    expect(value: Storage::disk(name: 'local')->exists(path: $fileName))
        ->toBeFalse();

    Carbon::setTestNow();
})->group('download-invoice');

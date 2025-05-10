<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */

declare(strict_types=1);

use App\Contracts\Services\Invoice\InvoiceServiceInterface;
use App\Contracts\Services\Invoice\PdfServiceInterface;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\Invoice\InvoiceService;
use Illuminate\Support\Facades\Config;
use Tests\Classes\Unit\Services\Invoice\TestInvoiceService;
use Tests\Classes\Unit\Services\Invoice\TestPdfService;

beforeEach(closure: function (): void
{
    $this->user = User::factory()->create(attributes: [
        'name'  => 'Test User',
        'email' => 'test@example.com',
    ]);

    $this->order = Order::factory()->create(attributes: [
        'user_id'             => $this->user->id,
        'package_name'        => 'Test Package',
        'package_description' => 'Test Description',
    ]);

    $this->payment = Payment::factory()->create(attributes: [
        'order_id'       => $this->order->id,
        'user_id'        => $this->user->id,
        'amount'         => 10000,
        'invoice_number' => 12345,
        'card_type'      => 'visa',
        'card_last4'     => '4242',
    ]);

    Config::set('app.name', 'Test App');
    Config::set('app.url', 'https://test.app');
    Config::set('settings.contact', 'contact@test.app');
    Config::set('settings.invoices.address', 'Line 1|Line 2|Line 3');
    Config::set('settings.invoices.descriptor', 'Test Descriptor');
    Config::set('settings.invoices.padding', 6);

    $this->pdfService = new TestPdfService();

    app()->bind(
        abstract: PdfServiceInterface::class,
        concrete: fn () => $this->pdfService
    );

    app()->bind(
        abstract: InvoiceServiceInterface::class,
        concrete: TestInvoiceService::class
    );
});

it(description: 'initializes with the correct properties', closure: function (): void
{
    $service = app()->makeWith(
        abstract: InvoiceServiceInterface::class,
        parameters: [
            'order' => $this->order,
            'pdf'   => $this->pdfService,
        ]
    );

    expect(value: $service->order)
        ->toBe(expected: $this->order)
        ->and(value: $service->pdf)
        ->toBe(expected: $this->pdfService)
        ->and(value: $service->error)
        ->toBeNull();
});

it(description: 'builds the correct file name', closure: function (): void
{
    $service = app()->makeWith(
        abstract: InvoiceServiceInterface::class,
        parameters: [
            'order' => $this->order,
            'pdf'   => $this->pdfService,
        ]
    );

    $service->shouldBuildFileName = true;

    $service->buildDownload();

    expect(value: $service->generatedFileName)
        ->toMatch(expression: '/test-app-test-user-\d+-\d+/');
});

it(description: 'builds invoice PDF data correctly', closure: function (): void
{
    $service = app()->makeWith(
        abstract: InvoiceServiceInterface::class,
        parameters: [
            'order' => $this->order,
            'pdf'   => $this->pdfService,
        ]
    );

    $service->shouldBuildPdfData = true;

    $service->buildDownload();

    expect(value: $service->pdfData)
        ->toBeArray()
        ->toHaveKeys(keys: ['user', 'order', 'payment', 'settings'])
        ->and(value: $service->pdfData['user'])
        ->toHaveKeys(keys: ['id', 'name', 'email'])
        ->and(value: $service->pdfData['user']['name'])
        ->toBe(expected: 'Test User')
        ->and(value: $service->pdfData['order'])
        ->toHaveKeys(keys: ['id', 'package', 'description', 'date'])
        ->and(value: $service->pdfData['order']['package'])
        ->toBe(expected: 'Test Package')
        ->and(value: $service->pdfData['payment'])
        ->toHaveKeys(keys: ['id', 'amount', 'invoice_number', 'card_type', 'card_last4'])
        ->and(value: $service->pdfData['payment']['amount'])
        ->not()->toBeEmpty()
        ->and(value: $service->pdfData['settings'])
        ->toHaveKeys(keys: ['app_name', 'app_url', 'contact', 'address', 'descriptor']);
});

it(description: 'configures PDF service correctly during build', closure: function (): void
{
    $this->pdfService              = new TestPdfService();
    $this->pdfService->pageMargins = [];

    $service = app()->makeWith(
        abstract: InvoiceServiceInterface::class,
        parameters: [
            'order' => $this->order,
            'pdf'   => $this->pdfService,
        ]
    );

    $service->shouldConfigurePdf = true;

    $service->buildDownload();

    expect(value: $this->pdfService->marginTop)
        ->toBe(expected: 0.0)
        ->and(value: $this->pdfService->marginRight)
        ->toBe(expected: 0.0)
        ->and(value: $this->pdfService->marginBottom)
        ->toBe(expected: 0.0)
        ->and(value: $this->pdfService->marginLeft)
        ->toBe(expected: 0.0)
        ->and(value: $this->pdfService->pageSize)
        ->toBe(expected: 'a4')
        ->and(value: $this->pdfService->pageView)
        ->toBe(expected: 'pdf.invoice');
});

it(description: 'handles PDF generation errors', closure: function (): void
{
    $this->pdfService = new TestPdfService();
    $this->pdfService->simulateHtmlRenderingError();

    $service = app()->makeWith(
        abstract: InvoiceServiceInterface::class,
        parameters: [
            'order' => $this->order,
            'pdf'   => $this->pdfService,
        ]
    );

    $service->shouldHandleErrors = true;

    $service->buildDownload();

    expect(value: $service->error)
        ->toBe(expected: 'View not found');
});

it(description: 'handles exceptions during PDF generation', closure: function (): void
{
    Config::set('errors.technical', 'A technical error occurred');

    $this->pdfService                       = new TestPdfService();
    $this->pdfService->shouldThrowException = true;

    $service = app()->makeWith(
        abstract: InvoiceServiceInterface::class,
        parameters: [
            'order' => $this->order,
            'pdf'   => $this->pdfService,
        ]
    );

    $service->shouldThrowException   = true;
    $service->shouldHandleExceptions = true;

    $service->buildDownload();

    expect(value: $service->error)
        ->toBe(expected: 'A technical error occurred');
});

it(description: 'uses the actual implementation to generate a PDF', closure: function (): void
{
    $this->pdfService = new TestPdfService();

    $service = new InvoiceService(
        order: $this->order,
        pdf  : $this->pdfService
    );

    expect(value: $service->error)->toBeNull();
});

it(description: 'handles exceptions in the actual implementation', closure: function (): void
{
    Config::set('errors.technical', 'A technical error occurred');

    $this->pdfService                       = new TestPdfService();
    $this->pdfService->shouldThrowException = true;

    $service = new InvoiceService(
        order: $this->order,
        pdf  : $this->pdfService
    );

    expect(value: $service->error)
        ->toBe(expected: 'A technical error occurred');
});

it(description: 'returns error value with getError method', closure: function (): void
{
    $service = app()->makeWith(
        abstract: InvoiceServiceInterface::class,
        parameters: [
            'order' => $this->order,
            'pdf'   => $this->pdfService,
        ]
    );

    // Test with null error
    expect(value: $service->getError())
        ->toBeNull();

    // Test with an error set
    $service->error = 'Test error message';

    expect(value: $service->getError())
        ->toBe(expected: 'Test error message');
});

it(description: 'returns pdf service with getPdf method', closure: function (): void
{
    $service = app()->makeWith(
        abstract: InvoiceServiceInterface::class,
        parameters: [
            'order' => $this->order,
            'pdf'   => $this->pdfService,
        ]
    );

    expect(value: $service->getPdf())
        ->toBe(expected: $this->pdfService)
        ->toBeInstanceOf(class: PdfServiceInterface::class);
});

it(description: 'handles empty errors array from PDF service', closure: function (): void
{
    // Set up the technical error message
    Config::set('errors.technical', 'A technical error occurred');

    // Create a PDF service with errors but empty errors array
    $pdfService                                 = new TestPdfService();
    $pdfService->simulateHasErrorsButEmptyArray = true;

    // Create the invoice service
    $service = new InvoiceService(
        order: $this->order,
        pdf: $pdfService
    );

    // Verify it used the fallback error message
    expect(value: $service->error)->toBe(expected: 'A technical error occurred');
});

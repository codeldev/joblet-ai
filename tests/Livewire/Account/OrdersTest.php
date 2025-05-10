<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Services\Invoice\PdfServiceInterface;
use App\Livewire\Account\Orders;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(closure: function (): void
{
    $this->testUser = testUser();

    $this->order = Order::factory()
        ->for(factory: $this->testUser)
        ->create();

    Payment::factory()
        ->for(factory: $this->order)
        ->for(factory: $this->testUser)
        ->create();

    Carbon::setTestNow(now());

    $testName = collect(value: [
        config(key: 'app.name', default: ''),
        $this->order->user->name                        ?? 'unknown',
        $this->order->payment->formatted_invoice_number ?? Str::random(length: 8),
        now()->timestamp,
    ])->implode(value: ' ');

    $this->fileName = str(string: $testName)
        ->slug()
        ->toString() . '.pdf';
});

afterEach(closure: function (): void
{
    Carbon::setTestNow();
});

it('can render the orders component', function (): void
{
    Livewire::actingAs(user: $this->testUser)
        ->test(name: Orders::class)
        ->assertSee(values: trans(key: 'orders.title'));
});

it('cannot download an invoice pdf for another use', function (): void
{
    $payment = Payment::factory()->create();

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Orders::class)
        ->call('downloadPdf', $payment->order_id)
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'misc.action.disallowed'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('cannot download an invoice pdf for a free order', function (): void
{
    $order = Order::factory()
        ->for(factory: $this->testUser)
        ->create(attributes: ['free' => true]);

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Orders::class)
        ->call('downloadPdf', $order->id)
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'invoice.free.error'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

it('shows an error when it fails to download an invoice pdf', function (): void
{
    $this->mock(
        abstract: PdfServiceInterface::class,
        mock: function (Mockery\MockInterface $mock): void
        {
            $mock->shouldReceive(methodNames: 'generate')
                ->andThrow(exception: new Exception(message: 'PDF generation failed'));
        }
    );

    Livewire::actingAs(user: $this->testUser)
        ->test(name: Orders::class)
        ->call('downloadPdf', $this->order->id)
        ->assertDispatched(
            event   : 'toast-show',
            duration: 3500,
            slots   : [
                'text' => trans(key: 'errors.technical'),
            ],
            dataset : [
                'variant'  => 'danger',
            ]
        );
});

<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Services\Invoice\InvoiceDataServiceInterface;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Invoice\InvoiceDataService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

beforeEach(closure: function (): void
{
    Config::set(key: 'settings.invoices.address', value: 'Line 1|Line 2|Line 3');
    Config::set(key: 'app.name', value: 'Joblet');
    Config::set(key: 'app.url', value: 'https://joblet.ai');
    Config::set(key: 'settings.contact', value: 'contact@joblet.ai');
    Config::set(key: 'settings.invoices.descriptor', value: 'Invoice');
});

it(description: 'builds complete invoice data with all components', closure: function (): void
{
    $user = testUser();

    $order = Order::factory()
        ->for(factory: $user)
        ->create(attributes: [
            'package_name'        => 'Test Package',
            'package_description' => 'Test Description',
            'created_at'          => Carbon::parse(time: '2025-01-01 12:00:00'),
        ]);

    $payment = Payment::factory()
        ->for(factory: $order)
        ->for(factory: $user)
        ->create(attributes: [
            'invoice_number' => 1001,
            'amount'         => 10000,
            'created_at'     => Carbon::parse(time: '2025-01-02 12:00:00'),
        ]);

    $service = new InvoiceDataService(order: $order);
    $result  = $service->build();

    expect(value: $result)
        ->toBeArray()
        ->toHaveKeys(keys: ['user', 'order', 'payment', 'settings'])
        ->and(value: $result['user'])
        ->toBeArray()
        ->toHaveKeys(keys: ['id', 'name', 'email'])
        ->and(value: $result['user']['id'])
        ->toBe(expected: $user->id)
        ->and(value: $result['user']['name'])
        ->toBe(expected: $user->name)
        ->and(value: $result['user']['email'])
        ->toBe(expected: $user->email)
        ->and(value: $result['order'])
        ->toBeArray()
        ->toHaveKeys(keys: ['id', 'package', 'description', 'date'])
        ->and(value: $result['order']['id'])
        ->toBe(expected: $order->id)
        ->and(value: $result['order']['package'])
        ->toBe(expected: 'Test Package')
        ->and(value: $result['order']['description'])
        ->toBe(expected: 'Test Description')
        ->and(value: $result['order']['date'])
        ->toBe(expected: 'Wednesday, 1st January 2025 12:00pm')
        ->and(value: $result['payment'])
        ->toBeArray()
        ->toHaveKeys(keys: ['id', 'date', 'amount', 'invoice', 'type'])
        ->and(value: $result['payment']['id'])
        ->toBe(expected: $payment->id)
        ->and(value: $result['payment']['date'])
        ->toBe(expected: '02/01/2025')
        ->and(value: $result['payment']['amount'])
        ->toBe(expected: $payment->formatted_amount)
        ->and(value: $result['payment']['invoice'])
        ->toBe(expected: $payment->formatted_invoice_number)
        ->and(value: $result['settings'])
        ->toBeArray()
        ->toHaveKeys(keys: ['name', 'website', 'email', 'descriptor', 'address'])
        ->and(value: $result['settings']['name'])
        ->toBe(expected: 'Joblet')
        ->and(value: $result['settings']['website'])
        ->toBe(expected: 'https://joblet.ai')
        ->and(value: $result['settings']['email'])
        ->toBe(expected: 'contact@joblet.ai')
        ->and(value: $result['settings']['descriptor'])
        ->toBe(expected: 'Invoice')
        ->and(value: $result['settings']['address']->toArray())
        ->toBe(expected: ['Line 1', 'Line 2', 'Line 3']);
});

it(description: 'handles null payment gracefully', closure: function (): void
{
    $user = testUser();

    $order = Order::factory()
        ->for(factory: $user)
        ->create();

    $service = new InvoiceDataService(order: $order);
    $result  = $service->build();

    expect(value: $result)
        ->toBeArray()
        ->toHaveKeys(keys: ['user', 'order', 'payment', 'settings'])
        ->and(value: $result['payment'])
        ->toBeArray()
        ->toBeEmpty();
});

it(description: 'handles null user data gracefully', closure: function (): void
{
    $user  = testUser();
    $order = Order::factory()
        ->for(factory: $user)
        ->create();

    $user->delete();

    $order->unsetRelation(relation: 'user');

    $service = new InvoiceDataService(order: $order);
    $result  = $service->build();

    expect(value: $result)
        ->toBeArray()
        ->toHaveKeys(keys: ['user', 'order', 'payment', 'settings'])
        ->and(value: $result['user'])
        ->toBeArray()
        ->toHaveCount(count: 3)
        ->and(value: $result['user']['name'])
        ->toBeNull();
});

it(description: 'uses empty array when payment is null', closure: function (): void
{
    $user = testUser();

    $order = Order::factory()
        ->for(factory: $user)
        ->create();

    $service = new InvoiceDataService(order: $order);
    $result  = $service->build();

    expect(value: $result['payment'])
        ->toBeArray()
        ->toBeEmpty();
});

it(description: 'formats payment data correctly', closure: function (): void
{
    $user = testUser();

    $order = Order::factory()
        ->for(factory: $user)
        ->create();

    $payment = Payment::factory()
        ->for(factory: $order)
        ->for(factory: $user)
        ->create(attributes: [
            'amount'         => 10000,
            'invoice_number' => 1001,
        ]);

    $service = new InvoiceDataService(order: $order);
    $result  = $service->build();

    expect(value: $result['payment'])
        ->toBeArray()
        ->toHaveKeys(keys: ['id', 'date', 'amount', 'invoice', 'type'])
        ->and(value: $result['payment']['id'])
        ->toBe(expected: $payment->id)
        ->and(value: $result['payment']['amount'])
        ->toBe(expected: $payment->formatted_amount);
});

it(description: 'builds data with all required components', closure: function (): void
{
    $user = testUser();

    $order = Order::factory()
        ->for(factory: $user)
        ->create();

    $payment = Payment::factory()->create([
        'order_id'   => $order->id,
        'card_type'  => 'Visa',
        'card_last4' => '1234',
    ]);

    $service = new InvoiceDataService(order: $order);
    $result  = $service->build();

    expect(value: $result)
        ->toBeArray()
        ->toHaveKeys(keys: ['user', 'order', 'payment', 'settings'])
        ->and(value: $result['user'])
        ->toBeArray()
        ->toHaveCount(count: 3)
        ->and(value: $result['order'])
        ->toBeArray()
        ->toHaveCount(count: 4)
        ->and(value: $result['payment'])
        ->toBeArray()
        ->toHaveCount(count: 5)
        ->and(value: $result['settings'])
        ->toBeArray()
        ->toHaveCount(count: 5);
});

it(description: 'uses random string when invoice number is missing', closure: function (): void
{
    $user = testUser();

    $order = Order::factory()
        ->for(factory: $user)
        ->create();

    // Create a payment with null invoice_number to force using getRandomString
    $payment = Payment::factory()
        ->for(factory: $order)
        ->for(factory: $user)
        ->create(attributes: [
            'invoice_number' => null,
        ]);

    // Resolve the service from the container
    $service = app()->makeWith(abstract: InvoiceDataServiceInterface::class, parameters: [
        'order' => $order,
    ]);

    $result = $service->build();

    expect(value: $result)
        ->toBeArray()
        ->toHaveKeys(keys: ['payment'])
        ->and(value: $result['payment'])
        ->toHaveKey(key: 'invoice')
        ->and(value: $result['payment']['invoice'])
        ->toBeString()
        ->toHaveLength(10);
});

<?php

declare(strict_types=1);

namespace App\Services\Invoice;

use App\Contracts\Services\Invoice\InvoiceDataServiceInterface;
use App\Models\Order;
use Illuminate\Support\Str;

final class InvoiceDataService implements InvoiceDataServiceInterface
{
    /** @var array<string, string|null> */
    private array $userData = [];

    /** @var array<string, string> */
    private array $orderData = [];

    /** @var array<string, mixed> */
    private array $paymentData = [];

    /** @var array<string, string|array<int, string>> */
    private array $settingsData = [];

    public function __construct(private readonly Order $order) {}

    /** @return array<string, mixed> */
    public function build(): array
    {
        return $this
            ->buildUser()
            ->buildOrder()
            ->buildPayment()
            ->buildSettings()
            ->buildData();
    }

    private function buildUser(): self
    {
        $this->userData = [
            'id'    => $this->order->user?->id,
            'name'  => $this->order->user?->name,
            'email' => $this->order->user?->email,
        ];

        return $this;
    }

    private function buildOrder(): self
    {
        $this->orderData = [
            'id'          => $this->order->id,
            'package'     => $this->order->package_name,
            'description' => $this->order->package_description,
            'date'        => $this->order->created_at->format(format: 'l, jS F Y g:ia'),
        ];

        return $this;
    }

    private function buildPayment(): self
    {
        if ($this->order->payment)
        {
            $this->paymentData = [
                'id'      => $this->order->payment->id,
                'date'    => $this->order->payment->created_at->format(format: 'd/m/Y'),
                'amount'  => $this->order->payment->formatted_amount         ?? '0.00',
                'invoice' => $this->order->payment->formatted_invoice_number ?? Str::random(length: 8),
                'type'    => $this->order->payment->payment_method,
            ];
        }

        return $this;
    }

    private function buildSettings(): self
    {
        /** @var string $address */
        $address = config(key: 'settings.invoices.address', default: '');

        /** @var array<int,string> $addressLines */
        $addressLines = str(string: $address)
            ->explode(delimiter: '|');

        /** @var string $appName */
        $appName = config(key: 'app.name', default: '');

        /** @var string $appUrl */
        $appUrl = config(key: 'app.url', default: '');

        /** @var string $contact */
        $contact = config(key: 'settings.contact', default: '');

        /** @var string $descriptor */
        $descriptor = config(key: 'settings.invoices.descriptor', default: '');

        $this->settingsData = [
            'name'       => $appName,
            'website'    => $appUrl,
            'email'      => $contact,
            'descriptor' => $descriptor,
            'address'    => $addressLines,
        ];

        return $this;
    }

    /** @return array<string, mixed> */
    private function buildData(): array
    {
        return [
            'user'     => $this->userData,
            'order'    => $this->orderData,
            'payment'  => $this->paymentData,
            'settings' => $this->settingsData,
        ];
    }
}

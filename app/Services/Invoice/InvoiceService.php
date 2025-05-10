<?php

declare(strict_types=1);

namespace App\Services\Invoice;

use App\Contracts\Services\Invoice\InvoiceDataServiceInterface;
use App\Contracts\Services\Invoice\InvoiceServiceInterface;
use App\Contracts\Services\Invoice\PdfServiceInterface;
use App\Enums\PageSizeEnum;
use App\Models\Order;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;

final class InvoiceService implements InvoiceServiceInterface
{
    public ?string $error = null;

    public function __construct(
        private readonly Order $order,
        public PdfServiceInterface $pdf
    ) {
        $this->buildDownload();
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getPdf(): PdfServiceInterface
    {
        return $this->pdf;
    }

    public function buildDownload(): void
    {
        try
        {
            $this->pdf
                ->setPageMargins(top: 0, right: 0, bottom: 0, left: 0)
                ->setPageSize(size: PageSizeEnum::A4)
                ->setPageView(view: 'pdf.invoice')
                ->setPdfData(data: $this->buildInvoicePdfData())
                ->setFilename(fileName: $this->buildFileName())
                ->generate();

            if ($this->pdf->hasErrors())
            {
                $errors = $this->pdf->getErrors();

                $this->error = $errors[0] ?? trans(key: 'errors.technical');
            }
        }
        catch (Exception $e)
        {
            report(exception: $e);

            $this->error = trans(key: 'errors.technical');
        }
    }

    private function buildFileName(): string
    {
        $fileName = collect(value: [
            config(key: 'app.name', default: ''),
            $this->order->user->name                        ?? 'unknown',
            $this->order->payment->formatted_invoice_number ?? Str::random(length: 8),
            now()->timestamp,
        ])->implode(value: ' ');

        return str(string: $fileName)
            ->slug()
            ->toString();
    }

    /**
     * @return array<string, mixed>
     *
     * @throws BindingResolutionException
     */
    private function buildInvoicePdfData(): array
    {
        $service = app()->make(
            abstract: InvoiceDataServiceInterface::class,
            parameters: ['order' => $this->order]
        );

        /** @var array<string, mixed> $data */
        $data = $service->build();

        return $data;
    }
}

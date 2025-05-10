<?php

declare(strict_types=1);

namespace App\Contracts\Services\Invoice;

interface InvoiceServiceInterface
{
    public function buildDownload(): void;

    public function getError(): ?string;

    public function getPdf(): PdfServiceInterface;
}

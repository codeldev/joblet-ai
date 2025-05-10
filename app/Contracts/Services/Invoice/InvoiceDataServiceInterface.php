<?php

declare(strict_types=1);

namespace App\Contracts\Services\Invoice;

interface InvoiceDataServiceInterface
{
    /** @return array<string, mixed> */
    public function build(): array;
}

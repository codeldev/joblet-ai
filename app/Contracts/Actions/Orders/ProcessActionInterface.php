<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Orders;

use Illuminate\Http\JsonResponse;

interface ProcessActionInterface
{
    /** @param array<string, mixed> $paymentData */
    public function handle(array $paymentData): JsonResponse;
}

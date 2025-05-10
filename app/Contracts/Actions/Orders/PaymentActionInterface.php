<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Orders;

use App\Models\Order;
use Illuminate\Http\JsonResponse;

interface PaymentActionInterface
{
    /**
     * @param  array<string, mixed>  $paymentData
     */
    public function handle(Order $order, array $paymentData): JsonResponse;
}

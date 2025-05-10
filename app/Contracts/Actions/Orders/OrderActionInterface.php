<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Orders;

use App\Models\Order;

interface OrderActionInterface
{
    /**
     * @param  array<string, mixed>  $paymentData
     */
    public function handle(array $paymentData): ?Order;
}

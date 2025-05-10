<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Http\Requests\Webhooks;

use App\Contracts\Actions\Orders\OrderActionInterface;
use App\Models\Order;

final class TestOrderActionHandler implements OrderActionInterface
{
    public function handle(array $paymentData): Order
    {
        return new Order();
    }
}

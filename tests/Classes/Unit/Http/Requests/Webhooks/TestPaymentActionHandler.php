<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Http\Requests\Webhooks;

use App\Contracts\Actions\Orders\PaymentActionInterface;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

final class TestPaymentActionHandler implements PaymentActionInterface
{
    public function handle(Order $order, array $paymentData): JsonResponse
    {
        return response()->json(data: ['success' => true]);
    }
}

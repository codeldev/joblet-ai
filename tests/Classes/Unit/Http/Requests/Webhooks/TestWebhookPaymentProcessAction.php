<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Http\Requests\Webhooks;

use Illuminate\Http\JsonResponse;

final class TestWebhookPaymentProcessAction
{
    public function handle(array $paymentData): JsonResponse
    {
        return response()->json(['success' => true]);
    }
}

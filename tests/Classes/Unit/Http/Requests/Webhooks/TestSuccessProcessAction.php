<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Http\Requests\Webhooks;

use App\Contracts\Actions\Orders\ProcessActionInterface;
use Illuminate\Http\JsonResponse;

final class TestSuccessProcessAction implements ProcessActionInterface
{
    public function handle(array $paymentData): JsonResponse
    {
        return response()->json(data: ['success' => true]);
    }
}

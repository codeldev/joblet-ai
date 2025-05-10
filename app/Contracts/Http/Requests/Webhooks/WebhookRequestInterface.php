<?php

declare(strict_types=1);

namespace App\Contracts\Http\Requests\Webhooks;

use Illuminate\Http\JsonResponse;

interface WebhookRequestInterface
{
    public function __invoke(): JsonResponse;

    public function authorize(): bool;

    /** @return array<string, array<int, string>> */
    public function rules(): array;
}

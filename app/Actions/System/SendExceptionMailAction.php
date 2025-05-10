<?php

declare(strict_types=1);

namespace App\Actions\System;

use App\Contracts\Actions\System\SendExceptionMailActionInterface;
use App\Notifications\System\ExceptionReportNotification;
use App\Services\Models\UserService;
use Exception;
use Illuminate\Support\Facades\Auth;
use JsonException;
use Throwable;

final readonly class SendExceptionMailAction implements SendExceptionMailActionInterface
{
    public function __construct(private Throwable | Exception | JsonException $exception) {}

    public function handle(): void
    {
        if (app()->isLocal())
        {
            return;
        }

        if (! config(key: 'settings.exceptions.send_mail', default: false))
        {
            return;
        }

        UserService::getSupportUser()->notify(
            instance: new ExceptionReportNotification(
                exceptionData: $this->buildExceptionData()
            )
        );
    }

    /** @return array<string, mixed> */
    private function buildExceptionData(): array
    {
        /** @var array<int, array<string, mixed>> $trace */
        $trace = collect(value: $this->exception->getTrace())
            ->map(callback: fn (array $line): array => collect(value: $line)->forget(keys: 'args')->toArray())
            ->toArray();

        return [
            'message' => $this->exception->getMessage(),
            'file'    => $this->exception->getFile(),
            'line'    => $this->exception->getLine(),
            'trace'   => $trace,
            'url'     => request()->url() ?: 'Unknown',
            'body'    => request()->all(),
            'ip'      => request()->ip() ?: 'Unknown',
            'user'    => Auth::check() && Auth::user() !== null ? Auth::user()->name : 'No User',
        ];
    }
}

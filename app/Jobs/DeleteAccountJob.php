<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class DeleteAccountJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly string $userId) {}

    public function handle(): void
    {
        User::find(id: $this->userId)?->delete();
    }
}

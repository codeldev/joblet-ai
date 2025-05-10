<?php

declare(strict_types=1);

namespace App\Abstracts\Services\Generator;

use App\Models\User;

abstract class Prompt
{
    /** @var User user */
    protected User $user;

    /** @param array<string,mixed> $settings */
    public function __construct(protected array $settings)
    {
        /** @var User $user */
        $user = auth()->user();
        
        $this->user = $user;
    }

    abstract public function role(): string;

    abstract public function build(): string;
}

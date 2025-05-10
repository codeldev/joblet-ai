<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Generator;

use Throwable;

interface GenerateActionInterface
{
    /**
     * @param  array<string, mixed>  $settings
     *
     * @throws Throwable
     */
    public function handle(array $settings, callable $success, callable $failed): void;
}

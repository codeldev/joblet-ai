<?php

declare(strict_types=1);

namespace App\Contracts\Actions\System;

interface SendExceptionMailActionInterface
{
    public function handle(): void;
}

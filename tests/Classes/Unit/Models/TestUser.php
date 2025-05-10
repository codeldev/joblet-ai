<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Models;

use App\Models\User;

final class TestUser extends User
{
    public function __construct(public readonly string $id)
    {
        parent::__construct();
    }
}

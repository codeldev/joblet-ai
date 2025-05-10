<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Http\Requests\Payment;

use App\Enums\ProductPackageEnum;

final class TestProcessorWithoutProcess
{
    public function __construct(ProductPackageEnum $package, $user) {}
}

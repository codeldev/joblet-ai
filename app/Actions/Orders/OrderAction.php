<?php

declare(strict_types=1);

namespace App\Actions\Orders;

use App\Contracts\Actions\Orders\OrderActionInterface;
use App\Enums\ProductPackageEnum;
use App\Models\Order;
use App\Models\User;

final class OrderAction implements OrderActionInterface
{
    /** @param array<string, mixed> $paymentData */
    public function handle(array $paymentData): ?Order // implements OrderActionInterface
    {
        /** @var User|null $user */
        $user = $paymentData['user'];

        if (! $user instanceof User)
        {
            return null;
        }

        /** @var ProductPackageEnum|null $package */
        $package = $paymentData['package'];

        if (! $package instanceof ProductPackageEnum)
        {
            return null;
        }

        return $user->orders()->create(attributes: [
            'package_id'          => $package->value,
            'package_name'        => $package->title(),
            'package_description' => $package->description(),
            'price'               => $package->price(),
            'tokens'              => $package->credits(),
            'free'                => false,
        ]);
    }
}

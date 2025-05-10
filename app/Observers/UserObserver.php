<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\ProductPackageEnum;
use App\Models\User;
use App\Models\UserHistory;

final class UserObserver
{
    public function created(User $user): void
    {
        if (! $this->hasPreviouslySignedUp(email: $user->email))
        {
            $this->addFreeSignUpCredits(user: $user);
        }

        $this->addUserHistory(email: $user->email);
    }

    public function updated(User $user): void
    {
        if ($user->isDirty(attributes: 'email'))
        {
            $this->addUserHistory(email: $user->email);
        }
    }

    private function addUserHistory(string $email): void
    {
        UserHistory::updateOrCreate(
            attributes: ['email' => $email],
            values    : ['updated_at' => now()]
        );
    }

    private function addFreeSignUpCredits(User $user): void
    {
        $package = ProductPackageEnum::INTRODUCTION;

        $user->orders()->create(attributes: [
            'package_id'          => $package->value,
            'package_name'        => $package->title(),
            'package_description' => $package->description(),
            'price'               => $package->price(),
            'tokens'              => $package->credits(),
            'free'                => true,
        ]);
    }

    private function hasPreviouslySignedUp(string $email): bool
    {
        return UserHistory::where(
            column  : 'email',
            operator: '=',
            value   : $email
        )->exists();
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Generated;
use App\Models\User;

final class GeneratedPolicy
{
    public function view(User $user, Generated $generated): bool
    {
        return $user->id === $generated->user->id;
    }

    public function update(User $user, Generated $generated): bool
    {
        return $user->id === $generated->user->id;
    }

    public function delete(User $user, Generated $generated): bool
    {
        return $user->id === $generated->user->id;
    }
}

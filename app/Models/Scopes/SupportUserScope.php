<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class SupportUserScope implements Scope
{
    /**
     * @param  Builder<Model>  $builder
     */
    public function apply(Builder $builder, Model $model): void
    {
        /** @var string $email */
        $email = config(key: 'settings.contact');

        $builder->where(
            column  : 'email',
            operator: '!=',
            value   : $email
        );
    }
}

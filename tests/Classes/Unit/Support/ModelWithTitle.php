<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Support;

use Illuminate\Database\Eloquent\Model;

final class ModelWithTitle extends Model
{
    public string $title = 'Fallback Title';

    public function __get($key)
    {
        return $this->$key;
    }
}

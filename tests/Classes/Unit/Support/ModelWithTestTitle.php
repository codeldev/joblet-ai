<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Support;

use Illuminate\Database\Eloquent\Model;

final class ModelWithTestTitle extends Model
{
    public string $title = 'Test Title';

    public function __get($key)
    {
        return $this->$key;
    }
}

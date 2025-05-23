<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Support;

use Illuminate\Database\Eloquent\Model;

final class ArrayModel extends Model
{
    public function __get($key)
    {
        return 'Array Attribute';
    }
}

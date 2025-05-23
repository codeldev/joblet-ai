<?php

/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\Blog;

use Exception;

final readonly class ExceptionThrowingImagesClient
{
    public function __construct(private Exception $exception) {}

    /** @throws Exception */
    public function create(array $parameters): object
    {
        throw $this->exception;
    }
}

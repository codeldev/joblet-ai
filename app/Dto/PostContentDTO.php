<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class PostContentDTO
{
    /** @param array<int, mixed> $toc  */
    public function __construct(public array $toc, public ?string $tocHtml, public string $html) {}
}

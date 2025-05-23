<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Support;

final class TestAppContainer
{
    private static ?self $instance = null;

    private string $locale = 'en';

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Sitemap;

use App\Contracts\Services\Sitemap\GeneratorServiceInterface;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;

final class GeneratorService implements GeneratorServiceInterface
{
    private string $output = '';

    public function get(): string
    {
        /** @var string $sitemap */
        $sitemap = Cache::remember(
            key     : 'sitemap',
            ttl     : now()->addWeek(),
            callback: fn (): string => $this->start()->pages()->end()
        );

        return $sitemap;
    }

    private function start(): self
    {
        $this->output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $this->output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        return $this;
    }

    private function end(): string
    {
        $this->output .= '</urlset>';

        return $this->output;
    }

    private function pages(): self
    {
        $this->add(
            url      : route(name: 'home'),
            updated  : CarbonImmutable::now()->subMonth(),
            priority : 1.0,
            frequency: 'monthly'
        );

        $this->add(
            url      : route(name: 'generator'),
            updated  : CarbonImmutable::now()->subMonths(value: 2),
            priority : 0.9,
            frequency: 'monthly'
        );

        $this->add(
            url      : route(name: 'support'),
            updated  : CarbonImmutable::now()->subMonths(value: 3),
            priority : 0.8,
            frequency: 'monthly'
        );

        $this->add(
            url      : route(name: 'terms'),
            updated  : CarbonImmutable::now()->subYear(),
            priority : 0.5,
            frequency: 'yearly'
        );

        $this->add(
            url      : route(name: 'privacy'),
            updated  : CarbonImmutable::now()->subYear(),
            priority : 0.5,
            frequency: 'yearly'
        );

        return $this;
    }

    private function add(string $url, CarbonImmutable $updated, float $priority, string $frequency): void
    {
        $this->output .= '<url>' . PHP_EOL;
        $this->output .= "<loc>{$url}</loc>" . PHP_EOL;
        $this->output .= "<lastmod>{$updated->toIso8601String()}</lastmod>" . PHP_EOL;
        $this->output .= "<changefreq>{$frequency}</changefreq>" . PHP_EOL;
        $this->output .= "<priority>{$priority}</priority>" . PHP_EOL;
        $this->output .= '</url>' . PHP_EOL;
    }
}

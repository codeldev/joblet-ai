<?php

/** @noinspection PhpUnusedParameterInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Contracts\Services\Sitemap\GeneratorServiceInterface;
use App\Services\Sitemap\GeneratorService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;

beforeEach(closure: function (): void
{
    CarbonImmutable::setTestNow(
        testNow: CarbonImmutable::create(year: 2025, month: 5, hour: 12)
    );
});

afterEach(closure: function (): void
{
    CarbonImmutable::setTestNow();
});

it(description: 'implements the correct interface', closure: function (): void
{
    expect(value: new GeneratorService())
        ->toBeInstanceOf(class: GeneratorServiceInterface::class);
});

it(description: 'generates a valid sitemap XML structure', closure: function (): void
{
    Cache::shouldReceive('remember')
        ->once()
        ->andReturnUsing(args: fn ($key, $ttl, $callback) => $callback());

    expect(value: $sitemap = (new GeneratorService)->get())
        ->toBeString()
        ->toContain(needle: '<?xml version="1.0" encoding="UTF-8"?>')
        ->toContain(needle: '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">')
        ->toContain(needle: '</urlset>')
        ->toContain(needle: '<loc>')
        ->toContain(needle: '<lastmod>')
        ->toContain(needle: '<changefreq>monthly</changefreq>')
        ->toContain(needle: '<changefreq>yearly</changefreq>')
        ->toContain(needle: '<priority>1</priority>')
        ->toContain(needle: '<priority>0.9</priority>')
        ->toContain(needle: '<priority>0.8</priority>')
        ->toContain(needle: '<priority>0.5</priority>')
        ->toContain(needle: '<lastmod>2025-04-01T12:00:00+00:00</lastmod>')
        ->toContain(needle: '<lastmod>2025-03-01T12:00:00+00:00</lastmod>')
        ->toContain(needle: '<lastmod>2025-02-01T12:00:00+00:00</lastmod>')
        ->toContain(needle: '<lastmod>2024-05-01T12:00:00+00:00</lastmod>')
        ->and(value: mb_substr_count(haystack: $sitemap, needle: '<url>'))
        ->toBe(expected: 5);
});

it(description: 'caches the sitemap for a week', closure: function (): void
{
    Cache::shouldReceive('remember')
        ->once()
        ->withArgs(argsOrClosure: function ($key, $ttl, $callback)
        {
            expect(value: $key)
                ->toBe(expected: 'sitemap')
                ->and(value: $ttl->format('Y-m-d H:i:s'))
                ->toBe(expected: CarbonImmutable::now()->addWeek()->format(format: 'Y-m-d H:i:s'));

            return true;
        })
        ->andReturn(args: 'cached-sitemap-content');

    expect(value: (new GeneratorService)->get())
        ->toBe(expected: 'cached-sitemap-content');
});

it(description: 'returns cached content when available', closure: function (): void
{
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(args: 'pre-cached-sitemap-content');

    expect(value: (new GeneratorService)->get())
        ->toBe(expected: 'pre-cached-sitemap-content')
        ->not->toContain(needle: '<?xml version="1.0" encoding="UTF-8"?>');
});

it(description: 'adds correct URL entries with proper formatting', closure: function (): void
{
    $service    = new GeneratorService;
    $reflection = new ReflectionClass(objectOrClass: $service);

    $startMethod = $reflection->getMethod(name: 'start');
    $startMethod->setAccessible(accessible: true);

    $addMethod = $reflection->getMethod(name: 'add');
    $addMethod->setAccessible(accessible: true);

    $endMethod = $reflection->getMethod(name: 'end');
    $endMethod->setAccessible(accessible: true);

    $startMethod->invoke(object: $service);

    $testUrl = 'https://test.example.com';
    $addMethod->invokeArgs(
        object: $service,
        args: [$testUrl, CarbonImmutable::now(), 0.75, 'weekly']
    );

    expect(value: $endMethod->invoke(object: $service))
        ->toBeString()
        ->toContain(needle: '<loc>' . $testUrl . '</loc>')
        ->toContain(needle: '<lastmod>2025-05-01T12:00:00+00:00</lastmod>')
        ->toContain(needle: '<changefreq>weekly</changefreq>')
        ->toContain(needle: '<priority>0.75</priority>');
});

it(description: 'includes all required pages in the sitemap', closure: function (): void
{
    $service    = new GeneratorService;
    $reflection = new ReflectionClass(objectOrClass: $service);

    $startMethod = $reflection->getMethod(name: 'start');
    $startMethod->setAccessible(accessible: true);

    $pagesMethod = $reflection->getMethod(name: 'pages');
    $pagesMethod->setAccessible(accessible: true);

    $endMethod = $reflection->getMethod(name: 'end');
    $endMethod->setAccessible(accessible: true);

    $startMethod->invoke(object: $service);
    $pagesMethod->invoke(object: $service);

    expect(value: $result = $endMethod->invoke(object: $service))
        ->toBeString()
        ->toContain(needle: '<url>')
        ->toContain(needle: '<loc>')
        ->toContain(needle: '<lastmod>')
        ->toContain(needle: '<changefreq>')
        ->toContain(needle: '<priority>')
        ->and(value: mb_substr_count(haystack: $result, needle: '<url>'))
        ->toBe(expected: 5);
});

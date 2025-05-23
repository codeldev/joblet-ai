<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\PostStatusEnum;
use App\Http\Requests\Sitemap\SitemapRequest;
use App\Models\BlogPost;
use App\Services\Sitemap\GeneratorService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

beforeEach(closure: function (): void
{
    Mockery::close();
});

afterEach(closure: function (): void
{
    Mockery::close();
});

describe(description: 'SitemapRequest', tests: function (): void
{
    it(description: 'extends FormRequest', closure: function (): void
    {
        expect(value: new SitemapRequest)
            ->toBeInstanceOf(class: FormRequest::class);
    });

    it(description: 'authorizes all requests', closure: function (): void
    {
        expect(value: (new SitemapRequest)->authorize())
            ->toBeTrue();
    });

    it(description: 'has empty validation rules', closure: function (): void
    {
        expect(value: (new SitemapRequest)->rules())
            ->toBeArray()
            ->toBeEmpty();
    });

    it(description: 'returns XML response with correct content type', closure: function (): void
    {
        $responseMock = Mockery::mock(
            class: SymfonyResponse::class
        );

        Response::shouldReceive('make')
            ->once()
            ->withAnyArgs()
            ->andReturnUsing(args: function ($content, $status = 200, $headers = []) use ($responseMock)
            {
                expect(value: $content)
                    ->toBeString()
                    ->and(value: $headers)
                    ->toBe(expected: ['Content-Type' => 'application/xml']);

                return $responseMock;
            });

        expect(value: (new SitemapRequest)->__invoke())
            ->toBe(expected: $responseMock);
    });

    it(description: 'uses the GeneratorService to get sitemap content', closure: function (): void
    {
        $reflectionMethod = new ReflectionMethod(
            objectOrMethod: SitemapRequest::class,
            method        : '__invoke'
        );

        $reflectionMethod->setAccessible(accessible: true);

        $methodBody = file_get_contents(
            filename: new ReflectionClass(objectOrClass: SitemapRequest::class)->getFileName()
        );

        expect(value: $methodBody)
            ->toContain(needle: 'GeneratorService')
            ->toContain(needle: '->get()');
    });

    it(description: 'includes blog posts in the sitemap', closure: function (): void
    {
        $generatorService = new GeneratorService();
        $sitemap          = $generatorService->get();

        expect(value: $sitemap)
            ->toBeString()
            ->toContain(needle: '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">')
            ->toContain(needle: '</urlset>');

        $reflectionClass = new ReflectionClass(objectOrClass: GeneratorService::class);
        $methodBody      = file_get_contents(
            filename: $reflectionClass->getFileName()
        );

        expect(value: $methodBody)
            ->toContain(needle: 'posts()')
            ->toContain(needle: 'BlogPost::query()')
            ->toContain(needle: 'PostStatusEnum::PUBLISHED');
    });

    it(description: 'only includes published blog posts in the sitemap', closure: function (): void
    {
        $publishedPost = BlogPost::factory()->create(attributes: [
            'status'       => PostStatusEnum::PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $draftPost = BlogPost::factory()->create(attributes: [
            'status'       => PostStatusEnum::DRAFT,
            'published_at' => null,
        ]);

        expect(value: (new GeneratorService)->get())
            ->toBeString()
            ->toContain(needle: '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">')
            ->toContain(needle: route(name: 'resources.post', parameters: $publishedPost))
            ->not->toContain(needle: route(name: 'resources.post', parameters: $draftPost));
    });

    it(description: 'formats blog post URLs correctly in the sitemap', closure: function (): void
    {
        $published = now()->subDays(value: 5);
        $blogPost  = BlogPost::factory()->create(attributes: [
            'status'       => PostStatusEnum::PUBLISHED,
            'published_at' => $published,
            'slug'         => 'test-blog-post',
        ]);

        expect(value: (new GeneratorService)->get())
            ->toBeString()
            ->toContain(needle: '<loc>' . route(name: 'resources.post', parameters: $blogPost) . '</loc>')
            ->toContain(needle: '<lastmod>' . $published->toIso8601String() . '</lastmod>')
            ->toContain(needle: '<changefreq>monthly</changefreq>')
            ->toContain(needle: '<priority>0.8</priority>');
    });
});

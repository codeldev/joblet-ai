<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Http\Requests\Sitemap\SitemapRequest;
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
});

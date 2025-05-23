<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Support\BlogPostSlugOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\SlugOptions;
use Tests\Classes\Unit\Support\ArrayModel;
use Tests\Classes\Unit\Support\EmptyModel;
use Tests\Classes\Unit\Support\ModelWithTestTitle;
use Tests\Classes\Unit\Support\ModelWithTitle;
use Tests\Classes\Unit\Support\StringableObject;
use Tests\Classes\Unit\Support\TestAppContainer;

if (! function_exists(function: 'app_original'))
{
    function app_original($abstract = null, array $parameters = [])
    {
        global $__app;

        return $__app($abstract, $parameters);
    }
}

beforeEach(closure: function (): void
{
    global $__app;

    if (! isset($__app))
    {
        $__app = function ($abstract = null, $parameters = [])
        {
            $app = TestAppContainer::getInstance();

            if ($abstract === null)
            {
                return $app;
            }

            return $app->$abstract(...$parameters);
        };
    }

    TestAppContainer::getInstance()->setLocale(locale: 'en');
});

afterEach(closure: function (): void
{
    TestAppContainer::getInstance()->setLocale(locale: 'en');
});

describe(description: 'BlogPostSlugOptions', tests: function (): void
{
    test(description: 'it extends base SlugOptions class', closure: function (): void
    {
        expect(value: new BlogPostSlugOptions)
            ->toBeInstanceOf(class: SlugOptions::class);
    });

    test(description: 'withoutStopWords modifies generateSlugFrom callback', closure: function (): void
    {
        $options    = new BlogPostSlugOptions();
        $reflection = new ReflectionClass(objectOrClass: $options);
        $property   = $reflection->getProperty(name: 'generateSlugFrom');

        $property->setAccessible(accessible: true);
        $property->setValue(objectOrValue: $options, value: 'title');
        $options->withoutStopWords();

        $slug = $property->getValue(object: $options);

        expect(value: is_callable(value: $slug))
            ->toBeTrue();
    });

    test(description: 'withoutStopWords handles empty string input', closure: function (): void
    {
        $options    = new BlogPostSlugOptions();
        $reflection = new ReflectionClass(objectOrClass: $options);
        $property   = $reflection->getProperty(name: 'generateSlugFrom');

        $property->setAccessible(accessible: true);
        $property->setValue(objectOrValue: $options, value: fn () => '');
        $options->withoutStopWords();

        $slug  = $property->getValue(object: $options);
        $model = $this->createMock(Model::class);

        expect(value: $slug($model))->toBe(expected: '');
    });

    test(description: 'getFilteredResult removes stop words', closure: function (): void
    {
        $options = new BlogPostSlugOptions();
        $method  = new ReflectionMethod(
            objectOrMethod: $options,
            method: 'getFilteredResult'
        );

        $method->setAccessible(accessible: true);

        expect(value: $method->invokeArgs($options, [['this', 'is', 'a', 'test']]))
            ->toBe(expected: 'test');
    });

    test(description: 'filterWord correctly identifies stop words', closure: function (): void
    {
        $options = new BlogPostSlugOptions();
        $method  = new ReflectionMethod(
            objectOrMethod: $options,
            method: 'filterWord'
        );

        $method->setAccessible(accessible: true);

        expect(value: $method->invokeArgs($options, ['the', ['the', 'and', 'or']]))
            ->toBeFalse()
            ->and(value: $method->invokeArgs($options, ['hello', ['the', 'and', 'or']]))
            ->toBeTrue()
            ->and(value: $method->invokeArgs($options, ['', ['the', 'and', 'or']]))
            ->toBeFalse();
    });

    test(description: 'getStopWords returns array of stop words for current locale', closure: function (): void
    {
        $options = new BlogPostSlugOptions();
        $method  = new ReflectionMethod(
            objectOrMethod: $options,
            method: 'getStopWords'
        );

        $method->setAccessible(accessible: true);

        TestAppContainer::getInstance()
            ->setLocale(locale: 'en');

        expect(value: $method->invoke(object: $options))
            ->toBeArray()
            ->toContain('the', 'and', 'or');
    });

    test(description: 'ensureString converts various types to string', closure: function (): void
    {
        $options = new BlogPostSlugOptions();
        $method  = new ReflectionMethod(
            objectOrMethod: $options,
            method: 'ensureString'
        );

        $method->setAccessible(accessible: true);

        expect(value: $method->invoke($options, null))
            ->toBe(expected: '')
            ->and(value: $method->invoke($options, 'hello'))
            ->toBe(expected: 'hello')
            ->and(value: $method->invoke($options, 123))
            ->toBe(expected: '123')
            ->and(value: $method->invoke($options, 1.23))
            ->toBe(expected: '1.23')
            ->and(value: $method->invoke($options, new StringableObject))
            ->toBe(expected: 'object string')
            ->and(value: $method->invoke($options, ['array']))
            ->toBe(expected: '');
    });

    test(description: 'getFieldValue handles different source config types', closure: function (): void
    {
        $options  = new BlogPostSlugOptions();
        $method   = new ReflectionMethod(
            objectOrMethod: $options,
            method: 'getFieldValue'
        );

        $method->setAccessible(accessible: true);

        expect(value: $method->invokeArgs($options, [new ModelWithTestTitle, 'title']))
            ->toBe(expected: 'Test Title')
            ->and(value: $method->invokeArgs($options, [new EmptyModel, fn ($m) => 'Callable Result']))
            ->toBe(expected: 'Callable Result')
            ->and(value: $method->invokeArgs($options, [new EmptyModel, [fn ($m) => 'Array Callable']]))
            ->toBe(expected: 'Array Callable')
            ->and(value: $method->invokeArgs($options, [new ArrayModel, ['name']]))
            ->toBe(expected: 'Array Attribute')
            ->and(value: $method->invokeArgs($options, [new ModelWithTitle, []]))
            ->toBe(expected: 'Fallback Title');
    });

    test(description: 'getStopWords returns array of stop words for valid locales', closure: function (string $locale): void
    {
        $opts = new BlogPostSlugOptions();
        $app  = TestAppContainer::getInstance();
        $app->setLocale(locale: $locale);

        $method = new ReflectionMethod(
            objectOrMethod: $opts,
            method: 'getStopWords'
        );

        $method->setAccessible(accessible: true);

        $commonWords = ['and', 'the', 'a', 'an', 'in', 'on', 'at'];
        $stopWords   = $method->invoke(object: $opts);

        expect(value: $stopWords)
            ->toBeArray()
            ->not->toBeEmpty()
            ->and(value: count(value: array_intersect($commonWords, $stopWords)))
            ->toBeGreaterThan(expected: 0);
    })->with([
        'english' => 'en',
        'french'  => 'fr',
        'german'  => 'de',
        'spanish' => 'es',
    ]);
});

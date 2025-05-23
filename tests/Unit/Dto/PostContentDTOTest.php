<?php

/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Dto\PostContentDTO;

describe(description: 'PostContentDTO', tests: function (): void
{
    it('can be instantiated with valid parameters', function (): void
    {
        $toc = include base_path(path: 'tests/Fixtures/PostContentDto/valid_params_toc.php');

        $tocHtml = file_get_contents(
            filename: base_path(path: 'tests/Fixtures/PostContentDto/valid_params_toc.html')
        );

        $html = file_get_contents(
            filename: base_path(path: 'tests/Fixtures/PostContentDto/valid_params_html.html')
        );

        $dto = new PostContentDTO(
            toc    : $toc,
            tocHtml: $tocHtml,
            html   : $html
        );

        expect(value: $dto)
            ->toBeInstanceOf(class: PostContentDTO::class)
            ->toc->toBe(expected: $toc)
            ->tocHtml->toBe(expected: $tocHtml)
            ->html->toBe(expected: $html);
    });

    it('can be instantiated with null tocHtml', function (): void
    {
        $toc = include base_path(path: 'tests/Fixtures/PostContentDto/null_params_toc.php');

        $html = file_get_contents(
            filename: base_path(path: 'tests/Fixtures/PostContentDto/null_params_html.html')
        );

        $dto = new PostContentDTO(
            toc    : $toc,
            tocHtml: null,
            html   : $html
        );

        expect(value: $dto)
            ->toBeInstanceOf(class: PostContentDTO::class)
            ->toc->toBe(expected: $toc)
            ->tocHtml->toBeNull()
            ->html->toBe(expected: $html);
    });

    it('can be instantiated with empty toc array', function (): void
    {
        $toc     = [];
        $tocHtml = '<ul></ul>';
        $html    = '<p>Content without headings</p>';

        $dto = new PostContentDTO(
            toc    : $toc,
            tocHtml: $tocHtml,
            html   : $html
        );

        expect(value: $dto)
            ->toBeInstanceOf(class: PostContentDTO::class)
            ->toc->toBe(expected: $toc)
            ->tocHtml->toBe(expected: $tocHtml)
            ->html->toBe(expected: $html);
    });

    it('is a readonly class', function (): void
    {
        $toc = include base_path(path: 'tests/Fixtures/PostContentDto/valid_params_toc.php');

        $tocHtml = file_get_contents(
            filename: base_path(path: 'tests/Fixtures/PostContentDto/valid_params_toc.html')
        );

        $html = file_get_contents(
            filename: base_path(path: 'tests/Fixtures/PostContentDto/valid_params_html.html')
        );

        $dto = new PostContentDTO(
            toc    : $toc,
            tocHtml: $tocHtml,
            html   : $html
        );

        $reflection = new ReflectionClass(objectOrClass: $dto);

        expect(value: $reflection->isReadOnly())
            ->toBeTrue();
    });

    it('has correctly typed properties', function (): void
    {
        $reflection      = new ReflectionClass(objectOrClass: PostContentDTO::class);
        $tocProperty     = $reflection->getProperty(name: 'toc');
        $tocHtmlProperty = $reflection->getProperty(name: 'tocHtml');
        $htmlProperty    = $reflection->getProperty(name: 'html');

        expect(value: $tocProperty->getType()->getName())
            ->toBe(expected: 'array')
            ->and(value: $tocHtmlProperty->getType()->allowsNull())
            ->toBeTrue()
            ->and(value: $tocHtmlProperty->getType()->getName())
            ->toBe(expected: 'string')
            ->and(value: $htmlProperty->getType()->getName())
            ->toBe(expected: 'string');
    });
});

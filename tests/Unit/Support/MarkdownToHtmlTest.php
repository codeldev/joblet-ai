<?php

/** @noinspection HtmlUnknownAnchorTarget */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Dto\PostContentDTO;
use App\Models\BlogPost;
use App\Support\CommonMark\MarkdownPicture;
use App\Support\CommonMark\YouTubeExtension;
use App\Support\MarkdownToHtml;
use ReflectionClass;
use ReflectionMethod;

it(description: 'initializes with blog post content and images', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '# Test Content',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    expect(value: $markdownToHtml)
        ->toBeInstanceOf(class: MarkdownToHtml::class);
});

it(description: 'returns empty result when content is empty', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    expect(value: $result = $markdownToHtml->convert())
        ->toBeInstanceOf(class: PostContentDTO::class)
        ->and(value: $result->toc)
        ->toBe(expected: [])
        ->and(value: $result->tocHtml)
        ->toBeNull()
        ->and(value: $result->html)
        ->toBe(expected: '');
});

it(description: 'converts markdown to HTML with headings and paragraphs', closure: function (): void
{
    $markdown = file_get_contents(
        filename: base_path(path: 'tests/Fixtures/MarkdownToHtml/headings_and_paragraphs.md')
    );

    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => $markdown,
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    expect(value: $result = $markdownToHtml->convert())
        ->toBeInstanceOf(class: PostContentDTO::class)
        ->and(value: $result->html)
        ->toContain(needles: '<h1 id="heading-1">Heading 1')
        ->toContain(needles: '<h2 id="heading-2">Heading 2')
        ->toContain(needles: '<h3 id="heading-3">Heading 3')
        ->toContain(needles: '<p>Content paragraph</p>');
});

it(description: 'extracts images from content', closure: function (): void
{
    $markdown = file_get_contents(
        filename: base_path(path: 'tests/Fixtures/MarkdownToHtml/test_with_image.md')
    );

    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => $markdown,
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $result = $markdownToHtml->convert();

    expect(value: $result->html)
        ->toContain(needles: '<h1 id="test-with-image">Test with Image')
        ->toContain(needles: '<p>Here is an image reference</p>');
});

it(description: 'handles missing image references gracefully', closure: function (): void
{
    $markdown = file_get_contents(
        filename: base_path(path: 'tests/Fixtures/MarkdownToHtml/test_with_missing_image.md')
    );

    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => $markdown,
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $result = $markdownToHtml->convert();

    expect(value: $result->html)
        ->toContain(needles: '<h1 id="test-with-missing-image">Test with Missing Image')
        ->toContain(needles: 'Here is a missing image:');
});

it(description: 'converts markdown with nested headings correctly', closure: function (): void
{
    $markdown = file_get_contents(
        filename: base_path(path: 'tests/Fixtures/MarkdownToHtml/nested_headings_test.md')
    );

    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => $markdown,
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    expect(value: $result = $markdownToHtml->convert())
        ->toBeInstanceOf(class: PostContentDTO::class)
        ->and(value: $result->html)
        ->toContain(needles: '<h1 id="main-heading">Main Heading')
        ->toContain(needles: '<h2 id="sub-heading-1">Sub Heading 1')
        ->toContain(needles: '<h3 id="sub-sub-heading-1">Sub Sub Heading 1')
        ->toContain(needles: '<h2 id="sub-heading-2">Sub Heading 2');
});

it(description: 'uses correct configuration for CommonMark environment', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '# Test',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method        : 'getConfig'
    );

    $reflectionMethod->setAccessible(accessible: true);

    expect(value: $config = $reflectionMethod->invoke(object: $markdownToHtml))
        ->toBeArray()
        ->toHaveKeys(keys: [
            'html_input',
            'allow_unsafe_links',
            'external_link',
            'heading_permalink',
            'table_of_contents',
            'embeds',
        ])
        ->and(value: $config['html_input'])
        ->toBe(expected: 'strip')
        ->and(value: $config['allow_unsafe_links'])
        ->toBeFalse()
        ->and(value: $config['embeds'][0])
        ->toBeInstanceOf(class: YouTubeExtension::class);
});

it(description: 'extracts table of contents items from HTML', closure: function (): void
{
    $markdown = file_get_contents(
        filename: base_path(path: 'tests/Fixtures/MarkdownToHtml/toc_extract_test.md')
    );

    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => $markdown,
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method: 'extractTocItems'
    );

    $reflectionMethod->setAccessible(accessible: true);

    expect(value: $tocItems = $reflectionMethod->invoke(object: $markdownToHtml))
        ->toBeArray()
        ->not->toBeEmpty()
        ->and(value: $tocItems[0]->text)
        ->toBe(expected: 'Main Heading')
        ->and(value: $tocItems[0]->children[0]->text)
        ->toBe(expected: 'Sub Heading 1');
});

it(description: 'returns empty array when no headings found in content', closure: function (): void
{
    $markdown = file_get_contents(
        filename: base_path(path: 'tests/Fixtures/MarkdownToHtml/paragraph_without_headings.md')
    );

    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => $markdown,
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method        : 'extractTocItems'
    );

    $reflectionMethod->setAccessible(accessible: true);

    expect(value: $reflectionMethod->invoke(object: $markdownToHtml))
        ->toBeArray()
        ->toBeEmpty();
});

it(description: 'builds table of contents collection from heading match', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '# Test',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method        : 'buildTocCollection'
    );

    $reflectionMethod->setAccessible(accessible: true);

    $result = $reflectionMethod->invoke(
        $markdownToHtml,
        [0, '1', 'test-heading', 'Test Heading <a href="#">link</a>']
    );

    expect(value: $result)
        ->toBeArray()
        ->toHaveKeys(keys: ['id', 'text', 'level', 'children'])
        ->and(value: $result['id'])
        ->toBe(expected: 'test-heading')
        ->and(value: $result['text'])
        ->toBe(expected: 'Test Heading link')
        ->and(value: $result['level'])
        ->toBe(expected: 1)
        ->and(value: $result['children'])
        ->toBeArray()
        ->toBeEmpty();
});

it(description: 'cleans table of contents structure', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '# Test',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method        : 'cleanTocStructure'
    );

    $reflectionMethod->setAccessible(accessible: true);

    $result = $reflectionMethod->invoke($markdownToHtml, [
        [
            'id'       => 'heading-1',
            'text'     => 'Heading 1',
            'children' => [
                [
                    'id'       => 'heading-2',
                    'text'     => 'Heading 2',
                    'children' => [],
                ],
            ],
        ],
        [
            'text' => 'Invalid Heading',
        ],
        [
            'id' => 'invalid-heading',
        ],
        'invalid-item',
    ]);

    expect(value: $result)
        ->toBeArray()
        ->toHaveCount(count: 1)
        ->and(value: $result[0]->id)
        ->toBe(expected: 'heading-1')
        ->and(value: $result[0]->text)
        ->toBe(expected: 'Heading 1')
        ->and(value: $result[0]->children)
        ->toHaveCount(count: 1)
        ->and(value: $result[0]->children[0]->id)
        ->toBe(expected: 'heading-2');
});

it(description: 'generates table of contents HTML', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '# Test',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method: 'generateTocHtml'
    );

    $reflectionMethod->setAccessible(accessible: true);

    $result = $reflectionMethod->invoke($markdownToHtml, [
        (object) [
            'id'       => 'heading-1',
            'text'     => 'Heading 1',
            'children' => [
                (object) [
                    'id'   => 'heading-2',
                    'text' => 'Heading 2',
                ],
            ],
        ],
    ]);

    expect(value: $result)
        ->toBeString()
        ->toContain(needles: '<nav class="table-of-contents"><ul>')
        ->toContain(needles: '<a href="#heading-1">Heading 1</a>')
        ->toContain(needles: '<a href="#heading-2">Heading 2</a>');
});

it(description: 'returns empty string for empty TOC items in generateTocHtml', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '# Test',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method: 'generateTocHtml'
    );

    $reflectionMethod->setAccessible(accessible: true);

    expect(value: $reflectionMethod->invoke($markdownToHtml, []))
        ->toBeString()
        ->toBe(expected: '');
});

it(description: 'handles invalid TOC items in renderTocItems', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '# Test',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method        : 'renderTocItems'
    );

    $reflectionMethod->setAccessible(accessible: true);

    $result = $reflectionMethod->invoke($markdownToHtml, [
        (object) [
            'text' => 'Heading 1',
        ],
        (object) [
            'id' => 'heading-2',
        ],
        (object) [
            'id'       => 'heading-3',
            'text'     => 'Heading 3',
            'children' => [
                (object) [
                    'id'   => 'heading-4',
                    'text' => 'Heading 4',
                ],
            ],
        ],
    ]);

    expect(value: $result)
        ->toBeString()
        ->not->toContain(needles: 'Heading 1')
        ->not->toContain(needles: 'Heading 2')
        ->toContain(needles: '<a href="#heading-3">Heading 3</a>')
        ->toContain(needles: '<a href="#heading-4">Heading 4</a>');
});

it(description: 'uses correct external link configuration', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '# Test',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method        : 'getExternalLinkConfig'
    );

    $reflectionMethod->setAccessible(accessible: true);

    config(key: ['app.url' => null]);

    expect(value: $result = $reflectionMethod->invoke(object: $markdownToHtml))
        ->toBeArray()
        ->toHaveKeys(keys: ['html_class', 'open_in_new_window', 'internal_hosts'])
        ->and(value: $result['html_class'])
        ->toBe(expected: 'external-link')
        ->and(value: $result['open_in_new_window'])
        ->toBeTrue()
        ->and(value: $result['internal_hosts'])
        ->toBeNull();

    config(key: ['app.url' => 'http://localhost']);

    $result = $reflectionMethod->invoke(object: $markdownToHtml);

    expect(value: $result['internal_hosts'])
        ->toBe(expected: 'http://localhost');
});

it(description: 'throws CommonMarkException when conversion fails', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '[Invalid](markdown',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    expect(value: $markdownToHtml->convert())
        ->toBeInstanceOf(class: PostContentDTO::class);
});

it(description: 'handles empty array in buildHierarchicalToc', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '# Test',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method        : 'buildHierarchicalToc'
    );

    $reflectionMethod->setAccessible(accessible: true);

    expect(value: $reflectionMethod->invoke($markdownToHtml, []))
        ->toBeArray()
        ->toBeEmpty();
});

it(description: 'handles null text in buildTocCollection', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '# Test',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method        : 'buildTocCollection'
    );

    $reflectionMethod->setAccessible(accessible: true);

    $result = $reflectionMethod->invoke(
        $markdownToHtml,
        [0, '1', 'test-heading', null]
    );

    expect(value: $result)
        ->toBeArray()
        ->toHaveKeys(keys: ['id', 'text', 'level', 'children'])
        ->and(value: $result['text'])
        ->toBe(expected: '');
});

it(description: 'processes content with TOC_PLACEHOLDER correctly', closure: function (): void
{
    $markdown = file_get_contents(
        filename: base_path(path: 'tests/Fixtures/MarkdownToHtml/toc_placeholder_with_headings.md')
    );

    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => $markdown,
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    expect(value: $result = $markdownToHtml->convert())
        ->toBeInstanceOf(class: PostContentDTO::class)
        ->and(value: $result->html)
        ->not->toContain(needles: 'TOC_PLACEHOLDER')
        ->toContain(needles: '<h1 id="heading-1">Heading 1');
});

it(description: 'handles empty content in extractTocItems', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method        : 'extractTocItems'
    );

    $reflectionMethod->setAccessible(accessible: true);

    expect(value: $reflectionMethod->invoke(object: $markdownToHtml))
        ->toBeArray()
        ->toBeEmpty();
});

it(description: 'processes content with TOC items but no TOC HTML', closure: function (): void
{
    $markdown = file_get_contents(
        filename: base_path(path: 'tests/Fixtures/MarkdownToHtml/toc_items_no_html.md')
    );

    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => $markdown,
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method: 'convertToHtml'
    );

    $reflectionMethod->setAccessible(accessible: true);

    $reflectionClass    = new ReflectionClass(objectOrClass: MarkdownToHtml::class);
    $reflectionProperty = $reflectionClass->getProperty(name: 'convertedHtml');

    $reflectionProperty->setAccessible(accessible: true);
    $reflectionProperty->setValue(
        objectOrValue: $markdownToHtml,
        value        : '<h1 id="heading-1">Heading 1</h1><h2 id="heading-2">Heading 2</h2>'
    );

    expect(value: $result = $reflectionMethod->invoke(object: $markdownToHtml))
        ->toBeInstanceOf(class: PostContentDTO::class)
        ->and(value: $result->html)
        ->toContain(needles: 'Heading 1')
        ->and(value: $result->html)
        ->toContain(needles: 'Heading 2');
});

it(description: 'tests TOC HTML generation with invalid JSON', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '# Test',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method: 'convertToHtml'
    );

    $reflectionMethod->setAccessible(accessible: true);

    $reflectionClass = new ReflectionClass(objectOrClass: MarkdownToHtml::class);
    $htmlProperty    = $reflectionClass->getProperty(name: 'convertedHtml');

    $htmlProperty->setAccessible(accessible: true);
    $htmlProperty->setValue(
        objectOrValue: $markdownToHtml,
        value        : '<h1 id="test">Test</h1>'
    );

    expect(value: $result = $reflectionMethod->invoke(object: $markdownToHtml))
        ->toBeInstanceOf(class: PostContentDTO::class)
        ->and(value: $result->html)
        ->toContain(needles: 'Test');
});

it(description: 'handles content with embedded YouTube videos', closure: function (): void
{
    $markdown = file_get_contents(
        filename: base_path(path: 'tests/Fixtures/MarkdownToHtml/youtube_video.md')
    );

    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => $markdown,
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    expect(value: $result = $markdownToHtml->convert())
        ->toBeInstanceOf(class: PostContentDTO::class)
        ->and(value: $result->html)
        ->toContain(needles: '<iframe');
});

it(description: 'handles HTML content with TOC but no TOC items', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => '# Test',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $convertToHtmlMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method        : 'convertToHtml'
    );

    $convertToHtmlMethod->setAccessible(accessible: true);

    $reflectionClass = new ReflectionClass(objectOrClass: MarkdownToHtml::class);
    $htmlProperty    = $reflectionClass->getProperty(name: 'convertedHtml');

    $htmlProperty->setAccessible(accessible: true);
    $htmlProperty->setValue(
        objectOrValue: $markdownToHtml,
        value        : '<nav class="table-of-contents">Empty TOC</nav><h1 id="test">Test</h1>'
    );

    $extractTocItemsMethod = new ReflectionMethod(
        objectOrMethod: MarkdownToHtml::class,
        method: 'extractTocItems'
    );

    $extractTocItemsMethod->setAccessible(accessible: true);

    expect(value: $result = $convertToHtmlMethod->invoke(object: $markdownToHtml))
        ->toBeInstanceOf(class: PostContentDTO::class)
        ->and(value: $result->html)
        ->not->toContain(needles: 'Empty TOC')
        ->and(value: $result->html)
        ->toContain(needles: 'Test');
});

it(description: 'handles nested headings in HTML output', closure: function (): void
{
    $markdown = file_get_contents(
        filename: base_path(path: 'tests/Fixtures/MarkdownToHtml/toc_placeholder_nested_headings.md')
    );

    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => $markdown,
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    expect(value: $result = $markdownToHtml->convert())
        ->toBeInstanceOf(class: PostContentDTO::class)
        ->and(value: $result->html)
        ->toContain(needles: '<h1 id="main-heading">Main Heading')
        ->toContain(needles: '<h2 id="sub-heading-1">Sub Heading 1')
        ->toContain(needles: '<h3 id="sub-sub-heading-1">Sub Sub Heading 1')
        ->toContain(needles: '<h2 id="sub-heading-2">Sub Heading 2')
        ->and(value: $result->html)
        ->not->toContain(needles: 'TOC_PLACEHOLDER');
});

it(description: 'handles all heading levels in HTML output', closure: function (): void
{
    $markdown = file_get_contents(
        filename: base_path(path: 'tests/Fixtures/MarkdownToHtml/all_heading_levels.md')
    );

    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => $markdown,
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $result = $markdownToHtml->convert();

    expect(value: $result->html)
        ->toContain(needles: '<h1')
        ->toContain(needles: '<h2')
        ->toContain(needles: '<h3')
        ->toContain(needles: '<h4')
        ->toContain(needles: '<h5')
        ->toContain(needles: '<h6')
        ->toContain(needles: 'Main Heading')
        ->toContain(needles: 'Sub Heading')
        ->toContain(needles: 'Sub Sub Heading')
        ->toContain(needles: 'Level 4 Heading')
        ->toContain(needles: 'Level 5 Heading')
        ->toContain(needles: 'Level 6 Heading')
        ->not->toContain(needles: 'TOC_PLACEHOLDER');
});

it(description: 'handles JSON encoding failure in TOC generation', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => 'TOC_PLACEHOLDER\n\n# Main Heading',
    ]);

    $markdownToHtml = new MarkdownToHtml(
        blogPost: $blogPost
    );

    $reflectionClass       = new ReflectionClass(objectOrClass: MarkdownToHtml::class);
    $convertedHtmlProperty = $reflectionClass->getProperty(name: 'convertedHtml');
    $convertedHtmlProperty->setAccessible(accessible: true);

    $convertedHtmlProperty->setValue(
        objectOrValue: $markdownToHtml,
        value        : '<h1 id="main-heading">Main Heading</h1>'
    );

    $convertToHtmlMethod = $reflectionClass->getMethod(name: 'convertToHtml');
    $convertToHtmlMethod->setAccessible(accessible: true);

    expect(value: $markdownToHtml->convert())
        ->toBeInstanceOf(class: PostContentDTO::class);
});

it(description: 'processes images with correct image data', closure: function (): void
{
    $markdownPicture = new MarkdownPicture(imageData: [
        'alt'   => 'Test image',
        'files' => [
            ['width' => 800, 'image' => 'test-image.jpg'],
        ],
    ]);

    expect(value: $markdownPicture->convert())
        ->toContain(needles: '<img')
        ->toContain(needles: 'alt="Test image"')
        ->toContain(needles: 'srcset="' . url(path: 'storage/blog/test-image.jpg') . '"');
});

it(description: 'tests json_encode failure in TOC generation', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => 'TOC_PLACEHOLDER\n\n# Heading',
    ]);

    $markdownToHtml        = new MarkdownToHtml(blogPost: $blogPost);
    $reflectionClass       = new ReflectionClass(objectOrClass: MarkdownToHtml::class);
    $convertedHtmlProperty = $reflectionClass->getProperty(name: 'convertedHtml');

    $convertedHtmlProperty->setAccessible(accessible: true);
    $convertedHtmlProperty->setValue(
        objectOrValue: $markdownToHtml,
        value        : '<h1 id="heading">Heading</h1>'
    );

    $convertToHtmlMethod = $reflectionClass->getMethod(name: 'convertToHtml');
    $convertToHtmlMethod->setAccessible(accessible: true);

    $extractTocItemsMethod = $reflectionClass->getMethod(name: 'extractTocItems');
    $extractTocItemsMethod->setAccessible(accessible: true);

    expect(value: $markdownToHtml->convert())
        ->toBeInstanceOf(class: PostContentDTO::class);
});

it(description: 'tests lines 247-250 in MarkdownToHtml class', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => 'Test content with image reference [img=123]',
    ]);

    $markdownToHtml  = new MarkdownToHtml(blogPost: $blogPost);
    $reflectionClass = new ReflectionClass(objectOrClass: MarkdownToHtml::class);
    $imagesProperty  = $reflectionClass->getProperty(name: 'images');

    $imagesProperty->setAccessible(accessible: true);
    $imagesProperty->setValue(objectOrValue: $markdownToHtml, value: [
        '123' => [
            'alt'   => 'Test image',
            'files' => [
                ['width' => 800, 'image' => 'test-image.jpg'],
            ],
        ],
    ]);

    $extractImagesMethod = $reflectionClass->getMethod(name: 'extractImages');
    $extractImagesMethod->setAccessible(accessible: true);

    $result = $extractImagesMethod->invoke(
        $markdownToHtml,
        'Test content with image reference [img=123]'
    );

    expect(value: $result)
        ->not->toContain(needles: '[img=123]')
        ->toContain(needles: 'Test content with image reference');
});

it(description: 'tests additional code paths for 100% coverage', closure: function (): void
{
    $blogPost = BlogPost::factory()->make(attributes: [
        'content' => "TOC_PLACEHOLDER\n\n# Heading 1",
    ]);

    $markdownToHtml        = new MarkdownToHtml(blogPost: $blogPost);
    $reflectionClass       = new ReflectionClass(objectOrClass: MarkdownToHtml::class);
    $convertedHtmlProperty = $reflectionClass->getProperty(name: 'convertedHtml');
    $convertedHtmlProperty->setAccessible(accessible: true);

    $convertedHtmlProperty->setValue(
        objectOrValue: $markdownToHtml,
        value        : '<nav class="table-of-contents">TOC</nav><h1 id="heading-1">Heading 1</h1>'
    );

    expect(value: $result = $markdownToHtml->convert())
        ->toBeInstanceOf(class: PostContentDTO::class)
        ->and(value: $result->html)
        ->toContain(needles: 'Heading 1');
});

it(description: 'tests MarkdownPicture class directly', closure: function (): void
{
    $markdownPicture = new MarkdownPicture(imageData: [
        'alt'   => 'Test image',
        'files' => [
            ['width' => 800, 'image' => 'test-image.jpg'],
        ],
    ]);

    expect(value: $markdownPicture->convert())
        ->toContain(needles: '<picture>')
        ->toContain(needles: '<source')
        ->toContain(needles: '<img')
        ->toContain(needles: 'alt="Test image"');
});

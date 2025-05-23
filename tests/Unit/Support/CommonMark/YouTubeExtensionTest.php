<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

namespace Tests\Unit\Support\CommonMark;

use App\Support\CommonMark\YouTubeExtension;
use League\CommonMark\Util\HtmlElement;
use Ueberdosis\CommonMark\Embed;
use Ueberdosis\CommonMark\Services\YouTube;

it(description: 'renders YouTube embed as iframe with correct attributes', closure: function (): void
{
    $youtubeExtension = new YouTubeExtension();

    $embed = mock(args: Embed::class);
    $embed->shouldReceive(methodNames: 'getUrl')
        ->once()
        ->andReturn('https://www.youtube.com/watch?v=dQw4w9WgXcQ');

    expect(value: $result = $youtubeExtension->render(node: $embed))
        ->toBeInstanceOf(class: HtmlElement::class)
        ->and(value: $result->getTagName())->toBe(expected: 'iframe')
        ->and(value: $result->getAttribute(key: 'src'))
        ->toBe(expected: 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ')
        ->and(value: $result->getAttribute(key: 'width'))
        ->toBe(expected: '560')
        ->and(value: $result->getAttribute(key: 'height'))
        ->toBe(expected: '315')
        ->and(value: $result->getAttribute(key: 'frameborder'))
        ->toBe(expected: '0')
        ->and(value: $result->getAttribute(key: 'allow'))
        ->toBe(expected: 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture')
        ->and(value: $result->getAttribute(key: 'referrerpolicy'))
        ->toBe(expected: 'strict-origin-when-cross-origin')
        ->and(value: $result->getAttribute(key: 'allowfullscreen'))
        ->toBe(expected: '');
});

it(description: 'handles YouTube URL with video ID correctly', closure: function (): void
{
    $youtubeExtension = new YouTubeExtension();

    $embed = mock(args: Embed::class);
    $embed->shouldReceive(methodNames: 'getUrl')
        ->once()
        ->andReturn('https://youtu.be/dQw4w9WgXcQ');

    $result = $youtubeExtension->render(node: $embed);

    expect(value: $result->getAttribute(key: 'src'))
        ->toBe(expected: 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ');
});

it(description: 'handles YouTube URL with parameters correctly', closure: function (): void
{
    $youtubeExtension = new YouTubeExtension();

    $embed = mock(args: Embed::class);
    $embed->shouldReceive(methodNames: 'getUrl')
        ->once()
        ->andReturn('https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=30s');

    $result = $youtubeExtension->render(node: $embed);

    expect(value: $result->getAttribute(key: 'src'))
        ->toBe(expected: 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ');
});

it(description: 'handles invalid YouTube URL gracefully', closure: function (): void
{
    $youtubeExtension = new YouTubeExtension();

    $embed = mock(args: Embed::class);
    $embed->shouldReceive(methodNames: 'getUrl')
        ->once()
        ->andReturn('https://example.com/not-a-youtube-url');

    $result = $youtubeExtension->render(node: $embed);

    expect(value: $result->getAttribute(key: 'src'))
        ->toBe(expected: 'https://www.youtube-nocookie.com/embed/');
});

it(description: 'uses privacy-enhanced URL with youtube-nocookie.com', closure: function (): void
{
    $youtubeExtension = new YouTubeExtension();

    $embed = mock(args: Embed::class);
    $embed->shouldReceive(methodNames: 'getUrl')
        ->once()
        ->andReturn('https://www.youtube.com/watch?v=dQw4w9WgXcQ');

    $result = $youtubeExtension->render(node: $embed);

    expect(value: $result->getAttribute(key: 'src'))
        ->toContain(needles: 'youtube-nocookie.com')
        ->not->toContain(needles: 'youtube.com');
});

it(description: 'properly extends the base YouTube service', closure: function (): void
{
    expect(value: new YouTubeExtension)
        ->toBeInstanceOf(class: YouTube::class);
});

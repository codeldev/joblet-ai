<?php

/** @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection */

declare(strict_types=1);

namespace App\Support;

use App\Dto\PostContentDTO;
use App\Enums\BlogImageTypeEnum;
use App\Models\BlogPost;
use App\Support\CommonMark\MarkdownPicture;
use App\Support\CommonMark\YouTubeExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Renderer\HtmlDecorator;
use stdClass;
use Ueberdosis\CommonMark\Embed;
use Ueberdosis\CommonMark\EmbedExtension;
use Ueberdosis\CommonMark\EmbedRenderer;

final class MarkdownToHtml
{
    private readonly string $content;

    /** @var array<string, array{alt: string, files: array<int, array{width: int, image: string}>}> */
    private array $images;

    private ?string $convertedHtml = null;

    public function __construct(BlogPost $blogPost)
    {
        $this->content = $blogPost->content;

        $contentImages = $blogPost->images()
            ->where('type', BlogImageTypeEnum::CONTENT)->get()
            ->mapWithKeys(callback: fn ($image) => [
                $image->id => [
                    'alt'   => $image->description,
                    'files' => $image->files !== null ? (array) $image->files : [],
                ],
            ])->toArray();

        /** @var array<string, array{alt: string, files: array<int, array{width: int, image: string}>}> $contentImages */
        $this->images = $contentImages;
    }

    /** @throws CommonMarkException */
    public function convert(): PostContentDTO
    {
        if (! notEmpty(value: $this->content))
        {
            return $this->createResult();
        }

        $this->convertedHtml = $this->convertMarkdown();

        return $this->convertToHtml();
    }

    /**  @param array<int, mixed> $toc */
    private function createResult(array $toc = [], ?string $tocHtml = null, string $html = ''): PostContentDTO
    {
        return new PostContentDTO(
            toc    : $toc,
            tocHtml: $tocHtml,
            html   : $html,
        );
    }

    /** @throws CommonMarkException */
    private function convertToHtml(): PostContentDTO
    {
        $html      = $this->convertedHtml ?? '';
        $cleanHtml = str_replace(
            search : ['<p>TOC_PLACEHOLDER</p>', 'TOC_PLACEHOLDER'],
            replace: '',
            subject: $html
        );

        if (preg_match(
            '/<nav class="table-of-contents">(.*?)<\/nav>/s',
            $cleanHtml,
            $matches
        ))
        {
            $htmlContent = str_replace(
                search: $matches[0],
                replace: '',
                subject: $cleanHtml
            );

            $tocItems = $this->extractTocItems();

            return $this->createResult(
                toc    : $tocItems,
                tocHtml: $matches[0],
                html   : $htmlContent
            );
        }

        $tocItems = $this->extractTocItems();

        if (! notEmpty(value: $tocItems))
        {
            /** @var array<int, object> $objectTocItems */
            $objectTocItems = array_map(
                callback: static fn ($item): stdClass => (object) $item,
                array   : $tocItems
            );

            $tocHtml = $this->generateTocHtml(tocItems: $objectTocItems);

            return $this->createResult(
                toc    : $tocItems,
                tocHtml: $tocHtml,
                html   : $cleanHtml
            );
        }

        return $this->createResult(
            html: $cleanHtml
        );
    }

    /**
     * @return array<int, mixed>
     *
     * @throws CommonMarkException
     */
    private function extractTocItems(): array
    {
        $html = $this->convertedHtml ?? $this->convertMarkdown();

        preg_match_all(
            '/<h([1-6])\s+id="([^"]+)"[^>]*>(.*?)<\/h\1>/s',
            $html,
            $matches,
            PREG_SET_ORDER
        );

        if ($matches === [])
        {
            return [];
        }

        $flatItems = [];

        foreach ($matches as $match)
        {
            $flatItems[] = $this->buildTocCollection(match: $match);
        }

        return $this->buildHierarchicalToc(items: $flatItems);
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, mixed>
     */
    private function buildHierarchicalToc(array $items): array
    {
        if (! notEmpty(value: $items))
        {
            return [];
        }

        $result = [];
        $stack  = [];

        foreach ($items as $item)
        {
            $item = (array) $item;

            $item['children'] = [];

            while ($stack !== [] && $item['level'] <= end($stack)['level'])
            {
                array_pop($stack);
            }

            if (! notEmpty(value: $stack))
            {
                $result[] = $item;
                $stack[]  = &$result[count(value: $result) - 1];
            }
            else
            {
                $parent = &$stack[count(value: $stack) - 1];

                $parent['children'][] = $item;
                $stack[]              = &$parent['children'][count(value: $parent['children']) - 1];
            }
        }

        return $this->cleanTocStructure(items: $result);
    }

    /**
     * @param  array<int, string>  $match
     * @return array<string, mixed>
     */
    private function buildTocCollection(array $match): array
    {
        $text = preg_replace(
            pattern    : '/<a[^>]*>.*?<\/a>/s',
            replacement: '',
            subject    : strip_tags(string: $match[3] ?? '')
        );

        $cleanText = $text !== null ? mb_trim(string: $text) : '';

        return [
            'id'       => $match[2] ?? '',
            'text'     => $cleanText,
            'level'    => (int) ($match[1] ?? 1),
            'children' => [],
        ];
    }

    private function extractImages(string $content): string
    {
        preg_match_all('/\[img=([0-9a-z\-]+)]/i', $content, $matches);

        if (empty($matches[1]))
        {
            return $content;
        }

        foreach ($matches[1] as $imageId)
        {
            $replaceWith = '';

            if (array_key_exists(key: $imageId, array: $this->images))
            {
                $imageData   = $this->images[$imageId];
                $replaceWith = new MarkdownPicture(
                    imageData: $imageData
                )->convert();
            }

            $content = str_replace(
                search : "[img={$imageId}]",
                replace: $replaceWith,
                subject: $content
            );
        }

        return $content;
    }

    /**  @throws CommonMarkException  */
    private function convertMarkdown(): string
    {
        $environment = new Environment(
            config: $this->getConfig()
        );

        $environment->addExtension(
            extension: new CommonMarkCoreExtension
        );

        $environment->addExtension(
            extension: new HeadingPermalinkExtension
        );

        $environment->addExtension(
            extension: new TableOfContentsExtension
        );

        $environment->addExtension(
            extension: new ExternalLinkExtension
        );

        $environment->addExtension(
            extension: new EmbedExtension
        );

        $environment->addRenderer(
            nodeClass : Embed::class,
            renderer  : new HtmlDecorator(
                inner     : new EmbedRenderer(),
                tag       : 'div',
                attributes: ['class' => 'video-content']
            )
        );

        $converter = new MarkdownConverter(environment: $environment);
        $input     = $this->content;

        if (notEmpty(value: $input))
        {
            $input = "TOC_PLACEHOLDER\n\n" . $input;
        }

        $content = $converter
            ->convert(input: $input)
            ->getContent();

        return $this->extractImages($content);
    }

    /**
     * @param  array<int, object>  $tocItems
     */
    private function generateTocHtml(array $tocItems): string
    {
        if (! notEmpty(value: $tocItems))
        {
            return '';
        }

        $html = '<nav class="table-of-contents"><ul>';
        $html .= $this->renderTocItems(items: $tocItems);

        return $html . '</ul></nav>';
    }

    /** @param array<int, object> $items */
    private function renderTocItems(array $items): string
    {
        $html = '';

        foreach ($items as $item)
        {
            if (! isset($item->id))
            {
                continue;
            }
            if (! isset($item->text))
            {
                continue;
            }
            /** @var string $itemId */
            $itemId = $item->id;

            /** @var string $itemText */
            $itemText = $item->text;

            $html .= '<li>';
            $html .= '<a href="#' . $itemId . '">' . $itemText . '</a>';

            if (! empty($item->children))
            {
                /** @var array<int, object> $children */
                $children = $item->children;

                $html .= '<ul>';
                $html .= $this->renderTocItems(items: $children);
                $html .= '</ul>';
            }

            $html .= '</li>';
        }

        return $html;
    }

    /**
     * @param  array<mixed, mixed>  $items
     * @return array<int, object>
     */
    private function cleanTocStructure(array $items): array
    {
        $result = [];

        foreach ($items as $item)
        {
            if (! is_array(value: $item))
            {
                continue;
            }
            if (! isset($item['id']))
            {
                continue;
            }
            if (! isset($item['text']))
            {
                continue;
            }
            /** @var string $itemId */
            $itemId = $item['id'];

            /** @var string $itemText */
            $itemText = $item['text'];

            $cleanItem = (object) [
                'id'   => $itemId,
                'text' => $itemText,
            ];

            if (isset($item['children'])
                && is_array(value: $item['children'])
                && $item['children'] !== []
            ) {
                $childItems = $this->cleanTocStructure(items: $item['children']);

                if ($childItems !== [])
                {
                    $cleanItem->children = $childItems;
                }
            }

            $result[] = $cleanItem;
        }

        return $result;
    }

    /** @return array<string, mixed> */
    private function getConfig(): array
    {
        return [
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
            'external_link'      => $this->getExternalLinkConfig(),
            'heading_permalink'  => $this->getPermaLinkConfig(),
            'table_of_contents'  => $this->getTocConfig(),
            'embeds'             => [new YouTubeExtension],
        ];
    }

    /** @return array<string, mixed> */
    private function getExternalLinkConfig(): array
    {
        $appUrl   = config(key: 'app.url');
        $internal = is_string(value: $appUrl)
            ? $appUrl
            : null;

        return [
            'html_class'         => 'external-link',
            'open_in_new_window' => true,
            'internal_hosts'     => $internal,
        ];
    }

    /** @return array<string, mixed> */
    private function getTocConfig(): array
    {
        return [
            'html_class'        => 'table-of-contents',
            'position'          => 'placeholder',
            'placeholder'       => 'TOC_PLACEHOLDER',
            'min_heading_level' => 1,
            'max_heading_level' => 3,
            'normalize'         => 'relative',
            'style'             => 'bullet',
        ];
    }

    /** @return array<string,mixed> */
    private function getPermaLinkConfig(): array
    {
        return [
            'html_class'          => '',
            'id_prefix'           => '',
            'apply_id_to_heading' => true,
            'min_heading_level'   => 1,
            'max_heading_level'   => 3,
            'fragment_prefix'     => '',
            'symbol'              => '',
            'insert'              => 'after',
        ];
    }
}

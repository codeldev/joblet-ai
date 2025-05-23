<?php

declare(strict_types=1);

namespace App\Support\CommonMark;

use League\CommonMark\Util\HtmlElement;
use Override;
use Ueberdosis\CommonMark\Embed;
use Ueberdosis\CommonMark\Services\YouTube as YouTubeService;

final class YouTubeExtension extends YouTubeService
{
    #[Override]
    public function render(Embed $node): HtmlElement
    {
        return new HtmlElement(
            tagName   : 'iframe',
            attributes: $this->getAttributes(node: $node),
        );
    }

    /** @return array<string,string> */
    private function getAttributes(Embed $node): array
    {
        $url     = $node->getUrl();
        $id      = $this->getId(url: $url);
        $videoId = is_string($id) ? $id : '';

        return [
            'width'           => '560',
            'height'          => '315',
            'src'             => 'https://www.youtube-nocookie.com/embed/' . $videoId,
            'frameborder'     => '0',
            'allow'           => 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture',
            'referrerpolicy'  => 'strict-origin-when-cross-origin',
            'allowfullscreen' => '',
        ];
    }
}

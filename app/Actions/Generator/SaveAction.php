<?php

declare(strict_types=1);

namespace App\Actions\Generator;

use App\Models\Generated;

final class SaveAction
{
    public function handle(Generated $asset, string $html, callable $callback): void
    {
        $text = $this->getRawText(html: $html);

        $asset->update(attributes: [
            'generated_content_html' => $html,
            'generated_content_raw'  => $text,
        ]);

        $callback($text);
    }

    private function getRawText(string $html): string
    {
        return strip_tags(string: str_replace(
            search : ['<br>', '<br />'],
            replace: "\n",
            subject: $html
        ));
    }
}

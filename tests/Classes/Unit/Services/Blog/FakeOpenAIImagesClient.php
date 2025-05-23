<?php

/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\Blog;

use stdClass;

final class FakeOpenAIImagesClient
{
    public function create(array $parameters): object
    {
        $response       = new stdClass();
        $response->data = [
            (object) [
                'url'      => 'https://example.com/test-image.png',
                'b64_json' => base64_encode(string: 'test-image-data'),
            ],
        ];

        return $response;
    }
}

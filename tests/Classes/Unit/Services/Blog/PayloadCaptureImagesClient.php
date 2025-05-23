<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Services\Blog;

use stdClass;

final readonly class PayloadCaptureImagesClient
{
    public function __construct(private PayloadCapture $payloadCapture) {}

    public function create(array $parameters): object
    {
        $this->payloadCapture->payload = $parameters;

        $response       = new stdClass();
        $response->data = [
            (object) [
                'b64_json' => base64_encode(string: 'test-image-data'),
            ],
        ];

        return $response;
    }
}

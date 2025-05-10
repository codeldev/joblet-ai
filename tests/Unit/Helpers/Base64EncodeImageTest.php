<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

beforeEach(closure: function (): void
{
    $this->testDir = storage_path(path: 'image-test');

    if (! is_dir(filename: $this->testDir))
    {
        mkdir(directory: $this->testDir, recursive: true);
    }
});

afterEach(closure: function (): void
{
    if (isset($this->testDir) && is_dir(filename: $this->testDir))
    {
        array_map(callback: 'unlink', array: glob(pattern: "{$this->testDir}/*.*"));

        rmdir(directory: $this->testDir);
    }
});

it('correctly converts a local PNG image to base64', function (): void
{
    $tempFile = "{$this->testDir}/test_image.png";
    $image    = imagecreate(width: 100, height: 100);

    imagecolorallocate(image: $image, red: 255, green: 255, blue: 255);

    $blue = imagecolorallocate(image: $image, red: 0, green: 0, blue: 255);

    imagefilledrectangle(image: $image, x1: 25, y1: 25, x2: 75, y2: 75, color: $blue);
    imagepng(image: $image, file: $tempFile);
    imagedestroy(image: $image);

    $result = generateBase64Image(image: $tempFile);

    expect(value: $result)
        ->toStartWith(expected: 'data:image/png;base64,');

    $base64Content  = str_replace(search: 'data:image/png;base64,', replace: '', subject: $result);
    $decodedContent = base64_decode(string: $base64Content, strict: true);

    expect(value: $decodedContent)
        ->toBeTruthy();

    $tempDecodedFile = "{$this->testDir}/decoded.png";

    file_put_contents(filename: $tempDecodedFile, data: $decodedContent);

    $imageInfo = getimagesize(filename: $tempDecodedFile);

    expect(value: $imageInfo[0])
        ->toBe(expected: 100)
        ->and(value: $imageInfo[1])
        ->toBe(expected: 100)
        ->and(value: $imageInfo['mime'])
        ->toBe(expected: 'image/png');
});

it('correctly converts a local JPEG image to base64', function (): void
{
    $tempFile = "{$this->testDir}/test_image.jpg";
    $image    = imagecreate(width: 100, height: 100);

    imagecolorallocate(image: $image, red: 0, green: 0, blue: 0);

    $red = imagecolorallocate(image: $image, red: 255, green: 0, blue: 0);

    imagefilledellipse(image: $image, center_x: 50, center_y: 50, width: 60, height: 60, color: $red);
    imagejpeg(image: $image, file: $tempFile);
    imagedestroy(image: $image);

    $result = generateBase64Image(image: $tempFile);

    expect(value: $result)
        ->toStartWith(expected: 'data:image/jpg;base64,');

    $base64Content  = str_replace(search: 'data:image/jpg;base64,', replace: '', subject: $result);
    $decodedContent = base64_decode(string: $base64Content, strict: true);

    expect(value: $decodedContent)
        ->toBeTruthy();

    $tempDecodedFile = "{$this->testDir}/decoded.jpg";

    file_put_contents(filename: $tempDecodedFile, data: $decodedContent);

    $imageInfo = getimagesize(filename: $tempDecodedFile);

    expect(value: $imageInfo[0])
        ->toBe(expected: 100)
        ->and(value: $imageInfo[1])
        ->toBe(expected: 100)
        ->and(value: $imageInfo['mime'])
        ->toBe(expected: 'image/jpeg');
});

it('throws RuntimeException when image file does not exist', function (): void
{
    generateBase64Image(image: "{$this->testDir}/non_existent_image.jpg");
})->throws(exception: RuntimeException::class, exceptionMessage: 'File not found');

it('throws RuntimeException when image path is empty', function (): void
{
    generateBase64Image(image: null);
})->throws(exception: RuntimeException::class, exceptionMessage: 'No file path passed');

it('throws RuntimeException when image file is empty', function (): void
{
    $tempFile = "{$this->testDir}/test-image.jpg";

    file_put_contents(filename: $tempFile, data: '');

    generateBase64Image(image: $tempFile);
})->throws(exception: RuntimeException::class, exceptionMessage: 'File is empty');

it('handles different file extensions correctly', function (string $extension): void
{
    $tempFile = "{$this->testDir}/test-image.{$extension}";
    $image    = imagecreate(width: 100, height: 100);

    imagecolorallocate(image: $image, red: 255, green: 255, blue: 255);

    match (true)
    {
        ($extension === 'jpg'), ($extension === 'jpeg') => imagejpeg(image: $image, file: $tempFile),
        ($extension === 'png')                          => imagepng(image: $image, file: $tempFile),
        ($extension === 'gif')                          => imagegif(image: $image, file: $tempFile),
    };

    imagedestroy(image: $image);

    $result = generateBase64Image(image: $tempFile);

    expect(value: $result)
        ->toStartWith(expected: "data:image/{$extension};base64,");
})->with(['png', 'jpg', 'jpeg', 'gif']);

it('works with file URLs', function (): void
{
    $tempFile = "{$this->testDir}/url_image.jpg";
    $image    = imagecreate(width: 100, height: 100);

    imagecolorallocate(image: $image, red: 0, green: 0, blue: 0);

    $red = imagecolorallocate(image: $image, red: 255, green: 0, blue: 0);

    imagefilledellipse(image: $image, center_x: 50, center_y: 50, width: 60, height: 60, color: $red);
    imagejpeg(image: $image, file: $tempFile);
    imagedestroy(image: $image);

    $result = generateBase64Image(image: 'file://' . $tempFile);

    expect(value: $result)
        ->toStartWith(expected: 'data:image/jpg;base64,');
});

it('handles larger images', function (): void
{
    $width    = 500;
    $height   = 500;
    $tileSize = 50;
    $tempFile = "{$this->testDir}/large_image.png";
    $image    = imagecreatetruecolor(width: $width, height: $height);
    $black    = imagecolorallocate(image: $image, red: 0, green: 0, blue: 0);
    $white    = imagecolorallocate(image: $image, red: 255, green: 255, blue: 255);

    imagefilledrectangle(image: $image, x1: 0, y1: 0, x2: $width, y2: $height, color: $white);

    for ($y = 0; $y < $height; $y += $tileSize)
    {
        for ($x = 0; $x < $width; $x += $tileSize)
        {
            if (($x + $y) % ($tileSize * 2) === 0)
            {
                imagefilledrectangle(image: $image, x1: $x, y1: $y, x2: $x + $tileSize, y2: $y + $tileSize, color: $black);
            }
        }
    }

    imagepng(image: $image, file: $tempFile);
    imagedestroy(image: $image);

    $result  = generateBase64Image(image: $tempFile);
    $content = str_replace(search: 'data:image/png;base64,', replace: '', subject: $result);

    expect(value: $result)
        ->toStartWith(expected: 'data:image/png;base64,')
        ->and(value: mb_strlen($content))
        ->toBeGreaterThan(expected: 1000);
});

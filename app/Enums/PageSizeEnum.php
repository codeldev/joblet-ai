<?php

/** @noinspection PhpUnused */

declare(strict_types=1);

namespace App\Enums;

enum PageSizeEnum: string
{
    case LETTER  = 'letter';
    case LEGAL   = 'legal';
    case TABLOID = 'tabloid';
    case LEDGER  = 'ledger';
    case A0      = 'a0';
    case A1      = 'a1';
    case A2      = 'a2';
    case A3      = 'a3';
    case A4      = 'a4';
    case A5      = 'a5';
    case A6      = 'a6';

    /** @return array<string, string> */
    public static function getPageSizes(): array
    {
        /** @var array<string, string> $result */
        $result = collect(self::cases())->mapWithKeys(
            callback: fn (PageSizeEnum $size) => [$size->value => PageSizeEnum::getSizeLabel(size: $size)]
        )->toArray();

        return $result;
    }

    /** @return array<string, float> */
    public function getDimensions(): array
    {
        $inches = match ($this)
        {
            self::LETTER  => [8.5, 11.0],
            self::LEGAL   => [8.5, 14.0],
            self::TABLOID => [11.0, 17.0],
            self::LEDGER  => [17.0, 11.0],
            self::A0      => [33.1, 46.8],
            self::A1      => [23.4, 33.1],
            self::A2      => [16.54, 23.4],
            self::A3      => [11.7, 16.54],
            self::A4      => [8.27, 11.7],
            self::A5      => [5.83, 8.27],
            self::A6      => [4.13, 5.83],
        };

        return [
            'width'  => $inches[0],
            'height' => $inches[1],
        ];
    }

    private static function getSizeLabel(PageSizeEnum $size): string
    {
        $dimensions = $size->getDimensions();
        $sizeLabel  = str(string: $size->value)->title()->toString();
        $width      = (string) $dimensions['width'];
        $height     = (string) $dimensions['height'];

        return "{$sizeLabel} ({$width} x {$height} inches)";
    }
}

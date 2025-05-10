<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Facades\Cache;

enum DateFormatEnum: int
{
    case VARIANT_A = 1;
    case VARIANT_B = 2;
    case VARIANT_C = 3;
    case VARIANT_D = 4;
    case VARIANT_E = 5;
    case VARIANT_F = 6;
    case VARIANT_G = 7;
    case VARIANT_H = 8;
    case VARIANT_I = 9;

    /** @return array<int, string> */
    public static function getFormats(): array
    {
        /** @var array<int, string> $result */
        $result = Cache::remember(
            key     : 'enum.letter.date_formats',
            ttl     : now()->addWeek(),
            callback: static function (): array
            {
                /** @var array<int, string> $formats */
                $formats = collect(value: self::cases())
                    ->mapWithKeys(callback: fn (self $case): array => $case->item())
                    ->toArray();

                return $formats;
            }
        );

        return $result;
    }

    public function format(): string
    {
        return match ($this)
        {
            self::VARIANT_A => 'd/m/Y',     // 15/05/2025
            self::VARIANT_B => 'm/d/Y',     // 03/15/2025
            self::VARIANT_C => 'Y-m-d',     // 2025-05-15
            self::VARIANT_D => 'Y/m/d',     // 2025/05/15
            self::VARIANT_E => 'M/d/Y',     // Feb/15/2025
            self::VARIANT_F => 'jS F Y',    // 15th February 2025
            self::VARIANT_G => 'F jS, Y',   // February 15th 2025
            self::VARIANT_H => 'jS M Y',    // 15th Feb 2025
            self::VARIANT_I => 'M jS, Y',   // Feb 15th 2025
        };
    }

    /** @return array<int, string> */
    public function item(): array
    {
        return [$this->value => now()->format(format: $this->format())];
    }
}

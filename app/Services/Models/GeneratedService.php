<?php

declare(strict_types=1);

namespace App\Services\Models;

use App\Models\Generated;

final class GeneratedService
{
    /** @return array<string, mixed> */
    public static function getFillable(Generated $asset): array
    {
        return collect(value: [
            $asset->except(attributes: self::unfillable()),
            self::setEnumFillable(asset: $asset),
        ])->collapse()->all();
    }

    /** @return list<string> */
    private static function unfillable(): array
    {
        return [
            'language_variant',
            'date_format',
            'option_length',
            'option_tone',
            'option_creativity',
        ];
    }

    /** @return array<string, int> */
    private static function setEnumFillable(Generated $asset): array
    {
        return [
            'language_variant'  => $asset->language_variant->value  ?? 1,
            'date_format'       => $asset->date_format->value       ?? 1,
            'option_length'     => $asset->option_length->value     ?? 2,
            'option_tone'       => $asset->option_tone->value       ?? 2,
            'option_creativity' => $asset->option_creativity->value ?? 2,
        ];
    }
}

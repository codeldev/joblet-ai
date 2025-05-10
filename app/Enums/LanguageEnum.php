<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Facades\Cache;

enum LanguageEnum: int
{
    case EN_GB = 1;
    case EN_US = 2;
    case EN_CA = 3;
    case EN_AU = 4;
    case EN_NZ = 5;
    case EN_IE = 6;
    case EN_ZA = 7;
    case EN_IN = 8;
    case EN_SG = 9;
    case EN_HK = 10;
    case EN_PH = 11;
    case EN_MY = 12;
    case EN_NG = 13;
    case EN_JM = 14;
    case EN_KE = 15;
    case EN_PK = 16;
    case EN_GH = 17;

    /** @return array<int, string> */
    public static function getLanguages(): array
    {
        /** @var array<int, string> $result */
        $result = Cache::remember(
            key     : 'enum.letter.languages',
            ttl     : now()->addWeek(),
            callback: static function (): array
            {
                /** @var array<int, string> $languages */
                $languages = collect(value: self::cases())
                    ->mapWithKeys(callback: fn (self $case): array => [$case->value => $case->label()])
                    ->toArray();

                return $languages;
            }
        );

        return $result;
    }

    public function label(): string
    {
        return match ($this)
        {
            self::EN_GB => 'British English',
            self::EN_US => 'American English',
            self::EN_CA => 'Canadian English',
            self::EN_AU => 'Australian English',
            self::EN_NZ => 'New Zealand English',
            self::EN_IE => 'Irish English',
            self::EN_ZA => 'South African English',
            self::EN_IN => 'Indian English',
            self::EN_SG => 'Singaporean English',
            self::EN_HK => 'Hong Kong English',
            self::EN_PH => 'Philippine English',
            self::EN_MY => 'Malaysian English',
            self::EN_NG => 'Nigerian English',
            self::EN_JM => 'Jamaican English',
            self::EN_KE => 'Kenyan English',
            self::EN_PK => 'Pakistani English',
            self::EN_GH => 'Ghanaian English',
        };
    }
}

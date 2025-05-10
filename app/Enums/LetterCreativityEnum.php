<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Facades\Cache;

enum LetterCreativityEnum: int
{
    case PRECISE  = 1;
    case BALANCED = 2;
    case DYNAMIC  = 3;
    case CREATIVE = 4;

    /** @return array<int,array{label:string,description:string}> */
    public static function getOptions(): array
    {
        /** @var array<int,array{label:string,description:string}> $result */
        $result = Cache::remember(
            key     : 'enum.letter.creativity',
            ttl     : now()->addWeek(),
            callback: static function (): array
            {
                /** @var array<int,array{label:string,description:string}> $options */
                $options = collect(value: self::cases())
                    ->mapWithKeys(callback: fn (self $option): array => $option->option())
                    ->toArray();

                return $options;
            }
        );

        return $result;
    }

    /** @return array<int,array{label:string,description:string}> */
    public function option(): array
    {
        return [
            $this->value => [
                'label'       => $this->label(),
                'description' => $this->description(),
            ],
        ];
    }

    public function label(): string
    {
        return match ($this)
        {
            self::PRECISE  => trans(key: 'generator.letter.creativity.label.precise'),
            self::BALANCED => trans(key: 'generator.letter.creativity.label.balanced'),
            self::DYNAMIC  => trans(key: 'generator.letter.creativity.label.dynamic'),
            self::CREATIVE => trans(key: 'generator.letter.creativity.label.creative'),
        };
    }

    public function description(): string
    {
        return match ($this)
        {
            self::PRECISE  => trans(key: 'generator.letter.creativity.description.precise'),
            self::BALANCED => trans(key: 'generator.letter.creativity.description.balanced'),
            self::DYNAMIC  => trans(key: 'generator.letter.creativity.description.dynamic'),
            self::CREATIVE => trans(key: 'generator.letter.creativity.description.creative'),
        };
    }

    public function temperature(): float
    {
        return match ($this)
        {
            self::PRECISE  => 0.25,
            self::BALANCED => 0.5,
            self::DYNAMIC  => 0.75,
            self::CREATIVE => 0.9,
        };
    }
}

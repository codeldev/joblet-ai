<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Facades\Cache;

enum LetterLengthEnum: int
{
    case SHORT  = 1;
    case MEDIUM = 2;
    case LONG   = 3;

    /** @return array<int,array{label:string,description:string}> */
    public static function getOptions(): array
    {
        /** @var array<int,array{label:string,description:string}> $result */
        $result = Cache::remember(
            key     : 'enum.letter.length',
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
            self::SHORT  => trans(key: 'generator.letter.length.label.short'),
            self::MEDIUM => trans(key: 'generator.letter.length.label.medium'),
            self::LONG   => trans(key: 'generator.letter.length.label.long'),
        };
    }

    public function description(): string
    {
        return match ($this)
        {
            self::SHORT  => trans(key: 'generator.letter.length.description.short'),
            self::MEDIUM => trans(key: 'generator.letter.length.description.medium'),
            self::LONG   => trans(key: 'generator.letter.length.description.long'),
        };
    }

    public function text(): string
    {
        return match ($this)
        {
            self::SHORT  => trans(key: 'prompt.system.length.short'),
            self::MEDIUM => trans(key: 'prompt.system.length.medium'),
            self::LONG   => trans(key: 'prompt.system.length.long'),
        };
    }
}

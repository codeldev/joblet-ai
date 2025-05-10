<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Facades\Cache;

enum LetterToneEnum: int
{
    case CASUAL = 1;
    case FORMAL = 2;
    case PRO    = 3;

    /** @return array<int,array{label:string,description:string}> */
    public static function getOptions(): array
    {
        /** @var array<int,array{label:string,description:string}> $result */
        $result = Cache::remember(
            key     : 'enum.letter.tone',
            ttl     : now()->addWeek(),
            callback: static function (): array
            {
                /** @var array<int,array{label:string,description:string}> $options */
                $options = collect(value: self::cases())
                    ->mapWithKeys(callback: fn (self $case): array => $case->option())
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
            self::CASUAL => trans(key: 'generator.letter.tone.label.casual'),
            self::FORMAL => trans(key: 'generator.letter.tone.label.formal'),
            self::PRO    => trans(key: 'generator.letter.tone.label.professional'),
        };
    }

    public function description(): string
    {
        return match ($this)
        {
            self::CASUAL => trans(key: 'generator.letter.tone.description.casual'),
            self::FORMAL => trans(key: 'generator.letter.tone.description.formal'),
            self::PRO    => trans(key: 'generator.letter.tone.description.professional'),
        };
    }

    public function text(): string
    {
        return match ($this)
        {
            self::CASUAL => trans(key: 'prompt.system.tone.casual'),
            self::FORMAL => trans(key: 'prompt.system.tone.formal'),
            self::PRO    => trans(key: 'prompt.system.tone.professional'),
        };
    }
}

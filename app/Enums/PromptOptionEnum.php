<?php

declare(strict_types=1);

namespace App\Enums;

enum PromptOptionEnum: int
{
    case PROBLEM_SOLVING = 1;
    case GROWTH_INTEREST = 2;
    case UNIQUE_VALUE    = 3;
    case ACHIEVEMENTS    = 4;
    case MOTIVATION      = 5;
    case AMBITIONS       = 6;
    case OTHER           = 7;

    public function systemPrompt(): string
    {
        return match ($this)
        {
            self::PROBLEM_SOLVING => trans(key: 'prompt.system.option.problem'),
            self::GROWTH_INTEREST => trans(key: 'prompt.system.option.growth'),
            self::UNIQUE_VALUE    => trans(key: 'prompt.system.option.unique'),
            self::ACHIEVEMENTS    => trans(key: 'prompt.system.option.achievements'),
            self::MOTIVATION      => trans(key: 'prompt.system.option.motivation'),
            self::AMBITIONS       => trans(key: 'prompt.system.option.ambitions'),
            self::OTHER           => trans(key: 'prompt.system.option.other'),
        };
    }

    public function userPrompt(string $text): string
    {
        return match ($this)
        {
            self::PROBLEM_SOLVING => trans(key: 'prompt.user.info.problem', replace: ['value' => $text]),
            self::GROWTH_INTEREST => trans(key: 'prompt.user.info.growth', replace: ['value' => $text]),
            self::UNIQUE_VALUE    => trans(key: 'prompt.user.info.unique', replace: ['value' => $text]),
            self::ACHIEVEMENTS    => trans(key: 'prompt.user.info.achievements', replace: ['value' => $text]),
            self::MOTIVATION      => trans(key: 'prompt.user.info.motivation', replace: ['value' => $text]),
            self::AMBITIONS       => trans(key: 'prompt.user.info.ambitions', replace: ['value' => $text]),
            self::OTHER           => trans(key: 'prompt.user.info.other', replace: ['value' => $text]),
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Generator;

use App\Abstracts\Services\Generator\Prompt;
use App\Enums\LetterCreativityEnum;
use App\Enums\MaxTokensEnum;

final readonly class Generator
{
    private string $model;

    /** @param array<string, mixed> $settings */
    public function __construct(public array $settings)
    {
        $this->model = config(key: 'openai.model', default: 'gpt-3.5-turbo');
    }

    /** @return array<string, mixed> */
    public function builder(): array
    {
        return [
            'model'             => $this->model,
            'messages'          => $this->buildMessages(),
            'max_tokens'        => $this->getMaxTokens(),
            'temperature'       => $this->getTemperature(),
            'presence_penalty'  => 0.1,
            'frequency_penalty' => 0.1,
        ];
    }

    /** @return array<int, array<string, string>> */
    private function buildMessages(): array
    {
        return [
            $this->buildMessage(
                prompt: new SystemPrompt(settings: $this->settings)
            ),
            $this->buildMessage(
                prompt: new UserPrompt(settings: $this->settings)
            ),
        ];
    }

    /** @return array<string, string> */
    private function buildMessage(Prompt $prompt): array
    {
        return [
            'role'    => $prompt->role(),
            'content' => $prompt->build(),
        ];
    }

    private function getTemperature(): float
    {
        /** @var int|string $optionCreativity */
        $optionCreativity = $this->settings['option_creativity'] ?? LetterCreativityEnum::BALANCED->value;

        return LetterCreativityEnum::from(
            value: $optionCreativity
        )->temperature();
    }

    private function getMaxTokens(): int
    {
        /** @var int|string $optionLength */
        $optionLength = $this->settings['option_length'] ?? MaxTokensEnum::MEDIUM->value;

        return MaxTokensEnum::from(
            value: $optionLength
        )->tokens();
    }
}

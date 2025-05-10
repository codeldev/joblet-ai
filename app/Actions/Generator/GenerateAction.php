<?php

declare(strict_types=1);

namespace App\Actions\Generator;

use App\Contracts\Actions\Generator\GenerateActionInterface;
use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Models\Generated;
use App\Models\User;
use App\Services\Generator\Generator;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;
use RuntimeException;
use Throwable;

final class GenerateAction implements GenerateActionInterface
{
    /** @param  array<string, mixed>  $settings */
    public function handle(array $settings, callable $success, callable $failed): void
    {
        try
        {
            $settings = $this->formatSettings(
                settings: $settings
            );

            $generator = new Generator(
                settings: $settings
            );

            $response = OpenAI::chat()->create(
                parameters: $generator->builder()
            );

            /** @var string|null $letterContent */
            $letterContent = $response->choices[0]->message->content ?? null;

            /** @var int $usedTokens */
            $usedTokens = $response->usage->totalTokens ?? 0;

            if (empty($letterContent))
            {
                throw new RuntimeException(
                    message: trans(key: 'generator.generation.failed')
                );
            }

            $asset = DB::transaction(callback: fn (): Generated => $this->storeGeneratedResponse(
                settings  : $settings,
                output    : $letterContent,
                tokensUsed: $usedTokens
            ));

            $success($asset);
        }
        catch (Throwable $e)
        {
            report(exception: $e);

            $failed($e->getMessage());
        }
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    private function formatSettings(array $settings): array
    {
        /** @var int $dateFormat */
        $dateFormat = $settings['date_format'] ?? DateFormatEnum::VARIANT_A->value;

        /** @var int $language */
        $language  = $settings['language_variant'] ?? LanguageEnum::EN_GB->value;

        $settings['date_format'] = DateFormatEnum::from(
            value: $dateFormat
        );

        $settings['language_variant'] = LanguageEnum::from(
            value: $language
        );

        return $settings;
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    private function storeGeneratedResponse(array $settings, string $output, int $tokensUsed): Generated
    {
        $settings['generated_content_raw']  = $output;
        $settings['generated_content_html'] = nl2br(string: $output);

        /** @var User $user */
        $user = auth()->user();

        /** @var Generated $asset */
        $asset = $user->generated()
            ->create(attributes: $settings);

        $asset->credit()->create(attributes: [
            'user_id'     => $user->id,
            'word_count'  => str_word_count(string: strip_tags(string: $output)),
            'tokens_used' => $tokensUsed,
        ]);

        return $asset;
    }
}

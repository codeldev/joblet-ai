<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Sluggable\SlugOptions;
use voku\helper\StopWords;
use voku\helper\StopWordsLanguageNotExists;

final class BlogPostSlugOptions extends SlugOptions
{
    public function withoutStopWords(): self
    {
        /** @var array<int, string>|callable(Model): mixed|string $sourceConfig */
        $sourceConfig = $this->generateSlugFrom;

        $this->generateSlugFrom = function (Model $model) use ($sourceConfig): string
        {
            $fieldValue = $this->getFieldValue(model: $model, sourceConfig: $sourceConfig);
            $subject    = Str::lower(value: $fieldValue);
            $words      = preg_split(pattern: '/\s+/', subject: $subject);

            // @codeCoverageIgnoreStart
            if (! is_array(value: $words))
            {
                return $subject;
            }
            // @codeCoverageIgnoreEnd

            $filtered = $this->getFilteredResult(words: $words);

            return notEmpty(value: $filtered)
                ? $filtered
                : $subject;
        };

        return $this;
    }

    /**
     * @param  array<int, string>  $words
     *
     * @throws StopWordsLanguageNotExists
     */
    private function getFilteredResult(array $words): string
    {
        $stopWordsList = $this->getStopWords();

        /** @var array<int, string> $filteredWords */
        $filteredWords = array_filter(
            array     : $words,
            callback  : fn (string $word): bool => $this->filterWord(word: $word, stopWordsList: $stopWordsList),
        );

        return implode(
            separator: ' ',
            array    : $filteredWords
        );
    }

    /** @param array<int, string> $stopWordsList */
    private function filterWord(string $word, array $stopWordsList): bool
    {
        return ! in_array(needle: $word, haystack: $stopWordsList, strict: true) && $word !== '';
    }

    /**
     * @return array<int, string>
     *
     * @throws StopWordsLanguageNotExists
     */
    private function getStopWords(): array
    {
        /** @var array<int, string> $stopWords */
        $stopWords = (new StopWords)->getStopWordsFromLanguage(
            language: app()->getLocale()
        );

        return $stopWords;
    }

    /**
     * Ensures a value is converted to a string safely
     *
     * @param  mixed  $value  The value to convert to string
     * @return string The string representation of the value
     */
    private function ensureString(mixed $value): string
    {
        if ($value === null)
        {
            return '';
        }

        if (is_string(value: $value))
        {
            return $value;
        }

        if (is_scalar(value: $value))
        {
            return (string) $value;
        }

        if (is_object(value: $value) && method_exists(object_or_class: $value, method: '__toString'))
        {
            return (string) $value;
        }

        return '';
    }

    /**
     * @param  string|array<int, string>|callable(Model): mixed  $sourceConfig
     */
    private function getFieldValue(Model $model, string | array | callable $sourceConfig): string
    {
        if (is_string(value: $sourceConfig))
        {
            $value = $model->{$sourceConfig};

            return $this->ensureString(value: $value);
        }

        if (is_callable(value: $sourceConfig))
        {
            /** @var mixed $value */
            $value = $sourceConfig($model);

            return $this->ensureString(value: $value);
        }

        if ($sourceConfig !== [])
        {
            /** @var string|(callable(Model): mixed) $firstSource */
            $firstSource = $sourceConfig[0];

            if (is_callable(value: $firstSource))
            {
                /** @var mixed $value */
                $value = $firstSource($model);

                return $this->ensureString(value: $value);
            }

            $value = $model->{$firstSource};

            return $this->ensureString(value: $value);
        }

        /** @var string $title */
        $title = $model->title ?? '';

        return $title;
    }
}

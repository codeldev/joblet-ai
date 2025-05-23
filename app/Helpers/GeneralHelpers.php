<?php

/** @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection */

declare(strict_types=1);

if (! function_exists(function: 'notEmpty'))
{
    /** @param string|array<mixed>|null|mixed $value */
    function notEmpty(mixed $value): bool
    {
        if (is_array(value: $value))
        {
            return $value !== [];
        }

        if ($value === null)
        {
            return false;
        }

        if (is_bool(value: $value))
        {
            return true;
        }

        if (is_numeric(value: $value) && $value !== '0')
        {
            return true;
        }

        if (is_string(value: $value))
        {
            return $value !== '' && $value !== '0';
        }

        if (is_scalar(value: $value) || (is_object(value: $value) && method_exists(object_or_class: $value, method: '__toString')))
        {
            $stringValue = (string) $value;

            return $stringValue !== '' && $stringValue !== '0';
        }

        return true;
    }
}

if (! function_exists(function: 'pluralize'))
{
    function pluralize(float | int $amount, string $word, bool $titleCase = false): string
    {
        $amountAsInteger = $amount < 1 ? 0 : (int) ceil(num: $amount);
        $formattedAmount = convertQuantity(quantity: $amount);
        $pluralizedLabel = $titleCase
            ? str(string: $word)->title()->plural(count: $amountAsInteger)->toString()
            : str(string: $word)->plural(count: $amountAsInteger)->toString();

        return $formattedAmount . ' ' . $pluralizedLabel;
    }
}

if (! function_exists(function: 'convertQuantity'))
{
    function convertQuantity(float $quantity): string
    {
        $formatted = number_format(num: $quantity, decimals: 2);
        $trimmed   = mb_rtrim(string: $formatted, characters: '0');

        return mb_rtrim(string: $trimmed, characters: '.');
    }
}

if (! function_exists(function: 'generateBase64Image'))
{
    function generateBase64Image(?string $image): string
    {
        if ($image === null || $image === '' || $image === '0')
        {
            throw new RuntimeException(message: 'No file path passed');
        }

        $options = [
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ];

        if (! file_exists(filename: $image))
        {
            throw new RuntimeException(message: 'File not found');
        }

        $fileType = pathinfo(path: $image, flags: PATHINFO_EXTENSION);
        $contents = file_get_contents(
            filename: $image,
            context: stream_context_create(options: $options)
        );

        if ($contents === '' || $contents === '0' || $contents === false)
        {
            throw new RuntimeException(message: 'File is empty');
        }

        $encoded = base64_encode(string: $contents);

        return "data:image/{$fileType};base64,{$encoded}";
    }
}

if (! function_exists(function: 'generateRandomNumbers'))
{
    /** @return array<int,int> */
    function generateRandomNumbers(int $from = 1, int $to = 10, int $take = 3): array
    {
        $numbers = range(start: $from, end: $to);

        shuffle($numbers);

        return array_slice(array: $numbers, offset: 0, length: $take);
    }
}

if (! function_exists(function: 'cleanNl2br'))
{
    function cleanNl2br(string $string): string
    {
        return nl2br(string: str(string: $string)
            ->replaceMatches(pattern: "/\n\n/", replace: "\n")
            ->toString());
    }
}

if (! function_exists(function: 'throwException'))
{
    /** @throws Exception */
    function throwException(string $exceptionClass): void
    {
        $exception = new $exceptionClass();

        if (! $exception instanceof Exception)
        {
            throw new RuntimeException(message: "{$exceptionClass} is not an instance of Exception!");
        }

        report(exception: $exception);

        throw $exception;
    }
}

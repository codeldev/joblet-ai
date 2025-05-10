<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

declare(strict_types=1);

if (! function_exists(function: 'getFakeNames'))
{
    /** @return array<int, string> */
    function getFakeNames(int $take): array
    {
        return getRandomItems(type: 'name', count: $take);
    }
}

if (! function_exists(function: 'getFakeDocuments'))
{
    /** @return array<int, string> */
    function getFakeDocuments(int $take): array
    {
        return getRandomItems(type: 'document', count: $take);
    }
}

if (! function_exists(function: 'getFakeJobs'))
{
    /** @return array<int, string> */
    function getFakeJobs(int $take): array
    {
        return getRandomItems(type: 'job', count: $take);
    }
}

if (! function_exists(function: 'getFakeCompanies'))
{
    /** @return array<int, string> */
    function getFakeCompanies(int $take): array
    {
        return getRandomItems(type: 'company', count: $take);
    }
}

if (! function_exists(function: 'getRandomItems'))
{
    /** @return array<int, string> */
    function getRandomItems(string $type, int $count): array
    {
        return getRandomSelection(
            items: getAvailableList(type: $type),
            count: $count
        );
    }
}

if (! function_exists(function: 'getRandomSelection'))
{
    /**
     * @param  array<int|string, string>  $items
     * @return array<int, string>
     */
    function getRandomSelection(array $items, int $count): array
    {
        $count = min($count, count(value: $items));

        shuffle(array: $items);

        return array_combine(
            keys   : range(start: 1, end: $count),
            values : array_slice(
                array  : $items,
                offset : 0,
                length : $count
            )
        );
    }
}

if (! function_exists(function: 'getAvailableList'))
{
    /** @return array<int, string> */
    function getAvailableList(string $type): array
    {
        return array_map(
            callback : static fn ($i) => trans(key: "fake.{$type}.{$i}"),
            array    : range(start: 1, end: 20)
        );
    }
}

if (! function_exists(function: 'getRandomFutureDate'))
{
    /** @throws Random\RandomException */
    function getRandomFutureDate(): Carbon\CarbonImmutable
    {
        return Carbon\CarbonImmutable::now()->addDays(value: random_int(14, 70));
    }
}

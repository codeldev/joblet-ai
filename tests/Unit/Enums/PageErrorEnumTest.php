<?php

/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\PageErrorEnum;

test(description: 'getTitle returns correct translation for E405 error', closure: function (): void
{
    expect(value: PageErrorEnum::E405->getTitle())
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'getTitle returns correct translation for E406 error', closure: function (): void
{
    expect(value: PageErrorEnum::E406->getTitle())
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'getTitle returns correct translation for E407 error', closure: function (): void
{
    expect(value: PageErrorEnum::E407->getTitle())
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'getTitle returns correct translation for E500 error', closure: function (): void
{
    expect(value: PageErrorEnum::E500->getTitle())
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'getTitle returns correct translation for E505 error', closure: function (): void
{
    expect(value: PageErrorEnum::E505->getTitle())
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'getDescription returns correct translation for E405 error', closure: function (): void
{
    expect(value: PageErrorEnum::E405->getDescription())
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'getDescription returns correct translation for E410 error', closure: function (): void
{
    expect(value: PageErrorEnum::E410->getDescription())
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'getDescription returns correct translation for E415 error', closure: function (): void
{
    expect(value: PageErrorEnum::E415->getDescription())
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'getDescription returns correct translation for E500 error', closure: function (): void
{
    expect(value: PageErrorEnum::E500->getDescription())
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'getDescription returns correct translation for E505 error', closure: function (): void
{
    expect(value: PageErrorEnum::E505->getDescription())
        ->toBeString()
        ->not->toBeEmpty();
});

test(description: 'all enum cases have correct integer values', closure: function (): void
{
    expect(value: PageErrorEnum::E400->value)->toBe(expected: 400)
        ->and(value: PageErrorEnum::E401->value)->toBe(expected: 401)
        ->and(value: PageErrorEnum::E402->value)->toBe(expected: 402)
        ->and(value: PageErrorEnum::E403->value)->toBe(expected: 403)
        ->and(value: PageErrorEnum::E404->value)->toBe(expected: 404)
        ->and(value: PageErrorEnum::E405->value)->toBe(expected: 405)
        ->and(value: PageErrorEnum::E406->value)->toBe(expected: 406)
        ->and(value: PageErrorEnum::E407->value)->toBe(expected: 407)
        ->and(value: PageErrorEnum::E408->value)->toBe(expected: 408)
        ->and(value: PageErrorEnum::E409->value)->toBe(expected: 409)
        ->and(value: PageErrorEnum::E410->value)->toBe(expected: 410)
        ->and(value: PageErrorEnum::E411->value)->toBe(expected: 411)
        ->and(value: PageErrorEnum::E412->value)->toBe(expected: 412)
        ->and(value: PageErrorEnum::E413->value)->toBe(expected: 413)
        ->and(value: PageErrorEnum::E414->value)->toBe(expected: 414)
        ->and(value: PageErrorEnum::E415->value)->toBe(expected: 415)
        ->and(value: PageErrorEnum::E500->value)->toBe(expected: 500)
        ->and(value: PageErrorEnum::E501->value)->toBe(expected: 501)
        ->and(value: PageErrorEnum::E502->value)->toBe(expected: 502)
        ->and(value: PageErrorEnum::E503->value)->toBe(expected: 503)
        ->and(value: PageErrorEnum::E504->value)->toBe(expected: 504)
        ->and(value: PageErrorEnum::E505->value)->toBe(expected: 505);
});

test(description: 'getTitle and getDescription return different values for each error', closure: function (): void
{
    $errors = [
        PageErrorEnum::E405,
        PageErrorEnum::E500,
        PageErrorEnum::E505,
    ];

    collect(value: $errors)->each(callback: function (PageErrorEnum $error): void
    {
        expect(value: $error->getTitle())
            ->toBeString()
            ->not->toBeEmpty()
            ->and(value: $error->getDescription())
            ->toBeString()
            ->not->toBeEmpty()
            ->and(value: $error->getTitle())
            ->not->toBe(expected: $error->getDescription());
    });
});

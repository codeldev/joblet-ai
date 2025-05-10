<?php

declare(strict_types=1);

it('returns the correct title for a valid error code', function (): void
{
    $this->assertEquals(
        expected: trans(key: 'errors.404.title'),
        actual  : getErrorResponseTitle(code: 404)
    );
});

it('returns the correct title for different error codes', function (): void
{
    collect(value: [400, 403, 404, 500])->each(callback: function (int $code): void
    {
        $this->assertEquals(
            expected: trans(key: "errors.{$code}.title"),
            actual  : getErrorResponseTitle(code: $code)
        );
    });
});

it('throws an exception for an invalid error code', function (): void
{
    $this->expectException(ValueError::class);

    getErrorResponseTitle(code: 999);
});

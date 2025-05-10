<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

it('returns the custom message when provided', function (): void
{
    $customMessage = 'Custom error message';

    $this->assertEquals(
        expected: $customMessage,
        actual  : getErrorResponseText(code: 404, message: $customMessage)
    );
});

it('returns the error description when no message is provided', function (): void
{
    $this->assertEquals(
        expected: trans(key: 'errors.404.message'),
        actual  : getErrorResponseText(code: 404)
    );
});

it('returns the error description when message is empty string', function (): void
{
    $this->assertEquals(
        expected: trans(key: 'errors.404.message'),
        actual  : getErrorResponseText(code: 404, message: '')
    );
});

it('returns the error description when message is "0"', function (): void
{
    $this->assertEquals(
        expected: trans(key: 'errors.404.message'),
        actual  : getErrorResponseText(code: 404, message: '0')
    );
});

it('returns the error description when message is null', function (): void
{
    $this->assertEquals(
        expected: trans(key: 'errors.404.message'),
        actual  : getErrorResponseText(code: 404)
    );
});

it('throws an exception for an invalid error code', function (): void
{
    $this->expectException(ValueError::class);
    getErrorResponseText(code: 999);
});

it('casts non-null message to string', function (): void
{
    $number = 123;

    $this->assertEquals(
        expected: '123',
        actual  : getErrorResponseText(code: 404, message: (string) $number)
    );
});

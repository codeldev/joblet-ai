<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Actions\Account\UpdateAction;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void
{
    $this->testUser  = testUser();
    $this->validated = [
        'email' => testEmail(),
        'name'  => fake()->name(),
    ];

    $this->actingAs(user: $this->testUser);
});

it('updates a profile for a user', function (): void
{
    (new UpdateAction)->handle(
        validated: $this->validated,
        success  : function (): void
        {
            expect(value: $this->testUser->fresh()->email)
                ->toBe(expected: $this->validated['email'])
                ->and(value: $this->testUser->fresh()->name)
                ->toBe(expected: $this->validated['name']);
        },
        failed: fn () => $this->assertTrue(false)
    );
});

it('fails to update a profile for a user', function (): void
{
    DB::shouldReceive('transaction')
        ->once()
        ->andThrow(exception: new Exception(message: 'Database exception'));

    (new UpdateAction)->handle(
        validated: $this->validated,
        success  : fn () => $this->assertTrue(false),
        failed   : fn () => $this->assertTrue(true)
    );

    expect(value: $this->testUser->fresh()->email)
        ->not->toBe(expected: $this->validated['email'])
        ->and(value: $this->testUser->fresh()->name)
        ->not->toBe(expected: $this->validated['name']);
});

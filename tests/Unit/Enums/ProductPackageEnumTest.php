<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\ProductPackageEnum;
use Illuminate\Support\Collection;

describe(description: 'ProductPackageEnum', tests: function (): void
{
    it('has the correct cases and values', function (): void
    {
        expect(value: ProductPackageEnum::INTRODUCTION->value)
            ->toBe(expected: 1)
            ->and(value: ProductPackageEnum::PACKAGE_A->value)
            ->toBe(expected: 2)
            ->and(value: ProductPackageEnum::PACKAGE_B->value)
            ->toBe(expected: 3)
            ->and(value: ProductPackageEnum::PACKAGE_C->value)
            ->toBe(expected: 4);
    });

    it('getAll returns a Collection of product objects (excluding INTRODUCTION)', function (): void
    {
        $all = ProductPackageEnum::getAll();

        expect(value: $all)
            ->toBeInstanceOf(class: Collection::class)
            ->and(value: $all->count())
            ->toBe(expected: 3);

        $ids = $all->pluck(value: 'id')->all();
        expect(value: $ids)
            ->toBe(expected: [2, 3, 4]);
    });

    it('getAllWIthFree returns a Collection of all product objects (including INTRODUCTION)', function (): void
    {
        $all = ProductPackageEnum::getAllWIthFree();

        expect(value: $all)
            ->toBeInstanceOf(class: Collection::class)
            ->and(value: $all->count())
            ->toBe(expected: 4);

        $ids = $all->pluck(value: 'id')->all();
        expect(value: $ids)
            ->toBe(expected: [1, 2, 3, 4]);
    });

    it('product returns correct structure for each case', function (): void
    {
        collect(value: ProductPackageEnum::cases())->each(callback: function ($case): void
        {
            $product = $case->product();

            expect(value: $product)
                ->toBeObject()
                ->and(value: $product->id)
                ->toBe(expected: $case->value)
                ->and(value: $product->title)
                ->toBeString()
                ->and(value: $product->subtitle)
                ->toBeString()
                ->and(value: $product->description)
                ->toBeString()
                ->and(value: $product->credits)
                ->toBeInt()
                ->and(value: $product->stripe_id)
                ->toBeString()
                ->and(value: $product->frequency)
                ->toBeString()
                ->and(value: $product->benefits)
                ->toBeArray()
                ->and(value: $product->meta)
                ->toBeObject()
                ->and(value: $product->meta->color)
                ->toBeString()
                ->and(value: $product->meta->icon)
                ->toBeString()
                ->and(value: $product->price)
                ->toBeObject()
                ->and(value: $product->price->raw)
                ->toBeInt()
                ->and(value: $product->price->formatted)
                ->toBeString();
        });
    });

    it('title returns a string for each case', function (): void
    {
        collect(value: ProductPackageEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->title())->toBeString());
    });

    it('subTitle returns a string for each case', function (): void
    {
        collect(value: ProductPackageEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->subTitle())->toBeString());
    });

    it('frequency returns a string for each case', function (): void
    {
        collect(value: ProductPackageEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->frequency())->toBeString());
    });

    it('description returns a string for each case', function (): void
    {
        collect(value: ProductPackageEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->description())->toBeString());
    });

    it('price returns an int for each case', function (): void
    {
        collect(value: ProductPackageEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->price())->toBeInt());
    });

    it('formattedPrice returns a string for each case', function (): void
    {
        collect(value: ProductPackageEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->formattedPrice())->toBeString());
    });

    it('credits returns an int for each case', function (): void
    {
        collect(value: ProductPackageEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->credits())->toBeInt());
    });

    it('icon returns a string for each case', function (): void
    {
        collect(value: ProductPackageEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->icon())->toBeString());
    });

    it('color returns a string for each case', function (): void
    {
        collect(value: ProductPackageEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->color())->toBeString());
    });

    it('stripeId returns a string for each case', function (): void
    {
        collect(value: ProductPackageEnum::cases())
            ->each(callback: fn ($case) => expect(value: $case->stripeId())->toBeString());
    });

    it('benefits returns an array of strings for each case', function (): void
    {
        collect(value: ProductPackageEnum::cases())->each(callback: function ($case): void
        {
            $benefits = $case->benefits();

            expect(value: $benefits)
                ->toBeArray()
                ->and(value: count($benefits))
                ->toBeGreaterThanOrEqual(expected: 3);

            foreach ($benefits as $benefit)
            {
                expect(value: $benefit)->toBeString();
            }
        });
    });
});

<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;

enum ProductPackageEnum: int
{
    case INTRODUCTION = 1;
    case PACKAGE_A    = 2;
    case PACKAGE_B    = 3;
    case PACKAGE_C    = 4;

    /** @return Collection<int,object> */
    public static function getAll(): Collection
    {
        /** @var Collection<int,object> $result */
        $result = Cache::remember(
            key     : 'product:packages',
            ttl     : now()->addWeek(),
            callback: static function (): Collection
            {
                /** @var Collection<int,object> $collection */
                $collection = collect(value: self::cases())
                    ->reject(callback: fn (self $case): bool => $case === self::INTRODUCTION)
                    ->map(callback: fn (self $case): object => $case->product());

                return $collection;
            }
        );

        return $result;
    }

    /** @return Collection<int,object> */
    public static function getAllWIthFree(): Collection
    {
        /** @var Collection<int,object> $result */
        $result = Cache::remember(
            key     : 'product:packages:all',
            ttl     : now()->addWeek(),
            callback: static function (): Collection
            {
                /** @var Collection<int,object> $collection */
                $collection = collect(value: self::cases())
                    ->map(callback: fn (self $case): object => $case->product());

                return $collection;
            }
        );

        return $result;
    }

    public function product(): object
    {
        return (object) [
            'id'          => $this->value,
            'title'       => $this->title(),
            'subtitle'    => $this->subtitle(),
            'description' => $this->description(),
            'credits'     => $this->credits(),
            'stripe_id'   => $this->stripeId(),
            'frequency'   => $this->frequency(),
            'benefits'    => $this->benefits(),
            'meta'        => (object) [
                'color' => $this->color(),
                'icon'  => $this->icon(),
            ],
            'price'       => (object) [
                'raw'       => $this->price(),
                'formatted' => $this->formattedPrice(),
            ],
        ];
    }

    public function title(): string
    {
        return match ($this)
        {
            self::INTRODUCTION  => trans(key: 'product.intro.title'),
            self::PACKAGE_A     => trans(key: 'product.a.title'),
            self::PACKAGE_B     => trans(key: 'product.b.title'),
            self::PACKAGE_C     => trans(key: 'product.c.title'),
        };
    }

    public function subTitle(): string
    {
        return match ($this)
        {
            self::INTRODUCTION  => trans(key: 'product.intro.subtitle'),
            self::PACKAGE_A     => trans(key: 'product.a.subtitle'),
            self::PACKAGE_B     => trans(key: 'product.b.subtitle'),
            self::PACKAGE_C     => trans(key: 'product.c.subtitle'),
        };
    }

    public function frequency(): string
    {
        return match ($this)
        {
            self::INTRODUCTION  => trans(key: 'product.intro.frequency'),
            self::PACKAGE_A     => trans(key: 'product.a.frequency'),
            self::PACKAGE_B     => trans(key: 'product.b.frequency'),
            self::PACKAGE_C     => trans(key: 'product.c.frequency'),
        };
    }

    public function description(): string
    {
        return match ($this)
        {
            self::INTRODUCTION  => trans(key: 'product.intro.description'),
            self::PACKAGE_A     => trans(key: 'product.a.description'),
            self::PACKAGE_B     => trans(key: 'product.b.description'),
            self::PACKAGE_C     => trans(key: 'product.c.description'),
        };
    }

    public function price(): int
    {
        return match ($this)
        {
            self::INTRODUCTION => 0,
            self::PACKAGE_A    => 599,
            self::PACKAGE_B    => 999,
            self::PACKAGE_C    => 1799,
        };
    }

    public function formattedPrice(): string
    {
        return Cashier::formatAmount(
            amount: $this->price()
        );
    }

    public function credits(): int
    {
        return match ($this)
        {
            self::INTRODUCTION => 2,
            self::PACKAGE_A    => 10,
            self::PACKAGE_B    => 25,
            self::PACKAGE_C    => 50,
        };
    }

    public function icon(): string
    {
        return match ($this)
        {
            self::INTRODUCTION => 'heart-handshake',
            self::PACKAGE_A    => 'traffic-cone',
            self::PACKAGE_B    => 'rocket',
            self::PACKAGE_C    => 'atom',
        };
    }

    public function color(): string
    {
        return match ($this)
        {
            self::INTRODUCTION  => 'indigo',
            self::PACKAGE_A     => 'sky',
            self::PACKAGE_B     => 'lime',
            self::PACKAGE_C     => 'emerald',
        };
    }

    public function stripeId(): ?string
    {
        if ($this === self::INTRODUCTION)
        {
            return Str::uuid()->toString();
        }

        /** @var array<int,string> $stripeIds */
        $stripeIds = config(key: 'products.stripe_id');

        /** @var null|string $stripeId */
        $stripeId = Arr::get(array: $stripeIds, key: $this->value);

        return $stripeId;
    }

    /** @return list<string> */
    public function benefits(): array
    {
        return match ($this)
        {
            self::INTRODUCTION => [
                trans(key: 'product.intro.list.1'),
                trans(key: 'product.intro.list.2'),
                trans(key: 'product.intro.list.3'),
            ],
            self::PACKAGE_A => [
                trans(key: 'product.a.list.1'),
                trans(key: 'product.a.list.2'),
                trans(key: 'product.a.list.3'),
            ],
            self::PACKAGE_B => [
                trans(key: 'product.b.list.1'),
                trans(key: 'product.b.list.2'),
                trans(key: 'product.b.list.3'),
            ],
            self::PACKAGE_C => [
                trans(key: 'product.c.list.1'),
                trans(key: 'product.c.list.2'),
                trans(key: 'product.c.list.3'),
            ]
        };
    }
}

<?php

/** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
/** @noinspection PhpExpressionResultUnusedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Enums\LanguageEnum;

it('has the correct number of variants', function (): void
{
    expect(value: LanguageEnum::cases())
        ->toHaveCount(count: 17);
});

it('assigns the correct values to cases', function (): void
{
    expect(value: LanguageEnum::EN_GB->value)
        ->toBe(expected: 1)
        ->and(value: LanguageEnum::EN_US->value)
        ->toBe(expected: 2)
        ->and(value: LanguageEnum::EN_CA->value)
        ->toBe(expected: 3)
        ->and(value: LanguageEnum::EN_AU->value)
        ->toBe(expected: 4)
        ->and(value: LanguageEnum::EN_NZ->value)
        ->toBe(expected: 5)
        ->and(value: LanguageEnum::EN_IE->value)
        ->toBe(expected: 6)
        ->and(value: LanguageEnum::EN_ZA->value)
        ->toBe(expected: 7)
        ->and(value: LanguageEnum::EN_IN->value)
        ->toBe(expected: 8)
        ->and(value: LanguageEnum::EN_SG->value)
        ->toBe(expected: 9)
        ->and(value: LanguageEnum::EN_HK->value)
        ->toBe(expected: 10)
        ->and(value: LanguageEnum::EN_PH->value)
        ->toBe(expected: 11)
        ->and(value: LanguageEnum::EN_MY->value)
        ->toBe(expected: 12)
        ->and(value: LanguageEnum::EN_NG->value)
        ->toBe(expected: 13)
        ->and(value: LanguageEnum::EN_JM->value)
        ->toBe(expected: 14)
        ->and(value: LanguageEnum::EN_KE->value)
        ->toBe(expected: 15)
        ->and(value: LanguageEnum::EN_PK->value)
        ->toBe(expected: 16)
        ->and(value: LanguageEnum::EN_GH->value)
        ->toBe(expected: 17);
});

it('returns the correct label for each variant', function (): void
{
    expect(value: LanguageEnum::EN_GB->label())
        ->toBe(expected: 'British English')
        ->and(value: LanguageEnum::EN_US->label())
        ->toBe(expected: 'American English')
        ->and(value: LanguageEnum::EN_CA->label())
        ->toBe(expected: 'Canadian English')
        ->and(value: LanguageEnum::EN_AU->label())
        ->toBe(expected: 'Australian English')
        ->and(value: LanguageEnum::EN_NZ->label())
        ->toBe(expected: 'New Zealand English')
        ->and(value: LanguageEnum::EN_IE->label())
        ->toBe(expected: 'Irish English')
        ->and(value: LanguageEnum::EN_ZA->label())
        ->toBe(expected: 'South African English')
        ->and(value: LanguageEnum::EN_IN->label())
        ->toBe(expected: 'Indian English')
        ->and(value: LanguageEnum::EN_SG->label())
        ->toBe(expected: 'Singaporean English')
        ->and(value: LanguageEnum::EN_HK->label())
        ->toBe(expected: 'Hong Kong English')
        ->and(value: LanguageEnum::EN_PH->label())
        ->toBe(expected: 'Philippine English')
        ->and(value: LanguageEnum::EN_MY->label())
        ->toBe(expected: 'Malaysian English')
        ->and(value: LanguageEnum::EN_NG->label())
        ->toBe(expected: 'Nigerian English')
        ->and(value: LanguageEnum::EN_JM->label())
        ->toBe(expected: 'Jamaican English')
        ->and(value: LanguageEnum::EN_KE->label())
        ->toBe(expected: 'Kenyan English')
        ->and(value: LanguageEnum::EN_PK->label())
        ->toBe(expected: 'Pakistani English')
        ->and(value: LanguageEnum::EN_GH->label())
        ->toBe(expected: 'Ghanaian English');
});

it('returns all languages in getLanguages method', function (): void
{
    $languages = LanguageEnum::getLanguages();

    expect(value: $languages)
        ->toBeArray()
        ->and(value: $languages)
        ->toHaveCount(count: 17)
        ->and(value: $languages[1])
        ->toBe(expected: 'British English')
        ->and(value: $languages[2])
        ->toBe(expected: 'American English')
        ->and(value: $languages[3])
        ->toBe(expected: 'Canadian English')
        ->and(value: $languages[4])
        ->toBe(expected: 'Australian English')
        ->and(value: $languages[5])
        ->toBe(expected: 'New Zealand English')
        ->and(value: $languages[6])
        ->toBe(expected: 'Irish English')
        ->and(value: $languages[7])
        ->toBe(expected: 'South African English')
        ->and(value: $languages[8])
        ->toBe(expected: 'Indian English')
        ->and(value: $languages[9])
        ->toBe(expected: 'Singaporean English')
        ->and(value: $languages[10])
        ->toBe(expected: 'Hong Kong English')
        ->and(value: $languages[11])
        ->toBe(expected: 'Philippine English')
        ->and(value: $languages[12])
        ->toBe(expected: 'Malaysian English')
        ->and(value: $languages[13])
        ->toBe(expected: 'Nigerian English')
        ->and(value: $languages[14])
        ->toBe(expected: 'Jamaican English')
        ->and(value: $languages[15])
        ->toBe(expected: 'Kenyan English')
        ->and(value: $languages[16])
        ->toBe(expected: 'Pakistani English')
        ->and(value: $languages[17])
        ->toBe(expected: 'Ghanaian English');
});

it('ensures each language has a unique label', function (): void
{
    $languages = LanguageEnum::getLanguages();
    $unique    = array_unique(array: $languages);

    expect(value: count(value: $unique))
        ->toBe(expected: count(value: $languages));
});

it('can be instantiated from value', function (): void
{
    expect(value: LanguageEnum::from(value: 1))
        ->toBe(expected: LanguageEnum::EN_GB)
        ->and(value: LanguageEnum::from(value: 8))
        ->toBe(expected: LanguageEnum::EN_IN)
        ->and(value: LanguageEnum::from(value: 17))
        ->toBe(expected: LanguageEnum::EN_GH);
});

it('throws exception when instantiated with invalid value', function (): void
{
    LanguageEnum::from(value: 18);
})->throws(exception: ValueError::class);

it('can try to get enum case from value', function (): void
{
    expect(value: LanguageEnum::tryFrom(value: 7))
        ->toBe(expected: LanguageEnum::EN_ZA)
        ->and(value: LanguageEnum::tryFrom(value: 100))
        ->toBeNull();
});

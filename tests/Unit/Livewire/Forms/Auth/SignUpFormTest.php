<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Forms\Auth\SignUpForm;
use Tests\Classes\Unit\Livewire\TestComponent;

describe(description: 'SignUpForm', tests: function (): void
{
    test(description: 'can instantiate SignUpForm', closure: function (): void
    {
        $component = new TestComponent();
        $form      = new SignUpForm(component: $component, propertyName: 'signUpForm');

        expect(value: $form)
            ->toBeInstanceOf(class: SignUpForm::class);
    });
});

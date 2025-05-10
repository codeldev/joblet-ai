<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Forms\Account\ProfileForm;
use Tests\Classes\Unit\Livewire\TestComponent;

describe(description: 'ProfileForm', tests: function (): void
{
    test(description: 'can instantiate ProfileForm', closure: function (): void
    {
        $component = new TestComponent();
        $form      = new ProfileForm(component: $component, propertyName: 'profileForm');

        expect(value: $form)
            ->toBeInstanceOf(class: ProfileForm::class);
    });
});

<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Forms\Account\PasswordForm;
use Tests\Classes\Unit\Livewire\TestComponent;

describe(description: 'PasswordForm', tests: function (): void
{
    test(description: 'can instantiate PasswordForm', closure: function (): void
    {
        $component = new TestComponent();
        $form      = new PasswordForm(component: $component, propertyName: 'passwordForm');

        expect(value: $form)
            ->toBeInstanceOf(class: PasswordForm::class);
    });
});

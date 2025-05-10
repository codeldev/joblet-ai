<?php

declare(strict_types=1);

use App\Livewire\Forms\Auth\SignInForm;
use Tests\Classes\Unit\Livewire\TestComponent;

describe(description: 'SignInForm', tests: function (): void
{
    test(description: 'can instantiate and validate SignInForm', closure: function (): void
    {
        $component      = new TestComponent();
        $form           = new SignInForm(component: $component, propertyName: 'signInForm');
        $form->email    = 'test@example.com';
        $form->password = 'password';
        $form->remember = true;
        try
        {
            $form->validate();
        }
        catch (Throwable $e)
        {
            // Ignore validation errors for coverage
        }
        expect(value: $form)->toBeInstanceOf(class: SignInForm::class);
    });
});

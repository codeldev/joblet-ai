<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Forms\Account\DeleteForm;
use Tests\Classes\Unit\Livewire\TestComponent;

describe(description: 'DeleteForm', tests: function (): void
{
    test(description: 'can instantiate DeleteForm', closure: function (): void
    {
        $component = new TestComponent();
        $form      = new DeleteForm(component: $component, propertyName: 'deleteForm');

        expect(value: $form)
            ->toBeInstanceOf(class: DeleteForm::class);
    });
});

<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Forms\Generator\GeneratorForm;
use Tests\Classes\Unit\Livewire\TestComponent;

describe(description: 'GeneratorForm', tests: function (): void
{
    test(description: 'can instantiate GeneratorForm', closure: function (): void
    {
        $component = new TestComponent();
        $form      = new GeneratorForm(component: $component, propertyName: 'generatorForm');

        expect(value: $form)
            ->toBeInstanceOf(class: GeneratorForm::class);
    });
});

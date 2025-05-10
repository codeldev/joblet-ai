<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use App\Livewire\Account\Delete;
use Livewire\Livewire;

describe(description: 'Account/Delete Validation', tests: function (): void
{
    test(description: 'it fails if password is empty', closure: function (): void
    {
        Livewire::test(name: Delete::class)
            ->set(name: 'form.password', value: '')
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.password' => 'required']);
    });

    test(description: 'it fails if password is not the current password', closure: function (): void
    {
        Livewire::test(name: Delete::class)
            ->set(name: 'form.password', value: 'incorrect-password')
            ->call(method: 'submit')
            ->assertHasErrors(keys: ['form.password' => 'current_password']);
    });
});

<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Account;

use Livewire\Attributes\Validate;
use Livewire\Form;

final class DeleteForm extends Form
{
    #[Validate(['required', 'string', 'current_password'])]
    public string $password = '';
}

<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Account;

use Livewire\Attributes\Validate;
use Livewire\Form;

final class PasswordForm extends Form
{
    #[Validate(['required', 'string', 'same:confirmed'])]
    public string $password = '';

    #[Validate(['required', 'string', 'same:password'])]
    public string $confirmed = '';
}

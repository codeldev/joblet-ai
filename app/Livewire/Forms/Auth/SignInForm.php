<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Auth;

use Livewire\Attributes\Validate;
use Livewire\Form;

final class SignInForm extends Form
{
    #[Validate(['required', 'string', 'lowercase', 'email:rfc,dns,strict,spoof'])]
    public ?string $email = null;

    #[Validate(['required', 'string'])]
    public ?string $password = null;

    #[Validate(['required', 'boolean'])]
    public bool $remember = true;

    public function clearForm(): void
    {
        $this->reset();
        $this->clearValidation();
    }
}

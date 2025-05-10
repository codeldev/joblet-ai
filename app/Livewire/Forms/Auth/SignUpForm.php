<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Auth;

use Livewire\Attributes\Validate;
use Livewire\Form;

final class SignUpForm extends Form
{
    #[Validate(['required', 'string', 'min:5', 'max:255'])]
    public ?string $name = null;

    #[Validate(['required', 'string', 'lowercase', 'email:rfc,dns,strict,spoof', 'max:255', 'unique:users,email'])]
    public ?string $email = null;

    #[Validate(['required', 'string'])]
    public ?string $password = null;

    #[Validate(['required', 'boolean', 'accepted'])]
    public bool $agreed = false;

    public function clearForm(): void
    {
        $this->reset();
        $this->clearValidation();
    }
}

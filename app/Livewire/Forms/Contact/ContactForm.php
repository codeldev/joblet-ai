<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Contact;

use Livewire\Attributes\Validate;
use Livewire\Form;

final class ContactForm extends Form
{
    #[Validate(['required', 'string', 'min:5', 'max:255'])]
    public ?string $name = null;

    #[Validate(['required', 'string', 'lowercase', 'email:rfc,dns,strict,spoof', 'max:255'])]
    public ?string $email = null;

    #[Validate(['required', 'string', 'min:20', 'max:5000'])]
    public ?string $message = null;
}

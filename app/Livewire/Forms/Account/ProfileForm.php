<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Account;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

final class ProfileForm extends Form
{
    public ?string $name = null;

    public ?string $email = null;

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'name'  => [
                'required', 'string', 'min:5', 'max:255',
            ],
            'email' => [
                'required', 'string', 'lowercase', 'email:rfc,dns,strict,spoof', 'max:255',
                Rule::unique(table: User::class)->ignore(id: auth()->id()),
            ],
        ];
    }
}

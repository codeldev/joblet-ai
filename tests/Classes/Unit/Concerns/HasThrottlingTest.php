<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Concerns;

use App\Concerns\HasThrottlingTrait;
use Livewire\Component;

final class HasThrottlingTest extends Component
{
    use HasThrottlingTrait;

    public ?object $form = null;

    public function mount(): void
    {
        $this->form = (object) ['email' => 'test@example.com'];

        $this->setupProperties(
            keyPrefix : 'test',
            redirect  : 'auth'
        );
    }
}

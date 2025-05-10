<?php

declare(strict_types=1);

namespace Tests\Classes\Unit\Concerns;

use App\Concerns\HasNotificationsTrait;
use Livewire\Component;

final class HasNotificationsTest extends Component
{
    use HasNotificationsTrait;

    public function render(): string
    {
        return <<<'HTML'
        <div></div>
        HTML;
    }
}

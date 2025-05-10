<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Enums\ProductPackageEnum;
use App\Services\Models\UserService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

final class Credits extends Component
{
    public ?int $packageId = null;

    #[On('reload-credits-panel')]
    public function render(): View
    {
        return view(view: 'livewire.account.credits');
    }

    #[Computed]
    public function credits(): int
    {
        return UserService::getRemainingCredits();
    }

    /** @return Collection<int, object> */
    #[Computed]
    public function packages(): Collection
    {
        return ProductPackageEnum::getAll();
    }

    public function updated(): void
    {
        /** @var int $packageId */
        $packageId = $this->packageId;

        $this->reset('packageId');

        /** @var object $modal */
        $modal = Flux::modal(name: 'order-credits');

        if (method_exists(object_or_class: $modal, method: 'close'))
        {
            $modal->close();
        }

        $this->dispatch('send-for-payment', $packageId);
    }

    #[On('send-for-payment')]
    public function sendForPayment(int $packageId): void
    {
        $this->redirectRoute(name: 'payment.request', parameters: [
            'gateway' => 'stripe',
            'package' => $packageId,
        ]);
    }
}

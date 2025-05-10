<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Concerns\HasNotificationsTrait;
use App\Contracts\Services\Invoice\InvoiceServiceInterface;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** @phpstan-type OrderPaginator LengthAwarePaginator<int, Order> */
final class Orders extends Component
{
    use HasNotificationsTrait;

    public function render(): View
    {
        return view(view: 'livewire.account.orders');
    }

    /** @phpstan-return OrderPaginator */
    #[Computed]
    public function orders(): LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->orders()->paginate(perPage: 5);
    }

    /** @throws BindingResolutionException */
    public function downloadPdf(Order $order): ?BinaryFileResponse
    {
        if (! Gate::allows(ability: 'view', arguments: $order))
        {
            $this->notifyError(
                message: trans(key: 'misc.action.disallowed')
            );

            return null;
        }

        if ($order->free)
        {
            $this->notifyError(
                message: trans(key: 'invoice.free.error')
            );

            return null;
        }

        return $this->downloadInvoicePdf(order: $order);
    }

    /**
     * @throws BindingResolutionException
     */
    private function downloadInvoicePdf(Order $order): ?BinaryFileResponse
    {
        /** @var InvoiceServiceInterface $service */
        $service = app()->make(
            abstract  : InvoiceServiceInterface::class,
            parameters: ['order' => $order]
        );

        $service->buildDownload();

        /** @var string|null $error */
        $error = $service->getError();
        if (notEmpty(value: $error))
        {
            $errorMessage = (string) ($error ?? trans(key: 'errors.technical'));

            $this->notifyError(message: $errorMessage);

            return null;
        }

        return $service->getPdf()->download();
    }
}

@props(['order', 'payment'])

<div class="uppercase w-full rounded-lg bg-lime-600/70 text-white text-xl text-center py-3.5">
    <span class="font-light">
        {{ trans('invoice.invoice') }}
    </span>
    <span class="font-bold">
        #{{ $payment['invoice'] }}
    </span>
</div>
<div class="space-y-5 border border-neutral-400/50 rounded-lg p-3.5">
    <x-invoice.panel
        icon="shopping-cart"
        :label="trans('invoice.reference.order')"
        :text="$order['id']"
    />
    <x-invoice.panel
        icon="credit-card"
        :label="trans('invoice.reference.payment')"
        :text="$payment['id']"
    />
    <x-invoice.panel
        icon="calendar-check-2"
        :label="trans('invoice.reference.date')"
        :text="$order['date']"
    />
</div>

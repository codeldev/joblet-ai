@props(['order', 'payment'])
<div class="rounded-lg p-5 border border-neutral-400/50 space-y-3">
    <div class="flex justify-between items-center gap-6 font-semibold text-sm">
        <div clas="flex-1">
            {{ trans('invoice.items.description') }}
        </div>
        <div class="w-20 flex-none text-right">
            {{ trans('invoice.items.price') }}
        </div>
    </div>
    <div class="h-px bg-neutral-400/50"></div>
    <div class="flex justify-between items-center gap-6 text-sm">
        <div clas="flex-1">
            {{ $order['package'] }} - {{ $order['description'] }}
        </div>
        <div class="w-20 flex-none text-right">
            {{ $payment['amount'] }}
        </div>
    </div>
    <div class="h-px bg-neutral-400/50"></div>
    <div class="flex justify-between items-center gap-6 text-sm">
        <div class="flex-1 flex items-center justify-end font-semibold">
            {{ trans('invoice.items.total') }}
        </div>
        <div class="w-20 flex-none font-semibold text-right">
            {{ $payment['amount'] }}
        </div>
    </div>
    <div class="h-px bg-neutral-400/50"></div>
    <div class="flex justify-between items-center gap-6 text-sm">
        <div class="flex-1 flex items-center justify-end gap-2">
            ({{ $payment['type'] }})
            <span class=" font-semibold">
                {{ trans('invoice.items.paid') }}
            </span>
        </div>
        <div class="w-20 flex-none text-lime-600 font-semibold text-right">
            {{ $payment['amount'] }}
        </div>
    </div>
</div>

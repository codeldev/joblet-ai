<div>
    @switch(true)
        @case($paymentError)
            <x-payment-states.error :$paymentError />
        @break
        @case($paymentCancelled)
            <x-payment-states.cancelled />
        @break
        @case($paymentSuccess)
            <x-payment-states.success />
        @break
        @case($paymentComplete)
            <x-payment-states.verified />
        @break
        @default
            <livewire:account.credits />
        @break
    @endswitch
</div>

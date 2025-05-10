<flux:callout
    color="emerald"
    icon="loading"
    :heading="trans('payment.state.processing.title')"
    :text="trans('payment.state.processing.text')"
    wire:poll.2s="validatePayment"
/>

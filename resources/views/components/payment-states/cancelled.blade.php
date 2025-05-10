<flux:callout
    color="sky"
    icon="bell-ring"
    :heading="trans('payment.state.cancelled.title')"
    :text="trans('payment.state.cancelled.text')"
    x-init="setTimeout(() => $dispatch('reload-payment'), 3500)"
/>

<flux:callout
    color="emerald"
    icon="sparkles"
    :heading="trans('payment.state.verified.title')"
    :text="trans('payment.state.verified.text')"
    x-init="setTimeout(window.showConfetti(), 1000); setTimeout(() => $dispatch('reload-payment'), 3500)"
/>

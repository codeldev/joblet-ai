@props(['paymentError'])
<flux:callout
    variant="danger"
    icon="triangle-alert"
    :heading="trans('payment.state.error.title')"
    :text="trans($paymentError)"
    x-init="setTimeout(() => $dispatch('reload-payment'), 3500)"
/>

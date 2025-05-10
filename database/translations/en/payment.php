<?php

declare(strict_types=1);

return [
    'state.error.title'             => 'Payment Error',
    'state.cancelled.title'         => 'Payment Cancelled',
    'state.cancelled.text'          => 'Aww, you cancelled your payment. No hard feelings!',
    'state.processing.title'        => 'Thanks for your order',
    'state.processing.text'         => 'We’re just validating the payment. This won’t take long!',
    'state.verified.title'          => 'Payment verified',
    'state.verified.text'           => 'Payment validated! Your package has been applied.',
    // Webhook messages
    'webhook.error.order.failed'    => 'Order creation failed',
    'webhook.error.payload.invalid' => 'Invalid payload',
    'webhook.error.payload.type'    => 'invalid payload type',
    'webhook.success.order.payment' => 'Order and payment creation successful',
];

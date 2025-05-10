<?php

declare(strict_types=1);

return [
    // RESUME UPLOADS
    'upload.resume.empty'    => 'Your file is unreadable or empty. Please try again.',
    // PAYMENT GATEWAYS
    'payment.gateway'        => 'Invalid payment gateway',
    'payment.url'            => 'Invalid payment url',
    'package.invalid'        => 'Invalid product package',
    // STRIPE PAYMENTS
    'stripe.invalid.charge'  => 'Invalid charge data',
    'stripe.invalid.event'   => 'Invalid Event ID',
    'stripe.invalid.intent'  => 'Invalid payment intent',
    'stripe.invalid.token'   => 'Invalid payment token',
    'stripe.invalid.user'    => 'Invalid User',
    'stripe.charge.failed'   => 'Charge failed',
    // BACKUPS
    'backups.config.invalid' => 'Invalid configuration key: backups',
    'backups.config.missing' => 'Missing Backup configuration file',
    'backups.google.id'      => 'Google Drive client ID is missing or invalid',
    'backups.google.secret'  => 'Google Drive client secret is missing or invalid',
    'backups.google.token'   => 'Google Drive refresh token is missing or invalid',
    // EMAIL
    'email.subject'          => 'An Exception occurred on :app',
    'email.intro'            => 'A critical exception error occurred:',
    'email.url'              => '**URL:** :url',
    'email.ip'               => '**IP:** :ip',
    'email.user'             => '**User:** :user',
    'email.error'            => '**Error:** :message',
    'email.file'             => '**File:** :file',
    'email.line'             => '**Line:** :line',
    'email.trace.start'      => '**Trace:** :trace',
    'email.trace.line'       => 'at :class->:function() in :file on line :line',
    'email.trace.end'        => '**End Trace**',
    'email.trace.none'       => '**Stack Trace:** Unavailable',
];

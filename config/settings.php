<?php

declare(strict_types=1);

return [
    'contact'  => env(key: 'CONTACT_EMAIL'),
    'pan'      => env(key: 'PAN_ALLOWABLE', default: ''),
    'fathom'   => env(key:  'FATHOM_CODE'),
    'invoices' => [
        'initial'    => env(key: 'INVOICE_INITIAL', default: 12701),
        'padding'    => env(key: 'INVOICE_PADDING', default: 10),
        'address'    => env(key: 'INVOICE_ADDRESS', default: ''),
        'descriptor' => env(key: 'INVOICE_DESCRIPTOR', default: ''),
    ],
    'exceptions' => [
        'send_mail' => env(key: 'EXCEPTION_MAIL_SEND', default: false),
    ],
];

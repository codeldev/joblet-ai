<?php

declare(strict_types=1);

return [
    'created.success'             => 'Backup created successfully.',
    'missing.config'              => 'Missing configuration key: backups',
    'cleanup.success'             => 'Backups have been cleaned.',
    'unknown.error'               => 'An unknown error occurred.',
    'email.create.failed.subject' => 'Backup Generation failed for :app',
    'email.create.failed.line'    => 'The backup generation process for :app failed with the following error:',
    'email.clean.failed.subject'  => 'Backup Cleanup failed for :app',
    'email.clean.failed.line'     => 'The backup cleaner process for :app failed with the following error:',
];

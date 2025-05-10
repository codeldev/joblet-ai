<?php

declare(strict_types=1);

namespace App\Contracts\Actions\Contact;

interface SendFeedbackActionInterface
{
    /**
     * @param  array<string, string>  $validated
     */
    public function handle(
        array $validated,
        callable $success,
        callable $failed
    ): void;
}

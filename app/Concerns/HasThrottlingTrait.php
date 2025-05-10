<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

trait HasThrottlingTrait
{
    public ?string $lockoutMessage = null;

    public ?string $lockoutRedirect = null;

    public ?string $keyPrefix = null;

    public function setupProperties(string $keyPrefix, string $redirect): void
    {
        $this->lockoutRedirect = $redirect;
        $this->keyPrefix       = $keyPrefix;
    }

    public function checkLockedOutOnMount(): void
    {
        if ($this->isLockedOut())
        {
            $this->setLockoutMessage();
        }
    }

    public function ensureIsNotRateLimited(): void
    {
        if ($this->isLockedOut())
        {
            event(new Lockout(request: request()));

            if ($this->lockoutRedirect)
            {
                $this->redirectRoute(name: $this->lockoutRedirect);
            }
        }
    }

    /** @return list<string> */
    public function throttleKeys(): array
    {
        $sessionId = Session::getId();
        $ipAddress = request()->ip();
        $prefix    = $this->keyPrefix ?? '';
        $email     = $this->getEmail();
        $emailHash = $email && is_string(value: $email)
            ? mb_substr(md5(string: Str::lower(value: $email)), 0, 8)
            : mb_substr(md5(string: $sessionId), 0, 8);

        return [
            Str::transliterate(string: "{$prefix}|{$ipAddress}|{$sessionId}"),
            Str::transliterate(string: "{$prefix}|{$ipAddress}|{$emailHash}"),
        ];
    }

    public function addLimiterHit(?int $decayTime = null): void
    {
        $decayTime = $this->getDecayTime(decayTime: $decayTime);

        foreach ($this->throttleKeys() as $throttleKey)
        {
            RateLimiter::hit(
                key          : $throttleKey,
                decaySeconds : $decayTime,
            );
        }
    }

    public function setLockoutMessage(?string $message = null): void
    {
        $duration = $this->getLockoutDuration();
        $minutes  = floor(num: $duration / 60);
        $seconds  = $duration % 60;

        $this->lockoutMessage = trans(key: ($message ?? 'auth.lockout.message'), replace: [
            'minutes' => pluralize(amount: $minutes, word: trans(key: 'misc.word.minute')),
            'seconds' => pluralize(amount: $seconds, word: trans(key: 'misc.word.second')),
        ]);
    }

    public function isLockedOut(): bool
    {
        $maxAttempts = $this->getMaxAttempts();

        return array_any(
            array: $this->throttleKeys(),
            callback: static fn ($throttleKey) => RateLimiter::tooManyAttempts(
                key        : $throttleKey,
                maxAttempts: $maxAttempts,
            )
        );
    }

    public function getLockoutDuration(): int
    {
        $maxDuration = 0;

        foreach ($this->throttleKeys() as $throttleKey)
        {
            if ($seconds = RateLimiter::availableIn(key: $throttleKey))
            {
                $maxDuration = max($maxDuration, $seconds);
            }
        }

        return $maxDuration;
    }

    public function clearRateLimits(): void
    {
        collect(value: $this->throttleKeys())
            ->each(callback: fn (string $throttleKey) => RateLimiter::clear(key: $throttleKey));
    }

    public function getDecayTime(?int $decayTime): int
    {
        $decay = is_null(value: $decayTime)
            ? config(key: 'auth.passwords.users.throttle', default: 60)
            : $decayTime;

        return is_numeric(value: $decay)
            ? (int) $decay
            : 60;
    }

    private function getEmail(): ?string
    {
        if (! property_exists(object_or_class: $this, property: 'form'))
        {
            return null;
        }

        if (! is_object(value: $this->form))
        {
            return null;
        }

        if (! property_exists(object_or_class: $this->form, property: 'email'))
        {
            return null;
        }

        return $this->form->email;
    }

    private function getMaxAttempts(): int
    {
        $attempts = config(key: 'auth.passwords.users.attempts', default: 5);

        return is_numeric(value: $attempts)
            ? (int) $attempts
            : 5;
    }
}
